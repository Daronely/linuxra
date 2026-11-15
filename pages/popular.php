<?php
/** LINUXORA — popular commands. */
require_once __DIR__ . '/includes/functions.php';
$popular = commands_for_slugs(popular_slugs());
?>
<nav class="breadcrumbs" aria-label="Breadcrumb">
  <a href="<?= e(url('home')) ?>">Home</a><span aria-hidden="true">/</span><span aria-current="page">Popular Commands</span>
</nav>

<section class="section">
  <h1>Popular Commands</h1>
  <p class="page-intro">The commands used daily on almost every Linux system — from listing files to managing services. Master these twelve and you can navigate and operate any machine.</p>
  <div class="cmd-grid">
    <?php foreach ($popular as $cmd) { echo command_card($cmd); } ?>
  </div>
</section>

<section class="section" id="by-task">
  <h2>By task</h2>
  <div class="table-wrap">
    <table class="flag-table">
      <thead><tr><th scope="col">I want to…</th><th scope="col">Use</th></tr></thead>
      <tbody>
        <tr><td>Copy files</td><td><a href="<?= e(command_url('cp')) ?>"><code>cp</code></a>, <a href="<?= e(command_url('rsync')) ?>"><code>rsync</code></a>, <a href="<?= e(command_url('scp')) ?>"><code>scp</code></a></td></tr>
        <tr><td>Delete a folder</td><td><a href="<?= e(command_url('rm')) ?>"><code>rm -r</code></a>, <a href="<?= e(command_url('rmdir')) ?>"><code>rmdir</code></a> (empty only)</td></tr>
        <tr><td>Check memory</td><td><a href="<?= e(command_url('free')) ?>"><code>free</code></a>, <a href="<?= e(command_url('top')) ?>"><code>top</code></a>, <a href="<?= e(command_url('htop')) ?>"><code>htop</code></a></td></tr>
        <tr><td>Find text in files</td><td><a href="<?= e(command_url('grep')) ?>"><code>grep</code></a></td></tr>
        <tr><td>Check my IP</td><td><a href="<?= e(command_url('ip')) ?>"><code>ip</code></a>, <a href="<?= e(command_url('ifconfig')) ?>"><code>ifconfig</code></a></td></tr>
        <tr><td>Restart a service</td><td><a href="<?= e(command_url('systemctl')) ?>"><code>systemctl restart …</code></a></td></tr>
        <tr><td>Extract a .tar.gz</td><td><a href="<?= e(command_url('tar')) ?>"><code>tar -xzf</code></a></td></tr>
      </tbody>
    </table>
  </div>
</section>
