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

  /* Appointment calendar color coding */
  .flatpickr-day.available {
    background:#e6ffed !important;  /* green */
    color:#0f5132 !important;
    border-radius:50% !important;
  }
  .flatpickr-day.booked {
    background:#ffe0e0 !important;  /* red */
    color:#842029 !important;
    border-radius:50% !important;
  }
  .flatpickr-day.non-working {
    background:#f1f3f5 !important;  /* grey */
    color:#6c757d !important;
    border-radius:50% !important;
    pointer-events:none;
  }
  .flatpickr-day.selected {
    background:#87cefa !important;
    color:#000 !important;
    border-radius:50%;
  }

  /* Optional: time dropdown */
  #timeSelect option.slot-available { color:#198754; font-weight:500; }
  #timeSelect option.slot-booked    { color:#dc3545; }

  /* Minimal card slider look */
  .compact-view .minimal-card{cursor:pointer; border:1px solid #e9ecef ; border-radius:12px; padding:14px; transition:box-shadow .2s,border-color .2s; background:#fff;}
  .compact-view .minimal-card:hover, .compact-view .minimal-card.active{border-color:#0d6efd; box-shadow:0 0 0 3px rgba(13,110,253,.12);}
  .compact-view .minimal-card .avatar{width:120px; height:120px; object-fit:cover; border-radius:50%; display:block; margin:0 auto 10px;}
  .compact-view .minimal-card .info h4{font-size:1rem; margin:0;}
  .compact-view .minimal-card .info small{color:#6c757d;}
  .doctor-card .doctor-media img{width:100%; height:220px; object-fit:cover; border-radius:10px;}
  .doctor-card .doctor-actions .btn{margin-right:6px;}
</style>


</head>

<body class="doctors-page">
<!-- Header -->
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

    <!-- Appointment Button -->
    <button class="btn btn-getstarted me-0" id="appointmentBtn">Appointments</button>

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
          <tbody>
            <!-- Appointments will be dynamically loaded here -->
          </tbody>
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
  try {
    allNotifications = JSON.parse(localStorage.getItem('techmed_notifications_all') || '[]');
  } catch (_) {
    allNotifications = [];
  }
  if (!allNotifications.length) {
    allNotifications = [
      { id: 1, userId: currentUserId, type: 'appointment', message: 'Your appointment starts in 10 minutes', createdAt: '2025-10-04T09:50:00Z', read: false, link: '#' },
      { id: 2, userId: currentUserId, type: 'reminder', message: 'Complete your profile details', createdAt: '2025-10-03T16:00:00Z', read: true, link: 'patientprofile.php' },
      // Not shown: different user or non-allowed type
      { id: 99, userId: 'other-user', type: 'appointment', message: 'Not your notification', createdAt: '2025-10-01T10:00:00Z', read: false },
      { id: 100, userId: currentUserId, type: 'system', message: 'System-wide info', createdAt: '2025-10-01T09:00:00Z', read: false }
    ];
    localStorage.setItem('techmed_notifications_all', JSON.stringify(allNotifications));
    localStorage.setItem('techmed_user_id', currentUserId);
  }
  function saveAllNotifications() {
    localStorage.setItem('techmed_notifications_all', JSON.stringify(allNotifications));
  }
  function userNotifications() {
    return (allNotifications || []).filter(n =>
      n.userId === currentUserId && ALLOWED_TYPES.includes(n.type)
    );
  }

  function formatDateTime(iso) {
    try { return new Date(iso).toLocaleString(); } catch (_) { return iso; }
  }

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

  // Notifications: render + badge (user + allowed types only)
  function renderNotifications() {
    notificationLoading.classList.remove('d-none');
    notificationList.classList.add('d-none');
    notificationEmpty.classList.add('d-none');

    setTimeout(() => {
      notificationLoading.classList.add('d-none');
      notificationList.innerHTML = '';

      const items = userNotifications();
      const unread = items.filter(n => !n.read).length;

      if (unread > 0) {
        notificationBadge.textContent = String(unread);
        notificationBadge.classList.remove('d-none');
      } else {
        notificationBadge.classList.add('d-none');
      }

      if (!items.length) {
        notificationEmpty.classList.remove('d-none');
        return;
      }

      items.forEach(n => {
        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-start';
        li.innerHTML = `
          <div class="me-3">
            <div>${n.message}</div>
            <small class="text-muted">${formatDateTime(n.createdAt)}</small>
          </div>
          ${n.link ? `<a href="${n.link}" class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener">Open</a>` : ''}
        `;
        notificationList.appendChild(li);
      });
      notificationList.classList.remove('d-none');
    }, 100);
  }

  // Show notifications, then mark only the user's allowed ones as read
  notificationBtn.addEventListener('click', () => {
    notificationModal.show();
    renderNotifications();

    let changed = false;
    allNotifications = (allNotifications || []).map(n => {
      if (n.userId === currentUserId && ALLOWED_TYPES.includes(n.type) && !n.read) {
        changed = true;
        return { ...n, read: true };
      }
      return n;
    });
    if (changed) {
      saveAllNotifications();
      setTimeout(() => {
        const unread = userNotifications().filter(n => !n.read).length;
        if (unread === 0) notificationBadge.classList.add('d-none');
      }, 300);
    }
  });

  // ===== Appointments: fetch current patient's appointments (excluding done/completed) and render Join buttons =====
  async function waitForAuthUser() {
    const auth = firebase.auth();
    if (auth.currentUser) return auth.currentUser;
    return new Promise(resolve => {
      const unsub = auth.onAuthStateChanged(u => { unsub(); resolve(u); });
    });
  }

  function isAppointmentDone(a) {
  const boolFlags = [
    a.isDone, a.done, a.completed, a.isCompleted,
    a.finished, a.isFinished,
    a.marked, a.isMarked,
    a.cancelled, a.canceled, a.isCancelled, a.isCanceled,
    a.attended, a.isAttended
  ];
  if (boolFlags.some(v => v === true)) return true;

  const statusFields = [a.status, a.appointmentStatus, a.state, a.stage, a.progress];
  for (const s of statusFields) {
    if (!s) continue;
    const str = String(s).trim().toLowerCase();
    if (/(^|\b)(done|completed|complete|finished|attended|cancelled|canceled|no-?show)(\b|$)/.test(str)) {
      return true;
    }
  }
  return false;
}

// New: robustly resolve the appointment's start Date
function getAppointmentStartDate(a) {
  if (a.startAt?.toDate) {
    try { return a.startAt.toDate(); } catch (_) {}
  }
  if (a.datetime) {
    const d = new Date(a.datetime);
    if (!isNaN(d)) return d;
  }
  if (a.date && a.time) {
    const d = new Date(`${a.date} ${a.time}`);
    if (!isNaN(d)) return d;
  }
  if (a.date) {
    const d = new Date(a.date);
    if (!isNaN(d)) return d;
  }
  return null; // unknown -> we’ll keep it unless you prefer to hide unknowns
}

// New: only future-dated appointments count as upcoming
function isAppointmentUpcoming(a) {
  const when = getAppointmentStartDate(a);
  if (!when) return true; // keep unknowns visible; change to `false` if you want to hide them
  return when.getTime() >= Date.now();
}

// Replace loadAndRenderAppointments with this version
async function loadAndRenderAppointments() {
  appointmentTableBody.innerHTML = '<tr><td colspan="3" class="text-muted">Loading...</td></tr>';

  const user = await waitForAuthUser();
  if (!user) {
    appointmentTableBody.innerHTML = '<tr><td colspan="3" class="text-muted">Please sign in to view appointments.</td></tr>';
    return;
  }

  const db = firebase.firestore();
  const now = new Date();
  let docs = [];

  // Prefer server-side filtering by future startAt; fall back gracefully if index missing
  try {
    const snap = await db.collection('appointments')
      .where('patientId', '==', user.uid)
      .where('startAt', '>=', firebase.firestore.Timestamp.fromDate(now))
      .orderBy('startAt', 'asc')
      .limit(50)
      .get();
    snap.forEach(d => docs.push(d));
  } catch (_) {
    try {
      const snap = await db.collection('appointments')
        .where('patientId', '==', user.uid)
        .orderBy('startAt', 'asc')
        .limit(50)
        .get();
      snap.forEach(d => docs.push(d));
    } catch {
      const snap = await db.collection('appointments')
        .where('patientId', '==', user.uid)
        .limit(50)
        .get();
      snap.forEach(d => docs.push(d));
    }
  }

  // Show only upcoming and not-done
  const activeDocs = docs.filter(d => {
    const a = d.data();
    return !isAppointmentDone(a) && isAppointmentUpcoming(a);
  });

  appointmentTableBody.innerHTML = '';

  if (!activeDocs.length) {
    appointmentTableBody.innerHTML = '<tr><td colspan="3" class="text-muted">No upcoming appointments.</td></tr>';
    return;
  }

  activeDocs.forEach(doc => {
  const a = doc.data();
  const doctor =
    a.doctorName
    || [a.doctorFirstName, a.doctorLastName].filter(Boolean).join(' ')
    || a.doctor
    || 'Doctor';

  const startDate = getAppointmentStartDate(a);
  const now = new Date();
  const datetime = startDate ? startDate.toLocaleString() : (a.datetime || [a.date, a.time].filter(Boolean).join(' ') || '—');

  const docUid = a.doctorUid || a.doctorId || a.hostUid || a.hostId;
  const link =
    a.meetingUrl || a.videoUrl || a.videoLink || a.joinUrl
    || (a.roomName ? `video.php?room=${encodeURIComponent(a.roomName)}&id=${encodeURIComponent(doc.id)}` :
        (docUid ? `video.php?hostUid=${encodeURIComponent(docUid)}&id=${encodeURIComponent(doc.id)}` : ''));

  // Enable only when it's time for the appointment (within 15 minutes before start)
  const canJoin = link && startDate && (now >= new Date(startDate.getTime() - 15 * 60 * 1000));

  const joinHtml = canJoin
    ? `<a href="${link}" class="btn btn-success btn-sm" target="_blank" rel="noopener">Join Meeting</a>`
    : `<button class="btn btn-secondary btn-sm" disabled>Join Meeting</button>`;

  const row = document.createElement('tr');
  row.innerHTML = `
    <td>${doctor}</td>
    <td>${datetime}</td>
    <td>${joinHtml}</td>
  `;
  appointmentTableBody.appendChild(row);
});
}

  // Open modal and render appointments
  appointmentBtn.addEventListener('click', async () => {
    await loadAndRenderAppointments();
    appointmentModal.show();
  });

  // Initialize badge (user + allowed only)
  (function initBadge() {
    const unread = userNotifications().filter(n => !n.read).length;
    if (unread > 0) {
      notificationBadge.textContent = String(unread);
      notificationBadge.classList.remove('d-none');
    } else {
      notificationBadge.classList.add('d-none');
    }
  })();
</script>

<!-- Bootstrap & JS -->

<main class="main">
  <!-- Page Title -->
  <div class="page-title">
    <div class="heading">
      <div class="container">
        <div class="row d-flex justify-content-center text-center">
          <div class="col-lg-8">
            <h1 class="heading-title">Doctors</h1>
            <p class="mb-0">TechMed brings together licensed and highly qualified specialists...</p>
          </div>
        </div>
      </div>
    </div>
    <nav class="breadcrumbs">
      <div class="container">
        <ol>
          <li><a href="techmed.php">Home</a></li>
          <li class="current">Doctors</li>
        </ol>
      </div>
    </nav>
  </div>

  <!-- Doctors Section -->
  <section id="doctors" class="doctors section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <!-- VIEW CARD (SLIDER) -->
      <div class="compact-view mt-5">
        <div class="swiper">
          <div class="swiper-wrapper"><!-- populated by patient-directory.js --></div>
          <div class="swiper-button-prev"></div>
          <div class="swiper-button-next"></div>
          <br>
          <div class="swiper-pagination"></div>
        </div>
      </div>

      <!-- Doctor Profile with Tabs -->
      <div class="profile-tabs mt-5">
        <div class="row g-4">
          <div class="col-lg-4">
            <div class="tab-profile-card">
              <img id="doctor-image" src="logo.png" class="img-fluid rounded-3" alt="">
              <div class="pt-3">
                <h3 id="doctor-name" class="mb-1">Select a Doctor</h3>
                <p id="doctor-specialty" class="mb-2">Specialty</p>
              </div>
            </div>
          </div>
          <div class="col-lg-8">
            <ul class="nav nav-pills mb-3" role="tablist">
              <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-bio">Bio</button></li>
              <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-schedule">Schedule</button></li>
              <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-reviews">Reviews</button></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane fade show active" id="tab-bio">
                <p id="doctor-bio">Click a doctor card to see their profile.</p>
              </div>
              <div class="tab-pane fade" id="tab-schedule">
                <div id="doctor-schedule" class="schedule-grid"></div>
              </div>
              <div class="tab-pane fade" id="tab-reviews">
                <p id="doctor-reviews">Reviews will appear here.</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <br><br>

      <!-- Filterable Doctor Directory -->
      <div class="doctor-directory mb-5">
        <div class="directory-bar p-3 p-md-4 rounded-3">
          <div class="row gy-4 isotope-container" data-aos="fade-up" data-aos-delay="300">
            <!-- populated by patient-directory.js -->
             
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Appointment Section -->
  <section id="appointment" class="appointmnet section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="row gy-4">
        <div class="col-lg-6">
          <div class="appointment-info">
            <h3>Quick &amp; Easy Online Booking</h3>
            <p class="mb-4">Book your appointment in just a few simple steps.</p>
            <!-- features ... -->
          </div>
        </div>

        <div class="col-lg-6">
          <div class="appointment-form-wrapper" data-aos="fade-up" data-aos-delay="200">
            <form id="appointment-form" class="appointment-form">
              <div class="row gy-3">
                <div class="col-md-6">
                  <input type="text" name="name" class="form-control" placeholder="Your Full Name" required>
                </div>
                <div class="col-md-6">
                  <input type="email" name="email" class="form-control" placeholder="Your Email" required>
                </div>
                <div class="col-md-6">
                  <input type="tel" name="phone" class="form-control" placeholder="Your Phone Number" inputmode="tel" maxlength="11" oninput="this.value=this.value.replace(/(?!^\\+)[^\\d]/g,'')" required>
                </div>
                <div class="col-md-6">
                  <input type="text" name="doctor" id="doctorInput" class="form-control" placeholder="Selected Doctor" readonly required>
                  <input type="hidden" id="doctorIdHidden" name="doctorId">
                </div>
                <div class="col-md-6">
                  <div class="calendar-wrapper">
                    <input type="text" name="date" id="dateInput" class="form-control" placeholder="Select Date" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <select name="time" id="timeSelect" class="form-select" required>
                    <option value="">Select Time</option>
                  </select>
                </div>
                <div class="col-12">
                  <div class="loading" style="display:none">Loading</div>
                  <div class="error-message text-danger"></div>
                  <div class="sent-message text-success" style="display:none">Your appointment request has been sent successfully.</div>
                  <button type="submit" class="btn btn-appointment w-100">
                    <i class="bi bi-calendar-plus me-2"></i>Book Appointment
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
  </section>
</main>

<footer id="footer" class="footer position-relative">
  <!-- footer content unchanged -->
  <div class="container copyright text-center mt-4">
    <p>© <span>Copyright</span> <strong>TECHMED</strong>&nbsp;<span>All Rights Reserved</span></p>
    <div class="credits">Designed by <a href="">Lester Ramirez</a></div>
  </div>
</footer>

<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
<div id="preloader"></div>

<!-- Vendor JS Files -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/php-email-form/validate.js"></script>
<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
<script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
<script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>

<!-- Swiper JS (single source) -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<!-- Firebase compat + init -->
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-auth-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-firestore-compat.js"></script>
<script src="./firebase-init.js"></script>

<!-- Directory logic (loads doctors into slider + directory and wires schedule) -->
<script src="./patient-directory.js"></script>

<!-- Appointment submission logic -->
<script src="./appointment.js"></script>

<!-- Auth header + logout + prefill only (no duplicate submit) -->
<script>
  (function () {
    const auth = firebase.auth();
    const db = firebase.firestore();

    // Header auth + logout + prefill patient info
    auth.onAuthStateChanged(async (user) => {
      const nameEl = document.getElementById('headerUserName');
      const logoutEl = document.getElementById('logoutLink');

      if (!user) { window.location.replace('login.php'); return; }

      // Read patient data
      let fullName = user.displayName || '';
      let phone = '';
      try {
        const uDoc = await db.collection('users').doc(user.uid).get();
        if (uDoc.exists) {
          const u = uDoc.data();
          const fn = (u.firstName || '').trim();
          const ln = (u.lastName || '').trim();
          fullName = (fn || ln) ? `${fn} ${ln}`.trim() : (user.displayName || user.email || 'Account');
          phone = u.phone || '';
        }
      } catch {}

      if (nameEl) nameEl.textContent = fullName || user.email || 'Account';

      // Prefill appointment form
      const form = document.getElementById('appointment-form');
      if (form) {
        const nameInput = form.querySelector('input[name="name"]');
        const emailInput = form.querySelector('input[name="email"]');
        const phoneInput = form.querySelector('input[name="phone"]');
        if (nameInput && fullName) nameInput.value = fullName;
        if (emailInput && user.email) emailInput.value = user.email;
        if (phoneInput && phone) phoneInput.value = phone;
      }

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
