<?php
/** LINUXORA — learning roadmap. */
require_once __DIR__ . '/includes/functions.php';
$paths = load_json('learning-paths.json');
?>
<nav class="breadcrumbs" aria-label="Breadcrumb">
  <a href="<?= e(url('home')) ?>">Home</a><span aria-hidden="true">/</span><span aria-current="page">Learning Roadmap</span>
</nav>

<section class="section">
  <h1>Learning Roadmap</h1>
  <p class="page-intro">Three structured paths from first command to kernel internals. Your progress is saved in this browser as you open command pages — check off stages by visiting their commands.</p>
</section>

<?php foreach ($paths as $key => $path): ?>
<?php
$allCmds = array_merge(...array_map(fn ($s) => $s['commands'] ?? [], $path['steps'] ?? []));
?>
<section class="section path-section" id="path-<?= e($key) ?>">
  <div class="section-head">
    <h2><span class="path-level path-level-<?= e($key) ?>"><?= e(ucfirst($key)) ?></span> <?= e($path['title'] ?? '') ?></h2>
    <span class="path-progress-label" data-path-progress="<?= e($key) ?>">0 / <?= count($allCmds) ?> commands</span>
  </div>
  <p class="page-intro"><?= e($path['description'] ?? '') ?></p>

  <ol class="path-steps">
    <?php foreach (($path['steps'] ?? []) as $i => $step): ?>
    <li class="path-step">
      <div class="path-step-head">
        <span class="path-step-num"><?= $i + 1 ?></span>
        <h3><?= e($step['title'] ?? '') ?></h3>
        <span class="path-step-progress" data-step-progress aria-live="polite"></span>
      </div>
      <p><?= e($step['body'] ?? '') ?></p>
      <div class="path-cmds">
        <?php foreach (($step['commands'] ?? []) as $slug): $cmd = get_command((string) $slug); if ($cmd === null) { continue; } ?>
        <a class="path-cmd" href="<?= e(command_url((string) $cmd['slug'])) ?>" data-path-cmd="<?= e($key) ?>">
          <code><?= e($cmd['name'] ?? $cmd['slug']) ?></code>
          <span class="path-cmd-desc"><?= e($cmd['description'] ?? '') ?></span>
        </a>
        <?php endforeach; ?>
      </div>
    </li>
    <?php endforeach; ?>
  </ol>
</section>
<?php endforeach; ?>
