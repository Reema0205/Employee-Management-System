<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
requireLogin();

$cid = (int)$_SESSION['company_id'];

// ── AJAX ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $db  = getDB();
    $aid = (int)($_POST['applicant_id'] ?? 0);

    if ($_POST['action'] === 'remove') {
        $stmt = $db->prepare("DELETE FROM shortlisted WHERE company_id=? AND applicant_id=?");
        $stmt->bind_param('ii', $cid, $aid);
        echo json_encode(['ok' => $stmt->execute()]);
        $stmt->close(); $db->close(); exit;
    }

    if ($_POST['action'] === 'schedule') {
        // Just redirect confirmation; real scheduling happens on interviews.php
        echo json_encode(['ok' => true, 'redirect' => 'interviews.php?add=' . $aid]);
        $db->close(); exit;
    }

    echo json_encode(['ok' => false]); $db->close(); exit;
}

// ── Load shortlisted ──────────────────────────────────
$db   = getDB();
$stmt = $db->prepare("
    SELECT a.id, a.name, a.email, a.experience, j.title AS job_title
    FROM shortlisted s
    JOIN applicants a ON a.id = s.applicant_id
    JOIN jobs       j ON j.id = a.job_id
    WHERE s.company_id = ?
    ORDER BY s.added_at DESC
");
$stmt->bind_param('i', $cid);
$stmt->execute();
$candidates = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close(); $db->close();

$activePage = 'shortlisted';
$pageTitle  = 'Shortlisted Candidates';
$showSearch = true;
$searchPlaceholder = 'Search candidate…';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title> Shortlisted – IPS Company Panel</title>
  <link rel="stylesheet" href="css/sidebar.css">
  <link rel="stylesheet" href="css/shortlisted.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<div class="container">
  <?php include 'includes/sidebar.php'; ?>
  <main class="main-area">
    <?php include 'includes/topbar.php'; ?>
    <div class="page-content">

      <div class="cards" id="shortlistedCards">
        <?php if (empty($candidates)): ?>
          <p style="color:#9ca3af;grid-column:1/-1;">No shortlisted candidates yet. Accept applicants to shortlist them automatically.</p>
        <?php else: ?>
          <?php foreach ($candidates as $i => $c): ?>
          <div class="candidate-card" id="card-<?= $c['id'] ?>">
            <div class="card-avatar">
              <img src="https://i.pravatar.cc/150?img=<?= ($i % 15) + 1 ?>" alt="<?= htmlspecialchars($c['name']) ?>">
            </div>
            <h3><?= htmlspecialchars($c['name']) ?></h3>
            <p class="role"><?= htmlspecialchars($c['job_title']) ?></p>
            <p class="exp"><i class="fa-solid fa-clock"></i> <?= htmlspecialchars($c['experience']) ?> Experience</p>
            <span class="badge"><i class="fa-solid fa-star"></i> Shortlisted</span>
            <div class="buttons">
              <button class="interview-btn" onclick="scheduleInterview(<?= $c['id'] ?>)">
                <i class="fa-solid fa-calendar-check"></i> Schedule Interview
              </button>
              <button class="remove-btn" onclick="removeCandidate(this, <?= $c['id'] ?>)">
                <i class="fa-solid fa-trash"></i> Remove
              </button>
            </div>
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
  document.querySelectorAll('.candidate-card').forEach(c => {
    c.style.display = c.querySelector('h3').textContent.toLowerCase().includes(f) ? '' : 'none';
  });
});

function removeCandidate(btn, id) {
  if (!confirm('Remove this candidate from shortlist?')) return;
  fetch('shortlisted.php', {
    method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body: new URLSearchParams({action:'remove', applicant_id:id})
  })
  .then(r => r.ok ? r.json() : Promise.reject('Network response was not ok'))
  .then(d => {
    if (d.ok) document.getElementById('card-' + id).remove();
    else alert(d.msg || 'Could not remove. Try again.');
  })
  .catch(() => alert('Could not remove candidate. Please try again.'));
}

function scheduleInterview(id) {
  window.location.href = 'interviews.php?add=' + id;
}
</script>
</body>
</html>
