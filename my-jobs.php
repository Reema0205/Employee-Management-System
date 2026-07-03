<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
requireLogin();

$cid = (int)$_SESSION['company_id'];

// ── AJAX: delete job ──────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $db = getDB();

    if ($_POST['action'] === 'delete') {
        $jid  = (int)($_POST['job_id'] ?? 0);
        $stmt = $db->prepare("DELETE FROM jobs WHERE id=? AND company_id=?");
        $stmt->bind_param('ii', $jid, $cid);
        echo json_encode(['ok' => $stmt->execute()]);
        $stmt->close(); $db->close(); exit;
    }

    if ($_POST['action'] === 'toggle_status') {
        $jid    = (int)($_POST['job_id'] ?? 0);
        $status = ($_POST['status'] ?? '') === 'active' ? 'closed' : 'active';
        $stmt   = $db->prepare("UPDATE jobs SET status=? WHERE id=? AND company_id=?");
        $stmt->bind_param('sii', $status, $jid, $cid);
        echo json_encode(['ok' => $stmt->execute(), 'new_status' => $status]);
        $stmt->close(); $db->close(); exit;
    }

    if ($_POST['action'] === 'edit') {
        $jid   = (int)($_POST['job_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $type  = trim($_POST['type']  ?? '');
        $loc   = trim($_POST['location']   ?? '');
        $exp   = trim($_POST['experience'] ?? '');
        $desc  = trim($_POST['description'] ?? '');
        $skills= trim($_POST['skills']     ?? '');
        $stmt  = $db->prepare("UPDATE jobs SET title=?,type=?,location=?,experience=?,description=?,skills=? WHERE id=? AND company_id=?");
        $stmt->bind_param('ssssssii', $title,$type,$loc,$exp,$desc,$skills,$jid,$cid);
        echo json_encode(['ok' => $stmt->execute()]);
        $stmt->close(); $db->close(); exit;
    }

    echo json_encode(['ok' => false]); exit;
}

// ── Load jobs ─────────────────────────────────────────
$db   = getDB();
$stmt = $db->prepare("SELECT * FROM jobs WHERE company_id=? ORDER BY created_at DESC");
$stmt->bind_param('i', $cid);
$stmt->execute();
$jobs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close(); $db->close();

$activePage = 'my-jobs';
$pageTitle  = 'My Jobs';
$showSearch = true;
$searchPlaceholder = 'Search job…';

$iconMap = ['Full Time'=>'fa-code','Internship'=>'fa-brands fa-react','Part Time'=>'fa-pen-nib','Remote'=>'fa-laptop-code'];
$colorMap= ['Full Time'=>'blue','Internship'=>'green','Part Time'=>'purple','Remote'=>'orange'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Jobs – IPS Company Panel</title>
  <link rel="stylesheet" href="css/sidebar.css">
  <link rel="stylesheet" href="css/my-jobs.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<div class="container">
  <?php include 'includes/sidebar.php'; ?>
  <main class="main-area">
    <?php include 'includes/topbar.php'; ?>
    <div class="page-content">

      <div class="jobs-container" id="jobsContainer">
        <?php if (empty($jobs)): ?>
          <p style="color:#9ca3af;grid-column:1/-1;">No jobs posted yet. <a href="post-job.php">Post one now</a>.</p>
        <?php else: ?>
          <?php foreach ($jobs as $job):
            $icon  = $iconMap[$job['type']]  ?? 'fa-briefcase';
            $color = $colorMap[$job['type']] ?? 'blue';
          ?>
          <div class="job-card" data-id="<?= $job['id'] ?>">
            <div class="job-card-header">
              <div class="job-icon <?= $color ?>"><i class="fa-solid <?= $icon ?>"></i></div>
              <span class="status <?= $job['status'] === 'active' ? 'active-job' : 'closed-job' ?>">
                <?= ucfirst($job['status']) ?>
              </span>
            </div>
            <h3><?= htmlspecialchars($job['title']) ?></h3>
            <div class="job-meta">
              <span><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($job['location']) ?></span>
              <span><i class="fa-solid fa-clock"></i> <?= htmlspecialchars($job['experience']) ?></span>
              <span><i class="fa-solid fa-briefcase"></i> <?= htmlspecialchars($job['type']) ?></span>
            </div>
            <div class="actions">
              <button class="edit-btn" onclick="openEdit(<?= htmlspecialchars(json_encode($job)) ?>)">
                <i class="fa-solid fa-pen"></i> Edit
              </button>
              <button class="delete-btn" onclick="deleteJob(this, <?= $job['id'] ?>)">
                <i class="fa-solid fa-trash"></i> Delete
              </button>
              <button class="toggle-btn" onclick="toggleStatus(this, <?= $job['id'] ?>, '<?= $job['status'] ?>')">
                <i class="fa-solid fa-toggle-<?= $job['status'] === 'active' ? 'on' : 'off' ?>"></i>
                <?= $job['status'] === 'active' ? 'Close' : 'Reopen' ?>
              </button>
            </div>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

    </div>
  </main>
</div>

<!-- Edit Modal -->
<div id="editModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;align-items:center;justify-content:center;">
  <div style="background:#fff;border-radius:16px;padding:32px;width:540px;max-width:95vw;max-height:90vh;overflow-y:auto;">
    <h3 style="margin-bottom:20px;font-size:18px;color:#111827;">Edit Job</h3>
    <input type="hidden" id="editId">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
      <div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Title</label>
        <input id="editTitle" style="width:100%;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;outline:none;"></div>
      <div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Type</label>
        <select id="editType" style="width:100%;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;outline:none;">
          <option>Full Time</option><option>Part Time</option><option>Internship</option><option>Remote</option>
        </select></div>
      <div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Location</label>
        <input id="editLocation" style="width:100%;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;outline:none;"></div>
      <div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Experience</label>
        <select id="editExperience" style="width:100%;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;outline:none;">
          <option>Fresher</option><option>1-3 Years</option><option>3-5 Years</option><option>5+ Years</option>
        </select></div>
    </div>
    <div style="margin-bottom:14px;">
      <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Description</label>
      <textarea id="editDescription" rows="4" style="width:100%;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;outline:none;resize:vertical;"></textarea>
    </div>
    <div style="margin-bottom:20px;">
      <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Skills</label>
      <input id="editSkills" style="width:100%;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;outline:none;">
    </div>
    <div style="display:flex;gap:12px;justify-content:flex-end;">
      <button onclick="closeEdit()" style="padding:10px 22px;border:1.5px solid #e5e7eb;border-radius:8px;background:#fff;cursor:pointer;font-size:14px;">Cancel</button>
      <button onclick="saveEdit()" style="padding:10px 22px;background:#18b65a;color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:14px;font-weight:600;">Save Changes</button>
    </div>
  </div>
</div>

<style>
.toggle-btn{background:#f3f4f6;color:#374151;border:1.5px solid #e5e7eb;padding:7px 14px;border-radius:8px;font-size:13px;cursor:pointer;font-weight:500;transition:background .2s;}
.toggle-btn:hover{background:#e5e7eb;}
</style>

<script>
// Search
document.getElementById('searchInput').addEventListener('keyup', function(){
  const f = this.value.toLowerCase();
  document.querySelectorAll('.job-card').forEach(c => {
    c.style.display = c.querySelector('h3').textContent.toLowerCase().includes(f) ? '' : 'none';
  });
});

function deleteJob(btn, id) {
  if (!confirm('Delete this job? All its applicants will also be removed.')) return;
  fetch('my-jobs.php', {
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body: new URLSearchParams({action:'delete', job_id:id})
  })
  .then(r => r.ok ? r.json() : Promise.reject('Network response was not ok'))
  .then(d => {
    if (d.ok) {
      btn.closest('.job-card').remove();
    } else {
      alert(d.msg || 'Could not delete. Try again.');
    }
  })
  .catch(() => alert('Could not delete. Please try again.'));
}

function toggleStatus(btn, id, current) {
  fetch('my-jobs.php', {
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body: new URLSearchParams({action:'toggle_status', job_id:id, status:current})
  })
  .then(r => r.ok ? r.json() : Promise.reject('Network response was not ok'))
  .then(d => {
    if (d.ok) {
      const badge = btn.closest('.job-card').querySelector('.status');
      badge.textContent = d.new_status === 'active' ? 'Active' : 'Closed';
      badge.className   = 'status ' + (d.new_status === 'active' ? 'active-job' : 'closed-job');
      btn.innerHTML = `<i class="fa-solid fa-toggle-${d.new_status==='active'?'on':'off'}"></i> ${d.new_status==='active'?'Close':'Reopen'}`;
      btn.setAttribute('onclick', `toggleStatus(this,${id},'${d.new_status}')`);
    } else {
      alert(d.msg || 'Could not update status. Try again.');
    }
  })
  .catch(() => alert('Could not update status. Please try again.'));
}

function openEdit(job) {
  document.getElementById('editId').value           = job.id;
  document.getElementById('editTitle').value        = job.title;
  document.getElementById('editType').value         = job.type;
  document.getElementById('editLocation').value     = job.location;
  document.getElementById('editExperience').value   = job.experience;
  document.getElementById('editDescription').value  = job.description;
  document.getElementById('editSkills').value       = job.skills || '';
  document.getElementById('editModal').style.display = 'flex';
}
function closeEdit() { document.getElementById('editModal').style.display = 'none'; }

function saveEdit() {
  const id   = document.getElementById('editId').value;
  const body = new URLSearchParams({
    action:'edit', job_id:id,
    title:       document.getElementById('editTitle').value,
    type:        document.getElementById('editType').value,
    location:    document.getElementById('editLocation').value,
    experience:  document.getElementById('editExperience').value,
    description: document.getElementById('editDescription').value,
    skills:      document.getElementById('editSkills').value,
  });
  fetch('my-jobs.php', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body})
    .then(r=>r.json()).then(d => {
      if (d.ok) { location.reload(); }
      else alert('Update failed. Try again.');
    });
}
</script>
</body>
</html>
