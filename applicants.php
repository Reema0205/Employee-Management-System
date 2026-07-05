<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
requireLogin();

$cid = (int)$_SESSION['company_id'];

// ── AJAX: update applicant status / shortlist ─────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $db  = getDB();
    $aid = (int)($_POST['applicant_id'] ?? 0);

    if ($_POST['action'] === 'accept' || $_POST['action'] === 'reject') {
        $status = $_POST['action'] === 'accept' ? 'accepted' : 'rejected';
        $stmt   = $db->prepare("UPDATE applicants SET status=? WHERE id=? AND company_id=?");
        $stmt->bind_param('sii', $status, $aid, $cid);
        $ok = $stmt->execute(); $stmt->close();

        // Auto-shortlist on accept
        if ($ok && $status === 'accepted') {
            $s2 = $db->prepare("INSERT IGNORE INTO shortlisted (company_id,applicant_id) VALUES (?,?)");
            $s2->bind_param('ii', $cid, $aid); $s2->execute(); $s2->close();
        }
        echo json_encode(['ok' => $ok, 'status' => $status]); $db->close(); exit;
    }

    echo json_encode(['ok' => false]); $db->close(); exit;
}

// ── Load applicants ───────────────────────────────────
$db   = getDB();
$stmt = $db->prepare("
    SELECT a.*, j.title AS job_title
    FROM applicants a
    JOIN jobs j ON j.id = a.job_id
    WHERE a.company_id = ?
    ORDER BY a.applied_at DESC
");
$stmt->bind_param('i', $cid);
$stmt->execute();
$applicants = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close(); $db->close();

$activePage = 'applicants';
$pageTitle  = 'Applicants';
$showSearch = true;
$searchPlaceholder = 'Search applicant…';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Applicants – IPS Company Panel</title>
  <link rel="stylesheet" href="css/sidebar.css">
  <link rel="stylesheet" href="css/applicants.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<div class=" container">
  <?php include 'includes/sidebar.php'; ?>
  <main class="main-area">
    <?php include 'includes/topbar.php'; ?>
    <div class="page-content">

      <div class="table-box">
        <table id="applicantTable">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Email</th>
              <th>Job</th>
              <th>Experience</th>
              <th>Status</th>
              <th>Resume</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($applicants)): ?>
              <tr><td colspan="8" style="text-align:center;color:#9ca3af;padding:30px;">No applicants yet.</td></tr>
            <?php else: ?>
              <?php foreach ($applicants as $i => $ap): ?>
              <tr id="row-<?= $ap['id'] ?>">
                <td><?= $i + 1 ?></td>
                <td>
                  <div class="name-cell">
                    <img src="https://i.pravatar.cc/36?img=<?= ($i % 15) + 1 ?>" alt="">
                    <?= htmlspecialchars($ap['name']) ?>
                  </div>
                </td>
                <td><?= htmlspecialchars($ap['email']) ?></td>
                <td><?= htmlspecialchars($ap['job_title']) ?></td>
                <td><?= htmlspecialchars($ap['experience']) ?></td>
                <td>
                  <span class="badge-status <?= $ap['status'] ?>" id="badge-<?= $ap['id'] ?>">
                    <?= ucfirst($ap['status']) ?>
                  </span>
                </td>
                <td>
                  <?php if ($ap['resume']): ?>
                    <a href="uploads/<?= htmlspecialchars($ap['resume']) ?>" target="_blank">
                      <button class="resume-btn"><i class="fa-solid fa-file-pdf"></i> View</button>
                    </a>
                  <?php else: ?>
                    <button class="resume-btn" disabled style="opacity:.5;cursor:not-allowed;">
                      <i class="fa-solid fa-file-pdf"></i> N/A
                    </button>
                  <?php endif; ?>
                </td>
                <td class="action-btns">
                  <button class="accept-btn" onclick="updateStatus(<?= $ap['id'] ?>,'accept',this)">
                    <i class="fa-solid fa-check"></i> Accept
                  </button>
                  <button class="reject-btn" onclick="updateStatus(<?= $ap['id'] ?>,'reject',this)">
                    <i class="fa-solid fa-xmark"></i> Reject
                  </button>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>
  </main>
</div>

<script>
// Live search
document.getElementById('searchInput').addEventListener('keyup', function(){
  const f = this.value.toLowerCase();
  document.querySelectorAll('#applicantTable tbody tr').forEach(row => {
    row.style.display = row.children[1]?.textContent.toLowerCase().includes(f) ? '' : 'none';
  });
});

function updateStatus(id, action, btn) {
  btn.disabled = true;
  fetch('applicants.php', {
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body: new URLSearchParams({action, applicant_id: id})
  })
  .then(r => r.ok ? r.json() : Promise.reject('Network response was not ok'))
  .then(d => {
    if (d.ok) {
      const badge = document.getElementById('badge-' + id);
      badge.textContent = d.status.charAt(0).toUpperCase() + d.status.slice(1);
      badge.className   = 'badge-status ' + d.status;
      const row = document.getElementById('row-' + id);
      row.style.transition = 'background .4s';
      row.style.background = d.status === 'accepted' ? '#f0fdf4' : '#fef2f2';
      setTimeout(() => row.style.background = '', 1200);
    } else {
      alert(d.msg || 'Update failed. Try again.');
    }
  })
  .catch(() => {
    alert('Could not update status. Please try again.');
  })
  .finally(() => {
    btn.disabled = false;
  });
}
</script>
</body>
</html>
