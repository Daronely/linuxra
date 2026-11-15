<?php
/** LINUXORA — all categories. */
require_once __DIR__ . '/includes/functions.php';
$stats = get_stats();
?>
<nav class="breadcrumbs" aria-label="Breadcrumb">
  <a href="<?= e(url('home')) ?>">Home</a><span aria-hidden="true">/</span><span aria-current="page">Categories</span>
</nav>

<section class="section">
  <h1>Command Categories</h1>
  <p class="page-intro">
    The full catalog: <strong><?= (int) $stats['commands'] ?></strong> commands organized into
    <strong><?= count(get_categories()) ?></strong> categories and
    <strong><?= (int) $stats['subcategories'] ?></strong> subcategories. Every command page documents
    syntax, flags, examples with expected output, mistakes, warnings, and distribution notes.
  </p>

  <div class="cat-grid wide">
    <?php foreach (get_categories() as $cat): ?>
    <a class="cat-card" href="<?= e(category_url((string) $cat['id'])) ?>">
      <span class="cat-dot cat-dot-<?= e($cat['color'] ?? 'cyan') ?>" aria-hidden="true"></span>
      <div class="cat-card-body">
        <h3><span class="cat-num"><?= e($cat['num'] ?? '') ?></span> <?= e($cat['name'] ?? '') ?></h3>
        <p><?= e($cat['short'] ?? '') ?></p>
        <div class="cat-subs">
          <?php foreach (array_slice($cat['subcategories'] ?? [], 0, 4) as $sub): ?>
            <span class="tag"><?= e(is_array($sub) ? ($sub['name'] ?? '') : $sub) ?></span>
          <?php endforeach; ?>
          <?php if (count($cat['subcategories'] ?? []) > 4): ?>
            <span class="tag more">+<?= count($cat['subcategories']) - 4 ?> more</span>
          <?php endif; ?>
        </div>
      </div>
      <span class="cat-card-count"><?= (int) ($cat['commandCount'] ?? 0) ?> cmds</span>
    </a>
    <?php endforeach; ?>
  </div>
</section>
