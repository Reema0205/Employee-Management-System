<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
requireLogin();

$cid = (int)$_SESSION['company_id'];

// ── AJAX: send message ────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $db = getDB();

    if ($_POST['action'] === 'send') {
        $aid = (int)($_POST['applicant_id'] ?? 0);
        $msg = trim($_POST['message'] ?? '');
        if (!$aid || $msg === '') { echo json_encode(['ok'=>false]); exit; }
        $stmt = $db->prepare("INSERT INTO messages (company_id,applicant_id,sender,message) VALUES (?,?,'company',?)");
        $stmt->bind_param('iis', $cid, $aid, $msg);
        $ok = $stmt->execute();
        $newId = $db->insert_id;
        $stmt->close();
        echo json_encode(['ok'=>$ok,'id'=>$newId,'time'=>date('h:i A')]);
        $db->close(); exit;
    }

    if ($_POST['action'] === 'load') {
        $aid  = (int)($_POST['applicant_id'] ?? 0);
        $stmt = $db->prepare("SELECT sender,message,sent_at FROM messages WHERE company_id=? AND applicant_id=? ORDER BY sent_at ASC");
        $stmt->bind_param('ii', $cid, $aid);
        $stmt->execute();
        $msgs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close(); $db->close();
        echo json_encode(['ok'=>true,'messages'=>$msgs]); exit;
    }

    echo json_encode(['ok'=>false]); $db->close(); exit;
}

// ── Load contacts (distinct applicants who have a message thread) ──
$db   = getDB();
$stmt = $db->prepare("
    SELECT DISTINCT a.id, a.name, j.title AS job_title
    FROM applicants a
    JOIN jobs j ON j.id = a.job_id
    WHERE a.company_id = ?
    ORDER BY a.name
");
$stmt->bind_param('i', $cid);
$stmt->execute();
$contacts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Load initial messages for first contact
$firstMessages = [];
$firstContact  = $contacts[0] ?? null;
if ($firstContact) {
    $s2 = $db->prepare("SELECT sender,message,sent_at FROM messages WHERE company_id=? AND applicant_id=? ORDER BY sent_at ASC");
    $s2->bind_param('ii', $cid, $firstContact['id']);
    $s2->execute();
    $firstMessages = $s2->get_result()->fetch_all(MYSQLI_ASSOC);
    $s2->close();
}
$db->close();

$activePage = 'messages';
$pageTitle  = 'Messages';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Messages – IPS Company Panel</title>
  <link rel="stylesheet" href="css/sidebar.css">
  <link rel="stylesheet" href="css/messages.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<div class="container">
  <?php include 'includes/sidebar.php'; ?>
  <main class="main-area">
    <?php include 'includes/topbar.php'; ?>
    <div class="page-content messages-page">

      <div class="chat-container">

        <!-- Contacts -->
        <div class="contacts">
          <div class="contacts-header">
            <h3>Applicants</h3>
            <span class="contact-count"><?= count($contacts) ?></span>
          </div>
          <?php foreach ($contacts as $i => $c): ?>
          <div class="contact <?= $i === 0 ? 'active-contact' : '' ?>"
               onclick="switchContact(this, <?= $c['id'] ?>, '<?= htmlspecialchars(addslashes($c['name'])) ?>', <?= $i + 1 ?>)">
            <img src="https://i.pravatar.cc/50?img=<?= ($i % 15) + 1 ?>" alt="<?= htmlspecialchars($c['name']) ?>">
            <div class="contact-info">
              <span class="contact-name"><?= htmlspecialchars($c['name']) ?></span>
              <span class="contact-preview"><?= htmlspecialchars($c['job_title']) ?></span>
            </div>
          </div>
          <?php endforeach; ?>
          <?php if (empty($contacts)): ?>
            <p style="padding:16px;color:#9ca3af;font-size:13px;">No applicants yet.</p>
          <?php endif; ?>
        </div>

        <!-- Chat -->
        <div class="chat-box">
          <div class="chat-header">
            <img src="https://i.pravatar.cc/40?img=1" id="chatAvatar" alt="">
            <div>
              <h4 id="chatName"><?= htmlspecialchars($firstContact['name'] ?? 'Select a contact') ?></h4>
              <p class="online-status"><span class="dot"></span>Online</p>
            </div>
          </div>

          <div class="messages" id="messages">
            <?php foreach ($firstMessages as $m): ?>
            <div class="message <?= $m['sender'] === 'company' ? 'sent' : 'received' ?>">
              <?= htmlspecialchars($m['message']) ?>
            </div>
            <?php endforeach; ?>
            <?php if (empty($firstMessages) && $firstContact): ?>
              <p style="text-align:center;color:#9ca3af;font-size:13px;margin:20px 0;">No messages yet. Start the conversation!</p>
            <?php endif; ?>
          </div>

          <div class="message-input">
            <input type="text" id="messageText" placeholder="Type your message…"
                   <?= !$firstContact ? 'disabled' : '' ?>>
            <button id="sendBtn" <?= !$firstContact ? 'disabled' : '' ?>>
              <i class="fa-solid fa-paper-plane"></i>
            </button>
          </div>
        </div>

      </div>
    </div>
  </main>
</div>

<script>
let currentApplicantId = <?= $firstContact ? $firstContact['id'] : 0 ?>;

document.getElementById('sendBtn').addEventListener('click', sendMessage);
document.getElementById('messageText').addEventListener('keydown', function(e){
  if (e.key === 'Enter') sendMessage();
});

function sendMessage() {
  const text = document.getElementById('messageText').value.trim();
  if (!text || !currentApplicantId) return;

  fetch('messages.php',{
    method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:`action=send&applicant_id=${currentApplicantId}&message=${encodeURIComponent(text)}`
  }).then(r=>r.json()).then(d=>{
    if(d.ok){
      appendMessage('sent', text);
      document.getElementById('messageText').value='';
    }
  });
}

function appendMessage(type, text) {
  const box = document.getElementById('messages');
  const div = document.createElement('div');
  div.className = 'message ' + type;
  div.textContent = text;
  box.appendChild(div);
  box.scrollTop = box.scrollHeight;
}

function switchContact(el, id, name, imgIdx) {
  document.querySelectorAll('.contact').forEach(c => c.classList.remove('active-contact'));
  el.classList.add('active-contact');
  currentApplicantId = id;
  document.getElementById('chatName').textContent = name;
  document.getElementById('chatAvatar').src = `https://i.pravatar.cc/40?img=${imgIdx}`;
  document.getElementById('messageText').disabled = false;
  document.getElementById('sendBtn').disabled = false;

  // Load messages from DB
  fetch('messages.php',{
    method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:`action=load&applicant_id=${id}`
  }).then(r=>r.json()).then(d=>{
    const box = document.getElementById('messages');
    box.innerHTML = '';
    if(d.messages && d.messages.length){
      d.messages.forEach(m=>{
        appendMessage(m.sender==='company'?'sent':'received', m.message);
      });
    } else {
      box.innerHTML='<p style="text-align:center;color:#9ca3af;font-size:13px;margin:20px 0;">No messages yet.</p>';
    }
  });
}

// Scroll to bottom on load
const msgBox = document.getElementById('messages');
msgBox.scrollTop = msgBox.scrollHeight;
</script>
</body>
</html>
