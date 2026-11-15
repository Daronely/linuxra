<?php
/** LINUXORA — single category page with concepts, subcategories, and commands. */
require_once __DIR__ . '/includes/functions.php';

if ($category === null) {
    ?>
    <section class="section">
      <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="<?= e(url('home')) ?>">Home</a><span aria-hidden="true">/</span><span aria-current="page">Not found</span>
      </nav>
      <h1>Category not found</h1>
      <p><a class="btn btn-primary" href="<?= e(url('categories')) ?>">Browse all categories</a></p>
    </section>
    <?php
    return;
}

$cat      = $category;
$commands = commands_for_slugs(array_merge(
    ...array_map(fn ($s) => array_column($s['commands'] ?? [], 'slug'), $cat['subcategories'] ?? [])
));
$bySlug = [];
foreach ($commands as $c) { $bySlug[$c['slug']] = $c; }
?>
<nav class="breadcrumbs" aria-label="Breadcrumb">
  <a href="<?= e(url('home')) ?>">Home</a>
  <span aria-hidden="true">/</span>
  <a href="<?= e(url('categories')) ?>">Categories</a>
  <span aria-hidden="true">/</span>
  <span aria-current="page"><?= e($cat['name'] ?? '') ?></span>
</nav>

<section class="section category-hero">
  <div class="category-hero-head">
    <span class="cat-num-big"><?= e($cat['num'] ?? '') ?></span>
    <div>
      <h1><?= e($cat['name'] ?? '') ?></h1>
      <p class="page-intro"><?= e($cat['short'] ?? '') ?></p>
    </div>
  </div>
  <?php foreach (($cat['intro'] ?? []) as $para): ?>
    <p class="category-intro"><?= e($para) ?></p>
  <?php endforeach; ?>
</section>

<?php if (!empty($cat['concepts'])): ?>
<section class="section" id="concepts">
  <h2>Core concepts</h2>
  <div class="concept-grid">
    <?php foreach ($cat['concepts'] as $i => $concept): ?>
    <article class="concept-card" id="concept-<?= $i + 1 ?>">
      <h3><?= e($concept['title'] ?? '') ?></h3>
      <p><?= e($concept['body'] ?? '') ?></p>
    </article>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<?php foreach (($cat['subcategories'] ?? []) as $sub): ?>
<?php $subName = is_array($sub) ? ($sub['name'] ?? '') : (string) $sub; ?>
<section class="section" id="sub-<?= e(slugify($subName)) ?>">
  <div class="section-head">
    <h2><?= e($subName) ?></h2>
  </div>
  <div class="cmd-grid">
    <?php
    foreach (($sub['commands'] ?? []) as $entry) {
        $slug = is_array($entry) ? ($entry['slug'] ?? '') : (string) $entry;
        if (isset($bySlug[$slug])) { echo command_card($bySlug[$slug]); }
    }
    ?>
  </div>
</section>
<?php endforeach; ?>
