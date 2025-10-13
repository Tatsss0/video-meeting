<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>TECHMED — Doctor POV</title>
  <link rel="manifest" href="manifest.json" />
  <meta name="theme-color" content="#2c3e50" />

  <!-- Favicons -->
  <link href="logo512.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Poppins:wght@300;500;700&family=Ubuntu:wght@400;500;700&display=swap"
    rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">
  <link href="docpov.css" rel="stylesheet">
</head>

<body class="index-page">
  <!-- Header -->
  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container d-flex align-items-center justify-content-between">
      <a href="techmed.php" class="logo d-flex align-items-center me-auto me-xl-0">
        <h1 class="sitename">TECH<span>MED</span></h1>
      </a>
      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="docpov.php" class="active">Dashboard</a></li>
          <li><a href="patient.php">Patients & Records</a></li>
          <li><a href="profile.php">Profile</a></li>
        </ul>
      </nav>
      <div class="d-flex align-items-center gap-2">
        <div class="dropdown">
          <button class="btn btn-light position-relative" id="notifyDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-bell"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notify-count" style="display:none">0</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end p-2" style="width:300px;max-height:360px;overflow:auto;" aria-labelledby="notifyDropdown" id="notify-list">
            <li class="text-muted small px-2">No new notifications</li>
          </ul>
        </div>

        <button id="logoutBtn" class="btn btn-outline-secondary">
          <i class="bi bi-box-arrow-right"></i> Logout
        </button>
      </div>
    </div>
  </header>

  <!-- Main -->
  <main id="main" class="mt-5 pt-4">
    <div class="container-fluid">
      <div class="row">
        <aside class="col-md-3 mb-3">
          <div class="card p-3">
            <div class="d-flex align-items-center mb-3">
              <div id="avatar" class="avatar me-2">Dr</div>
              <div>
                <div id="doc-name" class="fw-semibold">Doctor</div>
                <div class="text-muted small" id="doc-specialty">Specialty</div>
              </div>
            </div>
            - <button data-section="appointments" class="btn w-100 btn-light mb-2 active">Appointments</button>
            - <button data-section="patients" class="btn w-100 btn-light mb-2">Patients</button>
          </div>
        </aside>

        <section class="col-md-9">
          <div id="section-appointments" class="card p-3">
            <div class="d-flex align-items-center justify-content-between">
              <h4 class="m-0">Appointments</h4>
            </div>
            <ul class="nav nav-tabs mt-2" id="apptTabs" role="tablist">
              <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#upcoming">Upcoming</button></li>
              <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#past">Past</button></li>
            </ul>
            <div class="tab-content mt-3">
              <div class="tab-pane fade show active" id="upcoming">
                <div id="appointments-upcoming" class="list-group"></div>
              </div>
              <div class="tab-pane fade" id="past">
                <div id="appointments-past" class="list-group"></div>
              </div>
            </div>
          </div>

          <div id="section-patients" class="card p-3 mt-3" style="display:none">
            <div class="d-flex align-items-center justify-content-between">
              <h4 class="m-0">Patients</h4>
              <div class="input-group" style="max-width:300px">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" id="patient-search" class="form-control" placeholder="Search patients..." />
              </div>
            </div>
            <div id="patients-list" class="list-group mt-2"></div>
          </div>
        </section>
      </div>
    </div>
  </main>

  <footer class="border-top py-3 text-center small text-muted">© TECHMED</footer>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-auth-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-firestore-compat.js"></script>
  <script src="./firebase-init.js"></script>
  <!-- Connect meeting inline via fullscreen modal -->
  <script src="./meeting-modal.js"></script>
  <script>
    document.getElementById('logoutBtn')?.addEventListener('click', async () => {
      try { await auth.signOut(); } finally { window.location.replace('doclogin.php'); }
    });
  </script>

  <!-- Inline dashboard logic with working Done button -->
  <script>
  (function(){
    const state = { user: null, profile: null, unsubscribeAppointments: null };

    function qs(id){ return document.getElementById(id); }
    function el(html){ const d=document.createElement('div'); d.innerHTML=html.trim(); return d.firstChild; }
    function fmt(ts){ if (!ts) return ''; const d = ts?.toDate ? ts.toDate() : new Date(ts); return d.toLocaleString(); }
    function toDateObj(ts){ return ts && ts.toDate ? ts.toDate() : (ts ? new Date(ts) : null); }
    function statusBadge(status){
      const s = (status || 'pending').toLowerCase();
      const cls = (s === 'confirmed' || s === 'done') ? 'bg-success'
        : (s === 'cancelled' || s === 'rejected') ? 'bg-danger'
        : s === 'rescheduled' ? 'bg-warning text-dark'
        : 'bg-secondary';
      return `<span class="badge rounded-pill ${cls}">${s}</span>`;
    }

    function guardAuth(){
      auth.onAuthStateChanged(async (user)=>{
        if (typeof state.unsubscribeAppointments === 'function') {
          try { state.unsubscribeAppointments(); } catch(_) {}
          state.unsubscribeAppointments = null;
        }
        if (!user) { window.location.href = './doclogin.php'; return; }

        // Doctor-only guard
        try {
          const [userDoc, doctorDoc] = await Promise.all([
            db.collection('users').doc(user.uid).get(),
            db.collection('doctors').doc(user.uid).get()
          ]);
          const role = userDoc.exists ? userDoc.data().role : null;
          const isDoctor = role === 'doctor' || doctorDoc.exists;
          if (!isDoctor) {
            await auth.signOut().catch(()=>{});
            window.location.href = './doclogin.php';
            return;
          }
        } catch {
          await auth.signOut().catch(()=>{});
          window.location.href = './doclogin.php';
          return;
        }

        state.user = user;
        await loadProfile(user.uid);
        wireNav();
        loadAppointments();
        loadPatients();
      });
    }

    async function loadProfile(uid){
      const snap = await db.collection('doctors').doc(uid).get();
      const p = snap.exists ? snap.data() : {};
      qs('doc-name') && (qs('doc-name').textContent = p.fullName ? (p.fullName.toLowerCase().startsWith('dr.') ? p.fullName : `Dr. ${p.fullName}`) : (auth.currentUser?.displayName || 'Doctor'));
      qs('doc-specialty') && (qs('doc-specialty').textContent = p.specialty || 'Specialty');
      if (qs('avatar')) {
        const initials = (p.fullName || 'Dr').split(' ').map(s=>s[0]).join('').substring(0,2).toUpperCase();
        qs('avatar').textContent = initials;
      }
    }

    function wireNav(){
      document.querySelectorAll('[data-section]').forEach(btn => {
        btn.addEventListener('click', ()=> showSection(btn.getAttribute('data-section')));
      });
    }

    function showSection(id){
      ['appointments','patients','profile'].forEach(sec=>{
        const elx = qs('section-' + sec);
        if (elx) elx.style.display = (sec===id) ? '' : 'none';
      });
      document.querySelectorAll('[data-section]').forEach(b=> b.classList.toggle('active', b.getAttribute('data-section')===id));
    }

    function toast(msg){
      const t = el(`<div class="toast align-items-center text-bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true" style="position:fixed;bottom:20px;right:20px;z-index:1080"><div class="d-flex"><div class="toast-body">${msg}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>`);
      document.body.appendChild(t);
      const bsToast = new bootstrap.Toast(t, { delay: 1600 });
      bsToast.show();
      t.addEventListener('hidden.bs.toast', ()=> t.remove());
    }

    async function loadAppointments(){
      const uid = auth.currentUser.uid;
      const listUpcoming = qs('appointments-upcoming');
      const listPast = qs('appointments-past');

      if (listUpcoming) listUpcoming.innerHTML = '<div class="text-muted small px-2">Loading…</div>';
      if (listPast) listPast.innerHTML = '';

      if (typeof state.unsubscribeAppointments === 'function') {
        try { state.unsubscribeAppointments(); } catch(_) {}
        state.unsubscribeAppointments = null;
      }

      try {
        const q = db.collection('appointments').where('doctorId','==',uid);

        state.unsubscribeAppointments = q.onSnapshot(
          (snap) => {
            if (listUpcoming) listUpcoming.innerHTML = '';
            if (listPast) listPast.innerHTML = '';

            if (!snap || snap.empty) {
              if (listUpcoming) listUpcoming.innerHTML = '<div class="list-group-item">No upcoming appointments</div>';
              return;
            }

            const nowMs = Date.now();
            const items = [];
            // Use collision-safe key for doc id
            snap.forEach(doc => items.push({ ...doc.data(), docId: doc.id }));

            items.sort((a,b) => {
              const at = (a.startAt?.toDate?.() ?? new Date(a.startAt ?? 0)).getTime();
              const bt = (b.startAt?.toDate?.() ?? new Date(b.startAt ?? 0)).getTime();
              return at - bt;
            });

            for (const a of items) {
              const when = a.startAt?.toDate?.() ? a.startAt.toDate() : (a.startAt ? new Date(a.startAt) : null);
              const statusLower = (a.status || '').toLowerCase();
              const isDone = statusLower === 'done';
              const isPast = isDone || (when ? when.getTime() < nowMs : false);
              const roomLink = a.roomId ? `./index.html?room=${encodeURIComponent(a.roomId)}` : './index.html';

              const status = (a.status || 'pending').toLowerCase();
              const badgeClass = (status === 'confirmed' || status === 'done') ? 'bg-success'
                                : (status === 'cancelled' || status === 'rejected') ? 'bg-danger'
                                : status === 'rescheduled' ? 'bg-warning text-dark'
                                : 'bg-secondary';

              const actions = isPast
                ? `<a href="${roomLink}" class="btn btn-sm btn-primary">${a.roomId ? 'Join call' : 'Open meeting'}</a>`
                : `<a href="${roomLink}" class="btn btn-sm btn-primary">${a.roomId ? 'Join call' : 'Open meeting'}</a>
                   <button class="btn btn-sm btn-outline-success mark-done-btn" data-id="${a.docId}" ${status === 'done' ? 'disabled' : ''}>
                     <i class="bi bi-check-lg"></i> Done
                   </button>`;

              const item = el(`<div class="list-group-item d-flex justify-content-between align-items-center appt-card">
                <div>
                  <div class="fw-semibold d-flex align-items-center gap-2">
                    <span>${a.patientName || 'Patient'}</span>
                    <span class="badge rounded-pill ${badgeClass}">${status}</span>
                  </div>
                  <div class="text-muted small">${fmt(a.startAt)}${a.reason ? ` • ${a.reason}` : ''}</div>
                </div>
                <div class="d-flex gap-2">${actions}</div>
              </div>`);

              if (!isPast) {
                const doneBtn = item.querySelector('.mark-done-btn');
                if (doneBtn) {
                  doneBtn.addEventListener('click', async () => {
                    if (doneBtn.disabled) return;
                    doneBtn.disabled = true;
                    const original = doneBtn.innerHTML;
                    doneBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                    try {
                      const docId = doneBtn.getAttribute('data-id');
                      await db.collection('appointments').doc(docId).update({
                        status: 'done',
                        completedAt: firebase.firestore.FieldValue.serverTimestamp(),
                        updatedAt: firebase.firestore.FieldValue.serverTimestamp()
                      });
                      toast('Appointment marked as done');
                    } catch (err) {
                      console.error('mark done failed', err);
                      doneBtn.disabled = false;
                      doneBtn.innerHTML = original;
                      toast('Failed to mark as done: ' + (err?.message || ''));
                    }
                  });
                }
              }

              (isPast ? listPast : listUpcoming).appendChild(item);
            }

            if (listPast && !listPast.children.length) {
              listPast.innerHTML = '<div class="list-group-item">No past appointments</div>';
            }
          },
          (err) => {
            console.error('appointments listener error', err);
            if (listUpcoming) listUpcoming.innerHTML = '<div class="list-group-item text-danger">Failed to load appointments</div>';
          }
        );
      } catch (err) {
        console.error('loadAppointments failed', err);
        if (listUpcoming) listUpcoming.innerHTML = '<div class="list-group-item text-danger">Failed to load appointments</div>';
      }
    }

    async function loadPatients(){
      const uid = auth.currentUser.uid;
      const list = qs('patients-list');
      if (list) list.innerHTML = '<div class="text-muted small px-2">Loading…</div>';
      const snap = await db.collection('patients').where('doctorId','==',uid).limit(25).get().catch(()=>null);
      if (!list) return;
      list.innerHTML = '';
      if (!snap || snap.empty) { list.innerHTML = '<div class="list-group-item">No patients yet</div>'; return; }
      snap.forEach(doc => {
        const p = doc.data();
        list.appendChild(el(`<div class="list-group-item d-flex justify-content-between align-items-center">
          <div>
            <div class="fw-semibold">${p.fullName || 'Patient'}</div>
            <div class="text-muted small">${p.email || ''}</div>
          </div>
          <a class="btn btn-sm btn-outline-primary" href="#">View</a>
        </div>`));
      });
    }

    window.addEventListener('beforeunload', () => {
      if (typeof state.unsubscribeAppointments === 'function') {
        try { state.unsubscribeAppointments(); } catch(_) {}
      }
    });

    guardAuth();
  })();
  </script>
</body>
</html>
