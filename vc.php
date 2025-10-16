<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>TechMed Video Consultation</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-auth-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-firestore-compat.js"></script>
  <script src="./firebase-init.js"></script>
  <link rel="stylesheet" href="vc.css">
</head>
<body>
  <header>TechMed Video Consultation</header>
  <div id="utility">👥 Participants: <span id="participantCount">1</span> • <span id="connStatus" class="status-dot" title="Not connected"></span></div>

  <div id="video-area">
    <video id="remoteVideo" autoplay playsinline></video>
    <video id="localVideo" autoplay playsinline muted></video>
    <!-- Name labels over videos -->
    <div id="remoteLabel" class="video-label" aria-hidden="true">Waiting for participant…</div>
    <div id="localLabel" class="video-label" aria-hidden="true">You</div>
  </div>

  <div id="controls">
    <button id="micBtn" class="btn" title="Toggle Microphone">🎤</button>
    <button id="camBtn" class="btn" title="Toggle Camera">🎥</button>
    <button id="screenBtn" class="btn" title="Share Screen">🖥️</button>
    <button id="membersBtn" class="btn" title="Members">👤</button>

    <button id="fullscreenBtn" class="btn" title="Fullscreen">⛶</button>
    <button id="leaveBtn" class="btn btn-red" title="Leave">❌</button>
  </div>

  <div id="room-info">
    <button id="createBtn" class="btn" title="Create Room">➕</button>
    <input id="roomIdInput" placeholder="Enter Room ID" />
    <button id="joinBtn" class="btn" title="Join Room">➡️</button>
    <button id="copyBtn" class="btn" style="display:none;" title="Copy Room ID">🔗</button>
  </div>

  <!-- Members panel -->
  <div id="membersPanel" class="hidden" style="position:fixed; top:64px; right:16px; width:240px; max-height:50vh; overflow:auto; background:#fff; border:1px solid #e5e7eb; border-radius:8px; padding:12px; box-shadow:0 6px 24px rgba(0,0,0,0.12); z-index:1000;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
      <strong>Members</strong>
      <button id="membersClose" class="btn btn-sm" title="Close">✕</button>
    </div>
    <ul id="membersList" style="list-style:none; padding-left:0; margin:0;"></ul>
  </div>

  <!-- Overlay for status/loading -->
  <div id="overlay" class="hidden">
    <div class="spinner" aria-hidden="true"></div>
    <div id="overlayText">Setting up your camera…</div>
  </div>

  <!-- Toasts -->
  <div id="toast" aria-live="polite"></div>

  <script>
    // Use existing Firebase app/init from firebase-init.js
    const db = window.db || firebase.firestore();
    const auth = firebase.auth();

    let localStream, remoteStream, peerConnection, screenStream;
    const servers = { iceServers: [{ urls: ["stun:stun.l.google.com:19302"] }] };

    const localVideo = document.getElementById("localVideo");
    const remoteVideo = document.getElementById("remoteVideo");
    const participantCount = document.getElementById("participantCount");
    const roomInfo = document.getElementById("room-info");
    const controls = document.getElementById("controls");

    const overlay = document.getElementById('overlay');
    const overlayText = document.getElementById('overlayText');
    const connStatus = document.getElementById('connStatus');

    // Video name labels elements and state
    const videoArea = document.getElementById('video-area');
    const localLabelEl = document.getElementById('localLabel');
    const remoteLabelEl = document.getElementById('remoteLabel');
    let selfMemberId = '';
    let selfDisplayName = '';

    function setLocalLabelName(name) {
      selfDisplayName = name || selfDisplayName || 'You';
      try { localLabelEl.textContent = selfDisplayName; } catch (_) {}
    }
    function setRemoteLabelName(name) {
      try { remoteLabelEl.textContent = name || 'Participant'; } catch (_) {}
    }
    function updateLabelFor(videoEl, labelEl) {
      if (!videoEl || !labelEl || !videoArea) return;
      const areaRect = videoArea.getBoundingClientRect();
      const rect = videoEl.getBoundingClientRect();
      if (!rect.width || !rect.height) { labelEl.style.opacity = '0'; return; }
      const left = Math.round(rect.left - areaRect.left + 8);
      const top = Math.round(rect.top - areaRect.top + rect.height - labelEl.offsetHeight - 8);
      labelEl.style.left = left + 'px';
      labelEl.style.top = top + 'px';
      labelEl.style.opacity = '1';
    }
    function updateLabelsPosition() {
      updateLabelFor(remoteVideo, remoteLabelEl);
      updateLabelFor(localVideo, localLabelEl);
    }

    function stopStream(stream) {
      if (!stream) return;
      stream.getTracks().forEach(t => t.stop());
    }

    async function leavePresence() {
      try { if (presenceDocRef) await presenceDocRef.set({ active:false, leftAt: firebase.firestore.FieldValue.serverTimestamp() }, { merge:true }); } catch(_) {}
    }

    function cleanup() {
      // Mark presence left
      leavePresence();
      if (peerConnection) {
        peerConnection.ontrack = null;
        peerConnection.onicecandidate = null;
        try { peerConnection.close(); } catch (_) {}
      }
      stopStream(localStream);
      stopStream(screenStream);
      if (remoteVideo.srcObject) {
        try { remoteVideo.srcObject.getTracks().forEach(t => t.stop()); } catch (_) {}
      }
      localStream = null; remoteStream = null; peerConnection = null; screenStream = null;
      participantCount.textContent = '1';
      setStatus('disconnected');
      updateLabelsPosition();
    }

    async function ensureLocalStreams() {
      if (!localStream) {
        localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
        localVideo.srcObject = localStream;
      }
      if (!remoteStream) {
        remoteStream = new MediaStream();
        remoteVideo.srcObject = remoteStream;
      }
      // position labels after streams are bound
      setTimeout(updateLabelsPosition, 0);
    }

    function showToast(message) {
      const toast = document.getElementById('toast');
      toast.textContent = message;
      toast.classList.add('show');
      clearTimeout(showToast._t);
      showToast._t = setTimeout(() => toast.classList.remove('show'), 2200);
    }

    function showOverlay(text) {
      if (text) overlayText.textContent = text;
      overlay.classList.remove('hidden');
    }
    function hideOverlay() { overlay.classList.add('hidden'); }

    function setStatus(state) {
      connStatus.classList.remove('status-connected','status-connecting','status-disconnected');
      if (state === 'connected') connStatus.classList.add('status-connected');
      else if (state === 'connecting') connStatus.classList.add('status-connecting');
      else connStatus.classList.add('status-disconnected');
    }

    async function createPeer() {
      peerConnection = new RTCPeerConnection(servers);
      localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));
      peerConnection.ontrack = e => {
        e.streams[0].getTracks().forEach(t => remoteStream.addTrack(t));
        hideOverlay();
        setStatus('connected');
        updateLabelsPosition();
      };
      peerConnection.onconnectionstatechange = () => {
        if (peerConnection.connectionState === 'connected') participantCount.textContent = '2';
        if ([ 'disconnected','failed','closed' ].includes(peerConnection.connectionState)) participantCount.textContent = '1';
        setStatus(
          peerConnection.connectionState === 'connected' ? 'connected' :
          peerConnection.connectionState === 'connecting' ? 'connecting' : 'disconnected'
        );
        updateLabelsPosition();
      };
      return peerConnection;
    }

    // Presence and members
    let currentRoomId = '';
    let presenceDocRef = null;
    let membersUnsub = null;

    function getDisplayName(as) {
      const u = auth.currentUser;
      return (u && (u.displayName || u.email)) || (as === 'doctor' ? 'Doctor' : 'Patient');
    }

    async function enterPresence(roomId, role) {
      currentRoomId = roomId;
      // Pick stable id for presence
      let pid = auth.currentUser?.uid || localStorage.getItem('techmed_member_id');
      if (!pid) { pid = 'anon_' + Math.random().toString(36).slice(2, 10); localStorage.setItem('techmed_member_id', pid); }
      const name = getDisplayName(role);
      presenceDocRef = db.collection('calls').doc(roomId).collection('members').doc(pid);
      await presenceDocRef.set({
        uid: auth.currentUser?.uid || null,
        name,
        role: role || 'guest',
        active: true,
        updatedAt: firebase.firestore.FieldValue.serverTimestamp(),
        joinedAt: firebase.firestore.FieldValue.serverTimestamp(),
      }, { merge: true });
      // Update local label details
      selfMemberId = pid;
      setLocalLabelName(name);
      updateLabelsPosition();
    }

    function listenMembers(roomId) {
      if (membersUnsub) { try { membersUnsub(); } catch(_) {} membersUnsub = null; }
      const listEl = document.getElementById('membersList');
      membersUnsub = db.collection('calls').doc(roomId).collection('members').onSnapshot((snap) => {
        const members = [];
        snap.forEach(d => { const m = d.data() || {}; if (m.active !== false) members.push({ ...m, _id: d.id }); });
        if (listEl) {
          listEl.innerHTML = '';
          members.forEach(m => {
            const li = document.createElement('li');
            li.textContent = `${m.name || 'Guest'}${m.role ? ' • ' + m.role : ''}`;
            listEl.appendChild(li);
          });
        }
        // Determine remote participant's name (first active member that's not me)
        const remote = members.find(m => m._id !== selfMemberId);
        if (remote) {
          setRemoteLabelName(remote.name || (remote.role === 'doctor' ? 'Doctor' : 'Patient'));
        } else {
          setRemoteLabelName('Waiting for participant…');
        }
        try { participantCount.textContent = String(Math.max(1, members.length)); } catch(_) {}
        updateLabelsPosition();
      });
    }

    async function createInRoom(roomId) {
      document.getElementById("roomIdInput").value = roomId;
      const callDoc = db.collection("calls").doc(roomId);
      const offerCandidates = callDoc.collection("offerCandidates");
      const answerCandidates = callDoc.collection("answerCandidates");

      const copyBtn = document.getElementById("copyBtn");
      copyBtn.style.display = "inline-block";
      copyBtn.onclick = () => {
        const link = `${location.origin}${location.pathname}?room=${encodeURIComponent(roomId)}`;
        if (navigator.share) {
          navigator.share({ title: 'TechMed Video Consultation', text: 'Join my meeting', url: link }).catch(() => {});
        } else {
          navigator.clipboard.writeText(link).then(() => showToast('Link copied'));
        }
      };

      await ensureLocalStreams();
      await createPeer();
      peerConnection.onicecandidate = e => { if (e.candidate) offerCandidates.add(e.candidate.toJSON()); };
      setStatus('connecting');
      showOverlay('Waiting for someone to join…');

      try {
        const offerDescription = await peerConnection.createOffer();
        await peerConnection.setLocalDescription(offerDescription);
        await callDoc.set({ offer: { type: offerDescription.type, sdp: offerDescription.sdp }, createdAt: Date.now() }, { merge: true });
      } catch (e) {
        console.error('Failed to create offer', e);
        showToast('Failed to start room');
        hideOverlay();
        return;
      }

      callDoc.onSnapshot(snapshot => {
        const data = snapshot.data();
        if (data?.answer && !peerConnection.currentRemoteDescription) {
          peerConnection.setRemoteDescription(new RTCSessionDescription(data.answer));
          participantCount.textContent = '2';
          updateLabelsPosition();
        }
      });

      answerCandidates.onSnapshot(snapshot => snapshot.docChanges().forEach(change => {
        if (change.type === "added") {
          peerConnection.addIceCandidate(new RTCIceCandidate(change.doc.data()));
        }
      }));

      roomInfo.classList.add("slide-away");
      // Presence & members
      await enterPresence(roomId, 'doctor');
      listenMembers(roomId);
    }

    async function joinInRoom(roomId) {
      document.getElementById("roomIdInput").value = roomId;
      const callDoc = db.collection("calls").doc(roomId);
      const offerCandidates = callDoc.collection("offerCandidates");
      const answerCandidates = callDoc.collection("answerCandidates");

      await ensureLocalStreams();
      await createPeer();
      // Send our ICE candidates as the answerer
      peerConnection.onicecandidate = e => { if (e.candidate) answerCandidates.add(e.candidate.toJSON()); };
      // Listen for remote ICE from the doctor immediately
      offerCandidates.onSnapshot(snapshot => snapshot.docChanges().forEach(change => {
        if (change.type === "added") {
          try { peerConnection.addIceCandidate(new RTCIceCandidate(change.doc.data())); } catch(_) {}
        }
      }));
      setStatus('connecting');
      showOverlay('Waiting for doctor to start…');

      // Watch the call doc until an offer appears, then answer
      let answered = false;
      const unsub = callDoc.onSnapshot(async (snap) => {
        const data = snap.exists ? (snap.data() || {}) : null;
        if (!data || !data.offer) {
          // still waiting for the doctor to start the room
          return;
        }
        if (answered) return;
        try {
          await peerConnection.setRemoteDescription(new RTCSessionDescription(data.offer));
          const answerDescription = await peerConnection.createAnswer();
          await peerConnection.setLocalDescription(answerDescription);
          await callDoc.set({ answer: { type: answerDescription.type, sdp: answerDescription.sdp }, answeredAt: Date.now() }, { merge: true });
          roomInfo.classList.add("slide-away");
          participantCount.textContent = '2';
          hideOverlay();
          updateLabelsPosition();
          answered = true;
          // Keep offerCandidates listener for additional ICE; optionally stop listening to main doc
          // unsub && unsub();
        } catch (e) {
          console.error('Failed to accept offer', e);
          showToast('Failed to join room');
        }
      });
      // Presence & members
      await enterPresence(roomId, 'patient');
      listenMembers(roomId);
    }

    document.getElementById("createBtn").onclick = async () => {
      const explicit = (document.getElementById("roomIdInput").value || '').trim();
      const roomId = explicit || `doc_${Math.random().toString(36).slice(2, 10)}`;
      await createInRoom(roomId);
    };

    document.getElementById("joinBtn").onclick = async () => {
      const callId = document.getElementById("roomIdInput").value.trim();
      if (!callId) { showToast('Enter Room ID'); return; }
      await joinInRoom(callId);
    };

    // Controls
    document.getElementById("micBtn").onclick = () => {
      if (localStream) {
        const track = localStream.getAudioTracks()[0];
        if (!track) return;
        track.enabled = !track.enabled;
        const btn = document.getElementById("micBtn");
        btn.textContent = track.enabled ? "🎤" : "🔇";
        btn.classList.toggle('btn-off', !track.enabled);
        btn.setAttribute('aria-pressed', String(!track.enabled));
        showToast(track.enabled ? 'Mic on' : 'Mic off');
      }
    };
    document.getElementById("camBtn").onclick = () => {
      if (localStream) {
        const track = localStream.getVideoTracks()[0];
        if (!track) return;
        track.enabled = !track.enabled;
        const btn = document.getElementById("camBtn");
        btn.textContent = track.enabled ? "🎥" : "📷";
        btn.classList.toggle('btn-off', !track.enabled);
        btn.setAttribute('aria-pressed', String(!track.enabled));
        showToast(track.enabled ? 'Camera on' : 'Camera off');
      }
    };
    document.getElementById("leaveBtn").onclick = () => { cleanup(); location.reload(); };
    document.getElementById("fullscreenBtn").onclick = () => {
      if (!document.fullscreenElement) document.documentElement.requestFullscreen();
      else document.exitFullscreen();
    };

    // Screen share
    document.getElementById('screenBtn').onclick = async () => {
      if (!peerConnection) return;
      if (!screenStream) {
        try {
          screenStream = await navigator.mediaDevices.getDisplayMedia({ video: true });
          const screenTrack = screenStream.getVideoTracks()[0];
          const sender = peerConnection.getSenders().find(s => s.track && s.track.kind === 'video');
          if (sender && screenTrack) { await sender.replaceTrack(screenTrack); }
          screenTrack.onended = async () => {
            const camTrack = localStream.getVideoTracks()[0];
            if (sender && camTrack) await sender.replaceTrack(camTrack);
            stopStream(screenStream); screenStream = null;
            updateLabelsPosition();
          };
        } catch (e) { console.error(e); }
      } else {
        const sender = peerConnection.getSenders().find(s => s.track && s.track.kind === 'video');
        const camTrack = localStream.getVideoTracks()[0];
        if (sender && camTrack) await sender.replaceTrack(camTrack);
        stopStream(screenStream); screenStream = null;
        updateLabelsPosition();
      }
    };

    // Drag local video
    let isDragging = false, offsetX, offsetY;
    localVideo.addEventListener("mousedown", e => {
      isDragging = true;
      offsetX = e.clientX - localVideo.offsetLeft;
      offsetY = e.clientY - localVideo.offsetTop;
      localVideo.style.cursor = "grabbing";
    });
    document.addEventListener("mousemove", e => {
      if (isDragging) {
        localVideo.style.left = (e.clientX - offsetX) + "px";
        localVideo.style.top = (e.clientY - offsetY) + "px";
        updateLabelsPosition();
      }
    });
    document.addEventListener("mouseup", () => { isDragging = false; localVideo.style.cursor = "grab"; updateLabelsPosition(); });
    localVideo.addEventListener("touchstart", e => {
      isDragging = true; const t = e.touches[0];
      offsetX = t.clientX - localVideo.offsetLeft; offsetY = t.clientY - localVideo.offsetTop;
    });
    document.addEventListener("touchmove", e => {
      if (isDragging) { const t = e.touches[0]; localVideo.style.left = (t.clientX - offsetX) + "px"; localVideo.style.top = (t.clientY - offsetY) + "px"; updateLabelsPosition(); }
    });
    document.addEventListener("touchend", () => { isDragging = false; updateLabelsPosition(); });

    // Reposition labels when metadata (dimensions) load
    localVideo.addEventListener('loadedmetadata', updateLabelsPosition);
    remoteVideo.addEventListener('loadedmetadata', updateLabelsPosition);

    // Auto-hide controls & adjust remote video
    let hideControlsTimeout;
    const localVideoEl = document.getElementById("localVideo");
    const remoteVideoEl = document.getElementById("remoteVideo");
    function showControls() {
      controls.classList.remove("hidden");
      localVideoEl.classList.remove("lowered");
      remoteVideoEl.classList.add("shrinked");
      clearTimeout(hideControlsTimeout);
      hideControlsTimeout = setTimeout(() => {
        controls.classList.add("hidden");
        localVideoEl.classList.add("lowered");
        remoteVideoEl.classList.remove("shrinked");
        updateLabelsPosition();
      }, 3000);
      // recalc immediately when controls become visible
      requestAnimationFrame(updateLabelsPosition);
    }
    ["mousemove", "touchstart"].forEach(evt => { document.addEventListener(evt, showControls); });
    showControls();

    // Clean up on page unload
    window.addEventListener('beforeunload', cleanup);

    // Parse URL params and bind to doctor uid based room
    const params = new URLSearchParams(location.search);
    const as = (params.get('as') || '').toLowerCase();
    const hostUid = params.get('hostUid') || '';
    let roomFromParam = (params.get('room') || '').trim();
    // If no room provided but hostUid exists, derive doctor room id
    if (!roomFromParam && hostUid) roomFromParam = `doc_${hostUid}`;

    if (roomFromParam) {
      document.getElementById('roomIdInput').value = roomFromParam;
    }

    // Set initial local label as soon as we know role (before presence commit)
    try { setLocalLabelName(getDisplayName(as)); } catch (_) {}

    // Auto initialize media, then auto create/join depending on role
    (async () => {
      // Ensure auth state is loaded (if rules require auth)
      await new Promise(resolve => {
        try { firebase.auth().onAuthStateChanged(() => resolve()); } catch (_) { resolve(); }
      });
      try {
        showOverlay('Setting up your camera…');
        await ensureLocalStreams();
        hideOverlay();
      } catch (e) {
        hideOverlay();
        showToast('Camera/mic permission denied');
      }
      ['micBtn','camBtn','screenBtn','fullscreenBtn','leaveBtn'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.disabled = false;
      });

      // Doctor: always create/host the room (doc_<uid>)
      if (as === 'doctor' && (roomFromParam || hostUid)) {
        const roomId = roomFromParam || `doc_${hostUid}`;
        await createInRoom(roomId);
        return;
      }
      // Patient or unknown: if room is present, auto-join; otherwise wait for manual input
      if (roomFromParam) {
        await joinInRoom(roomFromParam);
      }
      // Ensure labels positioned after potential layout changes
      updateLabelsPosition();
    })();

    // Enter to join
    document.getElementById('roomIdInput').addEventListener('keydown', (e) => {
      if (e.key === 'Enter') document.getElementById('joinBtn').click();
    });

    // Members UI toggles
    document.getElementById('membersBtn').addEventListener('click', () =>{
      const p = document.getElementById('membersPanel');
      p.classList.toggle('hidden');
      updateLabelsPosition();
    });
    document.getElementById('membersClose').addEventListener('click', () =>{
      document.getElementById('membersPanel').classList.add('hidden');
      updateLabelsPosition();
    });

    // Reposition labels on window resize
    window.addEventListener('resize', updateLabelsPosition);
  </script>
</body>
</html>
