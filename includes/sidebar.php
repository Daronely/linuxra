<?php
/** LINUXORA — left navigation sidebar. */
require_once __DIR__ . '/functions.php';

$cur = current_page();
$curCmd  = $command['slug']  ?? (function_exists('query_slug') ? query_slug('cmd') : '');
$curCat  = $category['id']   ?? (function_exists('query_slug') ? query_slug('cat') : '');

function nav_item(string $href, string $label, string $cur, string $page, string $icon): string
{
    $active = $cur === $page ? ' aria-current="page"' : '';
    $cls = $cur === $page ? ' class="nav-link active"' : ' class="nav-link"';
    return '<a' . $cls . $active . ' href="' . e($href) . '">'
         . '<span class="nav-icon" aria-hidden="true">' . $icon . '</span>' . e($label) . '</a>';
}

$icons = [
    'home'   => '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/></svg>',
    'start'  => '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 5h16M4 12h10M4 19h7"/></svg>',
    'book'   => '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V5a2 2 0 0 1 2-2h13v18H6a2 2 0 0 1-2-2z"/><path d="M19 17H6a2 2 0 0 0-2 2"/></svg>',
    'grid'   => '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>',
    'star'   => '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="m12 3 2.7 5.8 6.3.7-4.7 4.3 1.3 6.2L12 16.9 6.4 20l1.3-6.2L3 9.5l6.3-.7z"/></svg>',
    'clock'  => '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>',
    'mark'   => '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M6 3h12v18l-6-4-6 4z"/></svg>',
    'road'   => '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M8 3h8M8 21h8M12 3v4m0 4v2m0 4v4"/><circle cx="12" cy="9" r="1.6"/><circle cx="12" cy="15" r="1.6"/></svg>',
    'info'   => '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 11v5m0-8.5v.5"/></svg>',
];
?>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-head">
    <a class="logo" href="<?= e(url('home')) ?>">
      <span class="logo-mark" aria-hidden="true">❯_</span>
      <span class="logo-text">LINUXORA</span>
    </a>
    <button class="icon-btn sidebar-close mobile-only" id="sidebar-close" aria-label="Close navigation menu">
      <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
    </button>
  </div>

  <button class="sidebar-search" id="sidebar-search" type="button" aria-label="Search commands (Ctrl+K)">
    <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
    Search commands
    <kbd>Ctrl K</kbd>
  </button>

  <nav class="sidebar-nav" aria-label="Main navigation">
    <ul class="nav-list">
      <?= nav_item(url('home'), 'Home', $cur, 'home', $icons['home']) ?>
      <?= nav_item(url('getting-started'), 'Getting Started', $cur, 'getting-started', $icons['start']) ?>
      <?= nav_item(url('fundamentals'), 'Linux Fundamentals', $cur, 'fundamentals', $icons['book']) ?>
    </ul>

    <div class="nav-section">
      <button class="nav-section-toggle" type="button" aria-expanded="true" data-nav-section="categories">
        <span class="nav-icon" aria-hidden="true"><?= $icons['grid'] ?></span>
        Command Categories
        <svg class="chev" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
      </button>
      <ul class="cat-tree" id="cat-tree" data-open="<?= ($cur === 'category' || $cur === 'command') ? '1' : '0' ?>">
        <?php foreach (get_categories() as $cat): ?>
          <?php $isOpen = ($curCat === ($cat['id'] ?? '')); ?>
          <li class="cat-item<?= $isOpen ? ' open' : '' ?>">
            <button class="cat-toggle" type="button" aria-expanded="<?= $isOpen ? 'true' : 'false' ?>">
              <span class="cat-dot cat-dot-<?= e($cat['color'] ?? 'cyan') ?>" aria-hidden="true"></span>
              <span class="cat-label"><?= e($cat['name'] ?? '') ?></span>
              <span class="cat-count"><?= (int) ($cat['commandCount'] ?? 0) ?></span>
              <svg class="chev" viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6"/></svg>
            </button>
            <ul class="sub-list">
              <li><a class="sub-link<?= ($curCat === $cat['id'] && $curCmd === '') ? ' active' : '' ?>" href="<?= e(category_url((string) $cat['id'])) ?>">Overview</a></li>
              <?php foreach (($cat['subcategories'] ?? []) as $sub): ?>
                <?php $subName = is_array($sub) ? ($sub['name'] ?? '') : (string) $sub; ?>
                <li><a class="sub-link" href="<?= e(category_url((string) $cat['id'])) ?>#sub-<?= e(slugify($subName)) ?>"><?= e($subName) ?></a></li>
              <?php endforeach; ?>
            </ul>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <ul class="nav-list">
      <?= nav_item(url('popular'), 'Popular Commands', $cur, 'popular', $icons['star']) ?>
      <?= nav_item(url('recent'), 'Recently Added', $cur, 'recent', $icons['clock']) ?>
      <?= nav_item(url('bookmarks'), 'Bookmarks', $cur, 'bookmarks', $icons['mark']) ?>
      <?= nav_item(url('learning'), 'Learning Roadmap', $cur, 'learning', $icons['road']) ?>
      <?= nav_item(url('tools'), 'Educational Tools', $cur, 'tools', $icons['grid']) ?>
      <?= nav_item(url('about'), 'About', $cur, 'about', $icons['info']) ?>
    </ul>
  </nav>

  <div class="sidebar-foot">
    <div class="sim-note">
      <strong>Learn safely.</strong>
      The terminal on this site is a browser simulation — nothing you type ever runs on a server.
    </div>
  </div>
</aside>
<div class="sidebar-backdrop" id="sidebar-backdrop" hidden></div>
