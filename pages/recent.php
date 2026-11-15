<?php
/** LINUXORA — recently added + recently viewed. */
require_once __DIR__ . '/includes/functions.php';
$recent = commands_for_slugs(recent_slugs());
?>
<nav class="breadcrumbs" aria-label="Breadcrumb">
  <a href="<?= e(url('home')) ?>">Home</a><span aria-hidden="true">/</span><span aria-current="page">Recently Added</span>
</nav>

<section class="section">
  <h1>Recently Added</h1>
  <p class="page-intro">The newest documentation in the encyclopedia — currently deep-dives into tracing, namespaces, and kernel tooling.</p>
  <div class="cmd-grid">
    <?php foreach ($recent as $cmd) { echo command_card($cmd); } ?>
  </div>
</section>

<section class="section" id="recently-viewed">
  <h2>Recently viewed by you</h2>
  <p class="page-intro">Tracked privately in your browser (localStorage). Nothing is sent to any server.</p>
  <div class="cmd-grid" id="recently-viewed-grid" data-empty-note="Commands you open will appear here."></div>
</section>
