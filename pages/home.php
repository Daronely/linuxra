<?php
/** LINUXORA — homepage. */
require_once __DIR__ . '/includes/functions.php';
$stats    = get_stats();
$popular  = commands_for_slugs(popular_slugs());
$recent   = commands_for_slugs(recent_slugs());
$daily    = daily_command();
$paths    = load_json('learning-paths.json');
?>
<section class="hero">
  <div class="hero-inner">
    <div class="hero-badge">
      <span class="pulse-dot" aria-hidden="true"></span>
      <?= (int) ($stats['commands'] ?? 0) ?> commands · <?= (int) ($stats['examples'] ?? 0) ?> examples · <?= (int) ($stats['flags'] ?? 0) ?> flags documented
    </div>
    <h1 class="hero-title">Master Linux, <span class="grad-text">One Command</span> at a Time.</h1>
    <p class="hero-sub">Explore Linux commands, understand your system, and become confident in the terminal.</p>

    <div class="hero-search">
      <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
      <input type="search" id="home-search" placeholder='Try "copy files", "check memory", "network ip"…' aria-label="Search Linux commands" autocomplete="off" spellcheck="false">
      <kbd>Ctrl K</kbd>
    </div>
    <div class="hero-search-results" id="home-search-results" hidden></div>

    <div class="hero-actions">
      <a class="btn btn-primary" href="<?= e(url('categories')) ?>">Explore Categories</a>
      <a class="btn btn-ghost" href="<?= e(url('getting-started')) ?>">Start Learning</a>
      <button class="btn btn-ghost" id="random-command" type="button">Surprise me</button>
    </div>
  </div>

  <div class="hero-terminal" aria-label="Interactive terminal preview">
    <div class="code-block terminal tall" data-terminal-preview>
      <div class="code-head">
        <span class="code-dots" aria-hidden="true"><i></i><i></i><i></i></span>
        <span class="code-lang">simulated terminal — safe to try</span>
        <button class="term-reset" id="term-reset" type="button" hidden>Reset</button>
      </div>
      <div class="term-body" id="term-body" data-term-home="1"></div>
      <div class="term-input-row">
        <span class="prompt" aria-hidden="true">user@linuxora:~$</span>
        <input class="term-input" id="term-input" type="text" autocomplete="off" autocapitalize="off" spellcheck="false" aria-label="Terminal command input (simulated)">
      </div>
    </div>
    <p class="term-note">This is an educational simulation running entirely in your browser with a virtual filesystem. Nothing is executed on any server.</p>
  </div>
</section>

<section class="section" id="daily">
  <div class="section-head">
    <h2>Command of the day</h2>
    <button class="btn btn-ghost btn-sm" id="random-command-2" type="button">Random command</button>
  </div>
  <?php if ($daily !== []): ?>
  <a class="daily-card" href="<?= e(command_url((string) $daily['slug'])) ?>">
    <div class="daily-main">
      <code class="daily-name"><?= e($daily['name'] ?? $daily['slug']) ?></code>
      <p><?= e($daily['desc'] ?? '') ?></p>
    </div>
    <div class="daily-side">
      <?= difficulty_badge((string) ($daily['difficulty'] ?? 'beginner')) ?>
      <span class="daily-cat"><?= e(category_name((string) ($daily['category'] ?? ''))) ?></span>
    </div>
  </a>
  <?php endif; ?>
</section>

<section class="section" id="popular">
  <div class="section-head">
    <h2>Popular commands</h2>
    <a class="see-all" href="<?= e(url('popular')) ?>">See all →</a>
  </div>
  <div class="cmd-grid">
    <?php foreach ($popular as $cmd) { echo command_card($cmd); } ?>
  </div>
</section>

<section class="section" id="categories-overview">
  <div class="section-head">
    <h2>Browse by category</h2>
    <a class="see-all" href="<?= e(url('categories')) ?>">All <?= count(get_categories()) ?> categories →</a>
  </div>
  <div class="cat-grid">
    <?php foreach (get_categories() as $cat): ?>
    <a class="cat-card" href="<?= e(category_url((string) $cat['id'])) ?>">
      <span class="cat-dot cat-dot-<?= e($cat['color'] ?? 'cyan') ?>" aria-hidden="true"></span>
      <div class="cat-card-body">
        <h3><?= e($cat['name'] ?? '') ?></h3>
        <p><?= e($cat['short'] ?? '') ?></p>
      </div>
      <span class="cat-card-count"><?= (int) ($cat['commandCount'] ?? 0) ?> cmds</span>
    </a>
    <?php endforeach; ?>
  </div>
</section>

<section class="section" id="learning-paths">
  <div class="section-head">
    <h2>Learning paths</h2>
    <a class="see-all" href="<?= e(url('learning')) ?>">Full roadmap →</a>
  </div>
  <div class="path-grid">
    <?php foreach ($paths as $key => $path): ?>
    <a class="path-card path-<?= e($key) ?>" href="<?= e(url('learning')) ?>#path-<?= e($key) ?>">
      <span class="path-level"><?= e(ucfirst($key)) ?></span>
      <h3><?= e($path['title'] ?? '') ?></h3>
      <p><?= e($path['description'] ?? '') ?></p>
      <span class="path-meta"><?= count($path['steps'] ?? []) ?> stages · <?= count(array_merge(...array_map(fn ($s) => $s['commands'] ?? [], $path['steps'] ?? []))) ?> commands</span>
    </a>
    <?php endforeach; ?>
  </div>
</section>

<section class="section" id="recently-added">
  <div class="section-head">
    <h2>Recently added</h2>
    <a class="see-all" href="<?= e(url('recent')) ?>">See all →</a>
  </div>
  <div class="cmd-grid">
    <?php foreach ($recent as $cmd) { echo command_card($cmd); } ?>
  </div>
</section>

<section class="section" id="tools-preview">
  <div class="section-head">
    <h2>Educational tools</h2>
    <a class="see-all" href="<?= e(url('tools')) ?>">Open tools →</a>
  </div>
  <div class="tool-grid">
    <a class="tool-card" href="<?= e(url('tools')) ?>#permissions-calculator">
      <h3>Permissions Calculator</h3>
      <p>Toggle rwx bits for owner, group, and others; get the octal value and what it means.</p>
    </a>
    <a class="tool-card" href="<?= e(url('tools')) ?>#cron-builder">
      <h3>Cron Expression Builder</h3>
      <p>Build cron schedules visually and understand every field of the expression.</p>
    </a>
    <a class="tool-card" href="<?= e(url('tools')) ?>#size-converter">
      <h3>File Size Converter</h3>
      <p>Convert between B, KB, MB, GB, TB — with binary (KiB) vs decimal (kB) explained.</p>
    </a>
    <a class="tool-card" href="<?= e(url('tools')) ?>#distro-reference">
      <h3>Distro Reference</h3>
      <p>Compare Ubuntu, Debian, Fedora, Arch, Mint, openSUSE, and AlmaLinux side by side.</p>
    </a>
  </div>
</section>
