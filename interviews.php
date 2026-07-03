<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
requireLogin();

$cid = (int)$_SESSION['company_id'];

// ── AJAX actions ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $db = getDB();

    if ($_POST['action'] === 'schedule') {
        $aid  = (int)($_POST['applicant_id'] ?? 0);
        $date = $_POST['interview_date'] ?? '';
        $time = $_POST['interview_time'] ?? '';
        $mode = in_array($_POST['mode'] ?? '', ['Online','Offline']) ? $_POST['mode'] : 'Online';
        $link = trim($_POST['meeting_link'] ?? '');

        if (!$aid || !$date || !$time) { echo json_encode(['ok'=>false,'msg'=>'Missing fields']); exit; }

        $stmt = $db->prepare("INSERT INTO interviews (company_id,applicant_id,interview_date,interview_time,mode,meeting_link) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param('iissss', $cid, $aid, $date, $time, $mode, $link);
        $ok = $stmt->execute();
        $newId = $db->insert_id;
        $stmt->close();

        // Fetch applicant name + job for response
        $s2 = $db->prepare("SELECT a.name, j.title FROM applicants a JOIN jobs j ON j.id=a.job_id WHERE a.id=?");
        $s2->bind_param('i', $aid); $s2->execute();
        $row = $s2->get_result()->fetch_assoc(); $s2->close();

        echo json_encode(['ok'=>$ok,'id'=>$newId,'name'=>$row['name']??'','job'=>$row['title']??'','mode'=>$mode,'date'=>$date,'time'=>$time]);
        $db->close(); exit;
    }

    if ($_POST['action'] === 'cancel') {
        $iid  = (int)($_POST['interview_id'] ?? 0);
        $stmt = $db->prepare("UPDATE interviews SET status='cancelled' WHERE id=? AND company_id=?");
        $stmt->bind_param('ii', $iid, $cid);
        echo json_encode(['ok' => $stmt->execute()]);
        $stmt->close(); $db->close(); exit;
    }

    if ($_POST['action'] === 'delete') {
        $iid  = (int)($_POST['interview_id'] ?? 0);
        $stmt = $db->prepare("DELETE FROM interviews WHERE id=? AND company_id=?");
        $stmt->bind_param('ii', $iid, $cid);
        echo json_encode(['ok' => $stmt->execute()]);
        $stmt->close(); $db->close(); exit;
    }

    echo json_encode(['ok'=>false]); $db->close(); exit;
}

// ── Load interviews ───────────────────────────────────
$db   = getDB();
$stmt = $db->prepare("
    SELECT iv.*, a.name AS applicant_name, j.title AS job_title
    FROM interviews iv
    JOIN applicants a ON a.id = iv.applicant_id
    JOIN jobs       j ON j.id = a.job_id
    WHERE iv.company_id = ?
    ORDER BY iv.interview_date ASC, iv.interview_time ASC
");
$stmt->bind_param('i', $cid);
$stmt->execute();
$interviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Load accepted applicants for the schedule form
$stmt2 = $db->prepare("
    SELECT a.id, a.name, j.title AS job_title
    FROM applicants a
    JOIN jobs j ON j.id = a.job_id
    WHERE a.company_id=? AND a.status='accepted'
    ORDER BY a.name
");
$stmt2->bind_param('i', $cid);
$stmt2->execute();
$acceptedApplicants = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt2->close(); $db->close();

$preselect = (int)($_GET['add'] ?? 0);

$activePage = 'interviews';
$pageTitle  = 'Scheduled Interviews';
$showSearch = true;
$searchPlaceholder = 'Search candidate…';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Interviews – IPS Company Panel</title>
  <link rel="stylesheet" href="css/sidebar.css">
  <link rel="stylesheet" href="css/interviews.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
  .schedule-bar{background:#fff;border-radius:14px;padding:20px 24px;box-shadow:0 4px 16px rgba(0,0,0,.06);margin-bottom:24px;display:flex;flex-wrap:wrap;gap:14px;align-items:flex-end;}
  .schedule-bar select,.schedule-bar input{padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;outline:none;min-width:170px;}
  .schedule-bar select:focus,.schedule-bar input:focus{border-color:#18b65a;}
  .schedule-bar .sched-btn{background:#18b65a;color:#fff;border:none;padding:11px 22px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;}
  .schedule-bar .sched-btn:hover{background:#14964a;}
  .schedule-bar label{font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;}
  .cancel-interview{background:#fef2f2;color:#dc2626;border:1px solid #fecaca;padding:7px 14px;border-radius:8px;font-size:13px;cursor:pointer;font-weight:500;}
  .delete-interview{background:#f3f4f6;color:#374151;border:1.5px solid #e5e7eb;padding:7px 14px;border-radius:8px;font-size:13px;cursor:pointer;font-weight:500;}
  .status-cancelled{opacity:.55;}
  .badge-cancelled{background:#fee2e2;color:#991b1b;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;}
  </style>
</head>
<body>
<div class="container">
  <?php include 'includes/sidebar.php'; ?>
  <main class="main-area">
    <?php include 'includes/topbar.php'; ?>
    <div class="page-content">

      <!-- Schedule Form -->
      <div class="schedule-bar">
        <div>
          <label>Candidate</label>
          <select id="schedApplicant">
            <option value="">-- Select Accepted Applicant --</option>
            <?php foreach ($acceptedApplicants as $ap): ?>
            <option value="<?= $ap['id'] ?>" <?= $preselect === $ap['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($ap['name']) ?> – <?= htmlspecialchars($ap['job_title']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label>Date</label>
          <input type="date" id="schedDate" min="<?= date('Y-m-d') ?>">
        </div>
        <div>
          <label>Time</label>
          <input type="time" id="schedTime">
        </div>
        <div>
          <label>Mode</label>
          <select id="schedMode">
            <option value="Online">Online</option>
            <option value="Offline">Offline</option>
          </select>
        </div>
        <div>
          <label>Meeting Link (optional)</label>
          <input type="url" id="schedLink" placeholder="https://meet.google.com/…">
        </div>
        <button class="sched-btn" onclick="scheduleInterview()">
          <i class="fa-solid fa-calendar-plus"></i> Schedule
        </button>
      </div>

      <div class="interview-list" id="interviewList">
        <?php if (empty($interviews)): ?>
          <p style="color:#9ca3af;">No interviews scheduled yet.</p>
        <?php else: ?>
          <?php foreach ($interviews as $i => $iv): ?>
          <div class="interview-card <?= $iv['status'] === 'cancelled' ? 'status-cancelled' : '' ?>" id="iv-<?= $iv['id'] ?>">
            <div class="card-top">
              <img src="https://i.pravatar.cc/50?img=<?= ($i % 15) + 1 ?>" alt="">
              <div>
                <h3><?= htmlspecialchars($iv['applicant_name']) ?></h3>
                <p class="position"><?= htmlspecialchars($iv['job_title']) ?></p>
              </div>
              <?php if ($iv['status'] === 'cancelled'): ?>
                <span class="badge-cancelled">Cancelled</span>
              <?php else: ?>
                <span class="mode-badge <?= strtolower($iv['mode']) ?>"><?= $iv['mode'] ?></span>
              <?php endif; ?>
            </div>
            <div class="card-details">
              <div class="detail-item">
                <i class="fa-solid fa-calendar-days"></i>
                <span><?= date('d M Y', strtotime($iv['interview_date'])) ?></span>
              </div>
              <div class="detail-item">
                <i class="fa-regular fa-clock"></i>
                <span><?= date('h:i A', strtotime($iv['interview_time'])) ?></span>
              </div>
            </div>
            <?php if ($iv['status'] !== 'cancelled'): ?>
            <div class="buttons">
              <?php if ($iv['mode'] === 'Online' && $iv['meeting_link']): ?>
                <a href="<?= htmlspecialchars($iv['meeting_link']) ?>" target="_blank">
                  <button class="join-btn"><i class="fa-solid fa-video"></i> Join Meeting</button>
                </a>
              <?php else: ?>
                <button class="join-btn"><i class="fa-solid fa-location-dot"></i> View Details</button>
              <?php endif; ?>
              <button class="cancel-interview" onclick="cancelInterview(this, <?= $iv['id'] ?>)">
                <i class="fa-solid fa-ban"></i> Cancel
              </button>
              <button class="delete-interview" onclick="deleteInterview(this, <?= $iv['id'] ?>)">
                <i class="fa-solid fa-trash"></i>
              </button>
            </div>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

    </div>
  </main>
</div>

<script>
document.getElementById('searchInput').addEventListener('keyup', function(){
  const f = this.value.toLowerCase();
  document.querySelectorAll('.interview-card').forEach(c => {
    c.style.display = c.querySelector('h3').textContent.toLowerCase().includes(f) ? '' : 'none';
  });
});

function scheduleInterview() {
  const aid  = document.getElementById('schedApplicant').value;
  const date = document.getElementById('schedDate').value;
  const time = document.getElementById('schedTime').value;
  const mode = document.getElementById('schedMode').value;
  const link = document.getElementById('schedLink').value;

  if (!aid || !date || !time) { alert('Please select candidate, date and time.'); return; }

  fetch('interviews.php', {
    method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body: new URLSearchParams({
      action:'schedule', applicant_id:aid, interview_date:date, interview_time:time, mode:mode, meeting_link:link
    })
  })
  .then(r => r.ok ? r.json() : Promise.reject('Network response was not ok'))
  .then(d => {
    if (d.ok) {
      const fmt = new Date(d.date + 'T' + d.time);
      const dateStr = fmt.toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'});
      const timeStr = fmt.toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'});
      const html = `
        <div class="interview-card" id="iv-${d.id}">
          <div class="card-top">
            <img src="https://i.pravatar.cc/50?img=3" alt="">
            <div><h3>${d.name}</h3><p class="position">${d.job}</p></div>
            <span class="mode-badge ${d.mode.toLowerCase()}">${d.mode}</span>
          </div>
          <div class="card-details">
            <div class="detail-item"><i class="fa-solid fa-calendar-days"></i><span>${dateStr}</span></div>
            <div class="detail-item"><i class="fa-regular fa-clock"></i><span>${timeStr}</span></div>
          </div>
          <div class="buttons">
            <button class="join-btn"><i class="fa-solid fa-video"></i> Join Meeting</button>
            <button class="cancel-interview" onclick="cancelInterview(this,${d.id})"><i class="fa-solid fa-ban"></i> Cancel</button>
            <button class="delete-interview" onclick="deleteInterview(this,${d.id})"><i class="fa-solid fa-trash"></i></button>
          </div>
        </div>`;
      document.getElementById('interviewList').insertAdjacentHTML('afterbegin', html);
      ['schedApplicant','schedDate','schedTime','schedLink'].forEach(id => document.getElementById(id).value='');
    } else {
      alert(d.msg || 'Scheduling failed. Try again.');
    }
  })
  .catch(() => alert('Could not schedule interview. Please try again.'));
}

function cancelInterview(btn, id) {
  if (!confirm('Cancel this interview?')) return;
  fetch('interviews.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`action=cancel&interview_id=${id}`})
  .then(r=>r.json()).then(d=>{
    if(d.ok){ const card=document.getElementById('iv-'+id); card.classList.add('status-cancelled'); card.querySelector('.buttons').remove(); card.querySelector('.mode-badge').outerHTML='<span class="badge-cancelled">Cancelled</span>'; }
    else alert('Failed.');
  });
}

function deleteInterview(btn, id) {
  if (!confirm('Delete this interview record?')) return;
  fetch('interviews.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`action=delete&interview_id=${id}`})
  .then(r=>r.json()).then(d=>{
    if(d.ok) document.getElementById('iv-'+id).remove();
    else alert('Failed.');
  });
}
</script>
</body>
</html>
