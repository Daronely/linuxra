<?php
/** LINUXORA — single command documentation page. */
require_once __DIR__ . '/includes/functions.php';

if ($command === null) {
    ?>
    <section class="section">
      <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="<?= e(url('home')) ?>">Home</a><span aria-hidden="true">/</span><span aria-current="page">Not found</span>
      </nav>
      <h1>Command not found</h1>
      <p>The command documentation you requested does not exist yet. Try searching with <kbd>Ctrl</kbd>+<kbd>K</kbd>.</p>
      <p><a class="btn btn-primary" href="<?= e(url('categories')) ?>">Browse all categories</a></p>
    </section>
    <?php
    return;
}

require __DIR__ . '/includes/command-view.php';
