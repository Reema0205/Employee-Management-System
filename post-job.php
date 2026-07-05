<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
requireLogin();

$cid     = (int)$_SESSION['company_id'];
$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title']       ?? '');
    $type        = trim($_POST['type']        ?? '');
    $location    = trim($_POST['location']    ?? '');
    $experience  = trim($_POST['experience']  ?? '');
    $description = trim($_POST['description'] ?? '');
    $skills      = trim($_POST['skills']      ?? '');

    if (!$title || !$type || !$location || !$experience || !$description) {
        $error = 'Please fill in all required fields.';
    } else {
        $db   = getDB();
        $stmt = $db->prepare("INSERT INTO jobs (company_id,title,type,location,experience,description,skills) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param('issssss', $cid, $title, $type, $location, $experience, $description, $skills);
        if ($stmt->execute()) {
            $success = 'Job published successfully!';
        } else {
            $error = 'Failed to publish job. Please try again.';
        }
        $stmt->close(); $db->close();
    }
}

$activePage = 'post-job';
$pageTitle  = 'Post a Job';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Post a Job – IPS Company Panel</title>
  <link rel="stylesheet" href="css/sidebar.css">
  <link rel="stylesheet" href="css/post-job.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<div class="container">
  <?php include 'includes/sidebar.php'; ?>
  <main class="main-area">
    <?php include 'includes/topbar.php'; ?>
    <div class="page-content">

      <div class="form-card">
        <div class="form-card-header">
          <i class="fa-solid fa-briefcase"></i>
          <div>
            <h2>Post a New Job</h2>
            <p> Fill in the details below to publish your job listing.</p>
          </div>
        </div>

        <?php if ($success): ?>
          <div class="alert-success"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
          <div class="alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="post-job.php">

          <div class="form-row-2">
            <div class="form-group">
              <label><i class="fa-solid fa-heading"></i> Job Title <span class="req">*</span></label>
              <input type="text" name="title" placeholder="e.g. Frontend Developer Intern"
                     value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label><i class="fa-solid fa-list"></i> Job Type <span class="req">*</span></label>
              <select name="type">
                <option value="">Select Job Type</option>
                <?php foreach (['Full Time','Part Time','Internship','Remote'] as $t): ?>
                <option value="<?= $t ?>" <?= (($_POST['type'] ?? '') === $t) ? 'selected' : '' ?>><?= $t ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-row-2">
            <div class="form-group">
              <label><i class="fa-solid fa-location-dot"></i> Location <span class="req">*</span></label>
              <input type="text" name="location" placeholder="e.g. New York / Remote"
                     value="<?= htmlspecialchars($_POST['location'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label><i class="fa-solid fa-chart-line"></i> Experience Level <span class="req">*</span></label>
              <select name="experience">
                <option value="">Select Experience</option>
                <?php foreach (['Fresher','1-3 Years','3-5 Years','5+ Years'] as $e): ?>
                <option value="<?= $e ?>" <?= (($_POST['experience'] ?? '') === $e) ? 'selected' : '' ?>><?= $e ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label><i class="fa-solid fa-align-left"></i> Job Description <span class="req">*</span></label>
            <textarea name="description" placeholder="Describe the role, responsibilities, and requirements…"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
          </div>

          <div class="form-group">
            <label><i class="fa-solid fa-code"></i> Skills Required</label>
            <input type="text" name="skills" placeholder="e.g. HTML, CSS, JavaScript, React"
                   value="<?= htmlspecialchars($_POST['skills'] ?? '') ?>">
          </div>

          <div class="form-actions">
            <a href="my-jobs.php"><button type="button" class="cancel-btn"><i class="fa-solid fa-xmark"></i> Cancel</button></a>
            <button type="submit" class="publish-btn"><i class="fa-solid fa-paper-plane"></i> Publish Job</button>
          </div>

        </form>
      </div>

    </div>
  </main>
</div>
<style>
.alert-success,.alert-error{padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:14px;font-weight:500;display:flex;align-items:center;gap:10px;}
.alert-success{background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;}
.alert-error{background:#fef2f2;border:1px solid #fecaca;color:#dc2626;}
</style>
</body>
</html>
