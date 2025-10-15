<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>TECHMED</title>
  <link rel="manifest" href="manifest.json" />
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="logo512.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Poppins:wght@300;500;700&family=Ubuntu:wght@400;500;700&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">

  <!-- Swiper CSS (single source) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>

  <!-- Flatpickr CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

  <!-- NAME JS FILE -->
  <script type="module" src="assets/js/name.js"></script>

  <style>
  .flatpickr-calendar{ font-size:12px!important; transform:scale(0.89); transform-origin:top left; }
  .flatpickr-day.prevMonthDay,.flatpickr-day.nextMonthDay{ visibility:hidden; pointer-events:none; }
  .flatpickr-day.available { background:#e6ffed !important; color:#0f5132 !important; border-radius:50% !important; }
  .flatpickr-day.booked { background:#ffe0e0 !important; color:#842029 !important; border-radius:50% !important; }
  .flatpickr-day.non-working { background:#f1f3f5 !important; color:#6c757d !important; border-radius:50% !important; pointer-events:none; }
  .flatpickr-day.selected { background:#87cefa !important; color:#000 !important; border-radius:50%; }
  #joinCallBtn[disabled] { opacity: .6; cursor: not-allowed; }
  </style>
</head>

<body class="doctors-page">
<header id="header" class="header d-flex align-items-center fixed-top">
  <div class="container position-relative d-flex align-items-center justify-content-between">
    <a href="techmed.php" class="logo d-flex align-items-center me-auto me-xl-0">
      <h1 class="sitename">TECH<span>MED</span></h1>
    </a>

    <nav id="navmenu" class="navmenu">
      <ul>
        <li><a href="techmed.php" class="active">Home</a></li>
        <li><a href="about.php">About</a></li>
        <li><a href="contact.php">Contact</a></li>
        <li><a href="doctor.php">Doctors</a></li>
        <li class="dropdown"><a href="#"><span>More Pages</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
          <ul>
            <li><a href="department-details.html">Medical Certificate</a></li>
            <li><a href="service-details.html">E-Prescription</a></li>
            <li><a href="service-details.html">Apply to be a Doctor</a></li>
          </ul>
        </li>
        <li class="dropdown"><a href="#"><span>Account</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
          <ul>
            <li><a id="headerUserName">Loading...</a></li>
            <li><a href="patientprofile.php">Profile</a></li>
            <li><a href="#" id="logoutLink">Log Out</a></li>
          </ul>
        </li>
      </ul>
      <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
    </nav>

    <!-- Appointment + Join buttons -->
    <div class="btn-group" role="group" aria-label="Appointments and Join">
      <button class="btn btn-getstarted" id="appointmentBtn">Appointments</button>
      <button class="btn btn-success" id="joinCallBtn" disabled>
        <i class="bi bi-camera-video"></i>
        <span class="d-none d-md-inline"> Join Call</span>
      </button>
    </div>

    <!-- Notification Button -->
    <button class="btn btn-outline-primary position-relative ms-0" id="notificationBtn" aria-label="Notifications">
      <i class="bi bi-bell"></i>
      <span id="notificationBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">0</span>
    </button>
  </div>
</header>

<!-- Notification Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="notificationModalLabel">Notifications</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="notificationLoading">Loading...</div>
        <ul id="notificationList" class="list-group d-none"></ul>
        <div id="notificationEmpty" class="text-muted d-none">No new notifications</div>
      </div>
    </div>
  </div>
</div>

<!-- Appointment Modal -->
<div class="modal fade" id="appointmentModal" tabindex="-1" aria-labelledby="appointmentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="appointmentModalLabel">Patient Appointments</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table table-striped" id="appointmentTable">
          <thead>
            <tr>
              <th>Doctor</th>
              <th>Date & Time</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap & JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Allowed categories and current user
  const ALLOWED_TYPES = ['appointment', 'reminder', 'account'];
  const currentUserId = localStorage.getItem('techmed_user_id') || 'user-123';

  // All notifications store (persisted). Seed minimal data if empty.
  let allNotifications;
  try { allNotifications = JSON.parse(localStorage.getItem('techmed_notifications_all') || '[]'); } catch (_) { allNotifications = []; }
  if (!allNotifications.length) {
    allNotifications = [
      { id: 1, userId: currentUserId, type: 'appointment', message: 'Your appointment starts in 10 minutes', createdAt: '2025-10-04T09:50:00Z', read: false, link: '#' },
      { id: 2, userId: currentUserId, type: 'reminder', message: 'Complete your profile details', createdAt: '2025-10-03T16:00:00Z', read: true, link: 'patientprofile.php' },
      { id: 99, userId: 'other-user', type: 'appointment', message: 'Not your notification', createdAt: '2025-10-01T10:00:00Z', read: false },
      { id: 100, userId: currentUserId, type: 'system', message: 'System-wide info', createdAt: '2025-10-01T09:00:00Z', read: false }
    ];
    localStorage.setItem('techmed_notifications_all', JSON.stringify(allNotifications));
    localStorage.setItem('techmed_user_id', currentUserId);
  }
  function saveAllNotifications() { localStorage.setItem('techmed_notifications_all', JSON.stringify(allNotifications)); }
  function userNotifications() { return (allNotifications || []).filter(n => n.userId === currentUserId && ALLOWED_TYPES.includes(n.type)); }

  // Patient POV appointments: mock for modal table fallback
  const demoAppointments = [
    { doctor: 'Dr. Amelia Carter', datetime: '2025-10-04 10:00 AM', link: 'index.html?room=TechMed-apt-001' },
    { doctor: 'Dr. Nathan Ruiz',  datetime: '2025-10-04 11:00 AM', link: 'index.html?room=TechMed-apt-002' }
  ];

  function formatDateTime(iso) { try { return new Date(iso).toLocaleString(); } catch (_) { return iso; } }

  // Elements
  const notificationBtn = document.getElementById('notificationBtn');
  const notificationBadge = document.getElementById('notificationBadge');
  const notificationModal = new bootstrap.Modal(document.getElementById('notificationModal'));
  const notificationLoading = document.getElementById('notificationLoading');
  const notificationList = document.getElementById('notificationList');
  const notificationEmpty = document.getElementById('notificationEmpty');

  const appointmentBtn = document.getElementById('appointmentBtn');
  const appointmentModal = new bootstrap.Modal(document.getElementById('appointmentModal'));
  const appointmentTableBody = document.querySelector('#appointmentTable tbody');
  const joinCallBtn = document.getElementById('joinCallBtn');

  // Notifications
  function renderNotifications() {
    notificationLoading.classList.remove('d-none');
    notificationList.classList.add('d-none');
    notificationEmpty.classList.add('d-none');
    setTimeout(() => {
      notificationLoading.classList.add('d-none');
      notificationList.innerHTML = '';
      const items = userNotifications();
      const unread = items.filter(n => !n.read).length;
      if (unread > 0) { notificationBadge.textContent = String(unread); notificationBadge.classList.remove('d-none'); }
      else { notificationBadge.classList.add('d-none'); }
      if (!items.length) { notificationEmpty.classList.remove('d-none'); return; }
      items.forEach(n => {
        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-start';
        li.innerHTML = `<div class="me-3"><div>${n.message}</div><small class="text-muted">${formatDateTime(n.createdAt)}</small></div>${n.link ? `<a href="${n.link}" class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener">Open</a>` : ''}`;
        notificationList.appendChild(li);
      });
      notificationList.classList.remove('d-none');
    }, 100);
  }

  notificationBtn.addEventListener('click', () => {
    notificationModal.show();
    renderNotifications();
    let changed = false;
    allNotifications = (allNotifications || []).map(n => {
      if (n.userId === currentUserId && ALLOWED_TYPES.includes(n.type) && !n.read) { changed = true; return { ...n, read: true }; }
      return n;
    });
    if (changed) { saveAllNotifications(); setTimeout(() => { const unread = userNotifications().filter(n => !n.read).length; if (unread === 0) notificationBadge.classList.add('d-none'); }, 300); }
  });

  // Render appointments from Firestore with per-row Join button
  async function renderAppointmentRowsForUser(uid){
    appointmentTableBody.innerHTML = '<tr><td colspan="3" class="text-muted">Loading…</td></tr>';
    try {
      const snap = await db.collection('appointments').where('patientId','==', uid).get();
      const items = [];
      snap.forEach(d => items.push({ id: d.id, ...d.data() }));
      items.sort((a,b)=>{
        const at = a.startAt?.toDate ? a.startAt.toDate().getTime() : (a.startAt ? new Date(a.startAt).getTime() : 0);
        const bt = b.startAt?.toDate ? b.startAt.toDate().getTime() : (b.startAt ? new Date(b.startAt).getTime() : 0);
        return at - bt;
      });

      appointmentTableBody.innerHTML = '';
      if (!items.length) {
        appointmentTableBody.innerHTML = '<tr><td colspan="3" class="text-muted">No appointments found</td></tr>';
        return;
      }

      items.forEach(a => {
        const doctor = a.doctorName || 'Doctor';
        const dt = a.startAt?.toDate ? a.startAt.toDate() : (a.startAt ? new Date(a.startAt) : null);
        const when = dt ? dt.toLocaleString() : '';
        const status = (a.status || '').toLowerCase();
        const disabled = status === 'cancelled' || status === 'rejected';
        const joinUrl = a.roomId ? `index.html?room=${encodeURIComponent(a.roomId)}&as=patient` : `index.html?appt=${encodeURIComponent(a.id)}&as=patient`;

        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${doctor}</td>
          <td>${when}</td>
          <td>
            <a href="${joinUrl}" class="btn btn-success btn-sm" ${disabled ? 'aria-disabled="true" tabindex="-1"' : ''}>Join Meeting</a>
          </td>
        `;
        appointmentTableBody.appendChild(row);
      });
    } catch (e) {
      appointmentTableBody.innerHTML = '<tr><td colspan="3" class="text-danger">Failed to load appointments</td></tr>';
    }
  }

  // Open modal and populate rows from Firestore
  appointmentBtn.addEventListener('click', async () => {
    appointmentModal.show();
    try {
      const user = firebase.auth().currentUser;
      if (!user) return;
      await renderAppointmentRowsForUser(user.uid);
    } catch (_) {}
  });
</script>

<!-- Vendor JS Files -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/php-email-form/validate.js"></script>
<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
<script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
<script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<!-- Firebase compat + init -->
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-auth-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-firestore-compat.js"></script>
<script src="./firebase-init.js"></script>

<!-- Meeting modal helper -->
<script src="./meeting-modal.js"></script>

<!-- Directory logic -->
<script src="./patient-directory.js"></script>
<!-- Appointment submission logic -->
<script src="./appointment.js"></script>

<!-- Auth header + logout + prefill + Join Call wiring -->
<script>
(function(){
  const auth = firebase.auth();
  const db = firebase.firestore();

  const nameEl = document.getElementById('headerUserName');
  const logoutEl = document.getElementById('logoutLink');
  const joinCallBtn = document.getElementById('joinCallBtn');

  let apptUnsub = null;
  let currentJoinUrl = '';

  function pickUpcoming(appts){
    const now = Date.now();
    const future = appts
      .filter(a => {
        const s = (a.status || '').toLowerCase();
        const t = a.startAt?.toDate ? a.startAt.toDate().getTime() : (a.startAt ? new Date(a.startAt).getTime() : 0);
        return !(s === 'cancelled' || s === 'rejected' || s === 'done') && t >= now - (60*60*1000);
      })
      .sort((a,b)=>{
        const at = a.startAt?.toDate ? a.startAt.toDate().getTime() : (a.startAt ? new Date(a.startAt).getTime() : 0);
        const bt = b.startAt?.toDate ? b.startAt.toDate().getTime() : (b.startAt ? new Date(b.startAt).getTime() : 0);
        return at - bt;
      });
    return future[0] || null;
  }

  function setJoinTarget(appt){
    if (!appt) {
      currentJoinUrl = '';
      joinCallBtn.disabled = true;
      joinCallBtn.title = 'No upcoming appointment';
      return;
    }
    // Prefer doctor's stable room derived from doctorId
    if (appt.doctorId) currentJoinUrl = `vc.php?room=${encodeURIComponent('doc_' + appt.doctorId)}&as=patient&hostUid=${encodeURIComponent(appt.doctorId)}`;
    else if (appt.roomId) currentJoinUrl = `vc.php?room=${encodeURIComponent(appt.roomId)}&as=patient`;
    else currentJoinUrl = `vc.php`;
    joinCallBtn.disabled = false;
    joinCallBtn.title = 'Join your next appointment';
  }

  function wireJoinClick(){
    joinCallBtn.addEventListener('click', (e)=>{
      e.preventDefault();
      if (!currentJoinUrl) return;
      if (window.TechmedMeeting && typeof window.TechmedMeeting.open === 'function') {
        window.TechmedMeeting.open(currentJoinUrl);
      } else {
        window.location.href = currentJoinUrl;
      }
    });
  }

  function watchPatientAppointments(uid){
    if (apptUnsub) { try { apptUnsub(); } catch(_) {} apptUnsub = null; }
    const q = db.collection('appointments').where('patientId','==', uid);
    apptUnsub = q.onSnapshot(snap => {
      const items = [];
      snap.forEach(d => items.push({ id: d.id, ...d.data() }));
      const next = pickUpcoming(items);
      setJoinTarget(next);
    }, _err => { setJoinTarget(null); });
  }

  auth.onAuthStateChanged(async (user)=>{
    if (!user) { window.location.replace('login.php'); return; }

    // Read patient profile
    try {
      const uDoc = await db.collection('users').doc(user.uid).get();
      let fullName = user.displayName || user.email || 'Account';
      if (uDoc.exists) {
        const u = uDoc.data();
        const fn = (u.firstName || '').trim();
        const ln = (u.lastName || '').trim();
        fullName = (fn || ln) ? `${fn} ${ln}`.trim() : fullName;
      }
      if (nameEl) nameEl.textContent = fullName;
    } catch {}

    // Join button
    wireJoinClick();
    watchPatientAppointments(user.uid);

    if (logoutEl) {
      logoutEl.addEventListener('click', async (e) => {
        e.preventDefault();
        await auth.signOut();
        window.location.replace('login.php');
      });
    }
  });
})();
</script>

<!-- Main JS File -->
<script src="assets/js/main.js"></script>
</body>
</html>
