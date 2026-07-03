<?php
// includes/sidebar.php
// Usage: <?php $activePage = 'dashboard'; include 'includes/sidebar.php';
if (!function_exists('currentCompany')) {
    require_once __DIR__ . '/auth.php';
}
$menu = [
    ['href' => 'dashboard.php',   'icon' => 'fa-table-columns',  'label' => 'Dashboard',  'key' => 'dashboard'],
    ['href' => 'post-job.php',    'icon' => 'fa-plus',           'label' => 'Post a Job', 'key' => 'post-job'],
    ['href' => 'my-jobs.php',     'icon' => 'fa-briefcase',      'label' => 'My Jobs',    'key' => 'my-jobs'],
    ['href' => 'applicants.php',  'icon' => 'fa-users',          'label' => 'Applicants', 'key' => 'applicants'],
    ['href' => 'shortlisted.php', 'icon' => 'fa-star',           'label' => 'Shortlisted','key' => 'shortlisted'],
    ['href' => 'interviews.php',  'icon' => 'fa-calendar-check', 'label' => 'Interviews', 'key' => 'interviews'],
    ['href' => 'messages.php',    'icon' => 'fa-message',        'label' => 'Messages',   'key' => 'messages'],
    ['href' => 'settings.php',    'icon' => 'fa-gear',           'label' => 'Settings',   'key' => 'settings'],
];
$menu = is_array($menu) ? $menu : [];
$company = currentCompany();
$initials = strtoupper(substr($company['name'] ?? '', 0, 2));
?>
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-icon">IPS</div>
    <div>
      <h2>IPS</h2>
      <p>Company Panel</p>
    </div>
  </div>
  <ul class="sidebar-menu">
    <?php foreach ($menu as $item): ?>
    <li class="<?= ($activePage ?? '') === $item['key'] ? 'active' : '' ?>">
      <a href="<?= $item['href'] ?>">
        <i class="fa-solid <?= $item['icon'] ?>"></i>
        <span><?= $item['label'] ?></span>
      </a>
    </li>
    <?php endforeach; ?>
  </ul>
  <div class="sidebar-footer">
    <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a>
  </div>
</aside>

