<?php
/** LINUXORA — user bookmarks (localStorage-driven). */
require_once __DIR__ . '/includes/functions.php';
?>
<nav class="breadcrumbs" aria-label="Breadcrumb">
  <a href="<?= e(url('home')) ?>">Home</a><span aria-hidden="true">/</span><span aria-current="page">Bookmarks</span>
</nav>

<section class="section">
  <h1>Your Bookmarks</h1>
  <p class="page-intro">Commands you saved while studying. Stored only in this browser via localStorage — no account needed. Bookmark any command from its page, or from search results.</p>
  <div class="cmd-grid" id="bookmarks-grid" data-empty-note="No bookmarks yet. Open any command and press “Bookmark”, or press Ctrl+K to find one."></div>
  <div class="section-head">
    <button class="btn btn-ghost btn-sm" id="clear-bookmarks" type="button" hidden>Remove all bookmarks</button>
  </div>
</section>
