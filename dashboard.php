<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
requireLogin();

$db  = getDB();
$cid = (int)$_SESSION['company_id'];

// Stats
$stats = [];
foreach ([
    'jobs_posted'  => "SELECT COUNT(*) c FROM jobs      WHERE company_id=? AND status='active'",
    'applicants'   => "SELECT COUNT(*) c FROM applicants WHERE company_id=?",
    'shortlisted'  => "SELECT COUNT(*) c FROM shortlisted WHERE company_id=?",
    'interviews'   => "SELECT COUNT(*) c FROM interviews  WHERE company_id=? AND status='scheduled'",
] as $key => $sql) {
    $s = $db->prepare($sql); $s->bind_param('i', $cid); $s->execute();
    $stats[$key] = $s->get_result()->fetch_assoc()['c'] ?? 0;
    $s->close();
}

// Recent applicants (last 5)
$stmt = $db->prepare("
    SELECT a.name, a.status, j.title AS job_title
    FROM applicants a
    JOIN jobs j ON j.id = a.job_id
    WHERE a.company_id = ?
    ORDER BY a.applied_at DESC
    LIMIT 5
");
$stmt->bind_param('i', $cid);
$stmt->execute();
$recentApplicants = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Monthly application counts for chart (last 6 months)
$stmt = $db->prepare("
    SELECT DATE_FORMAT(applied_at,'%b') AS mon,
           MONTH(applied_at) AS mnum,
           COUNT(*) AS total
    FROM applicants
    WHERE company_id = ?
      AND applied_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY mon, mnum
    ORDER BY mnum ASC
");
$stmt->bind_param('i', $cid);
$stmt->execute();
$chartData = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$db->close();

$activePage = 'dashboard';
$pageTitle  = 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard – IPS Company Panel</title>
  <link rel="stylesheet" href="css/sidebar.css">
  <link rel="stylesheet" href="css/dashboard.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<div class="container">
  <?php include 'includes/sidebar.php'; ?>
  <main class="main-area">
    <?php include 'includes/topbar.php'; ?>
    <div class="page-content">

      <div class="page-header">
        <h1>Welcome back, <?= htmlspecialchars(currentCompany()['name']) ?> 👋</h1>
        <p>Here's what's happening with your hiring today.</p>
      </div>

      <!-- STAT CARDS -->
      <section class="cards">
        <div class="card card-blue">
          <div class="card-icon"><i class="fa-solid fa-briefcase"></i></div>
          <div class="card-body">
            <h3>Jobs Posted</h3>
            <h2><?= $stats['jobs_posted'] ?></h2>
            <p> Active jobs</p>
          </div>
        </div>
        <div class="card card-green">
          <div class="card-icon"><i class="fa-solid fa-users"></i></div>
          <div class="card-body">
            <h3>Applicants</h3>
            <h2><?= $stats['applicants'] ?></h2>
            <p>Total applicants</p>
          </div>
        </div>
        <div class="card card-purple">
          <div class="card-icon"><i class="fa-solid fa-star"></i></div>
          <div class="card-body">
            <h3>Shortlisted</h3>
            <h2><?= $stats['shortlisted'] ?></h2>
            <p>Shortlisted candidates</p>
          </div>
        </div>
        <div class="card card-orange">
          <div class="card-icon"><i class="fa-solid fa-calendar-check"></i></div>
          <div class="card-body">
            <h3>Interviews</h3>
            <h2><?= $stats['interviews'] ?></h2>
            <p>Scheduled interviews</p>
          </div>
        </div>
      </section>

      <!-- GRID -->
      <section class="dashboard-grid">

        <!-- RECENT APPLICANTS -->
        <div class="panel applicants-panel">
          <div class="panel-header">
            <h3>Recent Applicants</h3>
            <a href="applicants.php">View all <i class="fa-solid fa-arrow-right"></i></a>
          </div>
          <?php if (empty($recentApplicants)): ?>
            <p style="color:#9ca3af;font-size:14px;">No applicants yet.</p>
          <?php else: ?>
            <?php foreach ($recentApplicants as $i => $ap): ?>
            <div class="applicant-item">
              <img src="https://i.pravatar.cc/45?img=<?= $i+1 ?>" alt="<?= htmlspecialchars($ap['name']) ?>">
              <div class="applicant-info">
                <h4><?= htmlspecialchars($ap['name']) ?></h4>
                <p><?= htmlspecialchars($ap['job_title']) ?></p>
              </div>
              <span class="status-badge <?= $ap['status'] ?>"><?= ucfirst($ap['status']) ?></span>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <!-- CHART -->
        <div class="panel chart-panel">
          <div class="panel-header">
            <h3>Applications Overview</h3>
            <span class="chart-label">Last 6 months</span>
          </div>
          <div class="chart-container" id="chartContainer">
            <?php
            $maxVal = 1;
            foreach ($chartData as $d) { if ($d['total'] > $maxVal) $maxVal = $d['total']; }
            $maxHeight = 220;
            foreach ($chartData as $idx => $d):
                $h = max(20, (int)($d['total'] / $maxVal * $maxHeight));
                $isLast = ($idx === count($chartData) - 1);
            ?>
            <div class="bar-group">
              <div class="bar <?= $isLast ? 'active-bar' : '' ?>"
                   style="height:<?= $h ?>px"
                   title="<?= htmlspecialchars($d['mon']) ?>: <?= $d['total'] ?>"></div>
              <span><?= htmlspecialchars($d['mon']) ?></span>
            </div>
            <?php endforeach; ?>
            <?php if (empty($chartData)): ?>
              <p style="color:#9ca3af;font-size:13px;margin:auto;">No data yet.</p>
            <?php endif; ?>
          </div>
        </div>

      </section>

      <div class="post-job-btn">
        <a href="post-job.php">
          <button><i class="fa-solid fa-plus"></i> Post a New Job</button>
        </a>
      </div>

    </div>
  </main>
</div>
</body>
</html>
