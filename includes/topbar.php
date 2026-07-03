<?php
// includes/topbar.php
// Usage: <?php $pageTitle = 'Dashboard'; include 'includes/topbar.php';
if (!function_exists('currentCompany')) {
    require_once __DIR__ . '/auth.php';
}
$company  = currentCompany();
$initials = strtoupper(substr($company['name'] ?? '', 0, 2));
?>
<header class="topbar">
  <h2><?= htmlspecialchars($pageTitle ?? 'Panel') ?></h2>
  <div class="topbar-right">
    <?php if (!empty($showSearch)): ?>
    <input type="text" id="searchInput" class="search-input" placeholder="<?= htmlspecialchars($searchPlaceholder ?? 'Search…') ?>">
    <?php endif; ?>
    <i class="fa-regular fa-bell bell-icon"></i>
    <div class="topbar-profile">
      <div class="avatar-initials"><?= htmlspecialchars($initials) ?></div>
      <div>
        <h4><?= htmlspecialchars($company['name'] ?? '') ?></h4>
        <p>Company</p>
      </div>
    </div>
  </div>
</header>

