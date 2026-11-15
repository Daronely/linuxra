<?php
/**
 * LINUXORA — full command documentation view.
 * Expects $command (array) to be set. Renders the complete documentation page.
 */
require_once __DIR__ . '/functions.php';

if (!isset($command) || !is_array($command)) {
    return;
}

$slug   = (string) ($command['slug'] ?? '');
$name   = (string) ($command['name'] ?? $slug);
$catId  = (string) ($command['category'] ?? '');
$cat    = get_category($catId);
$pn     = prev_next($slug);
$flags  = $command['flags'] ?? [];
$examples = $command['examples'] ?? [];
$related  = $command['related'] ?? [];
$tags     = $command['tags'] ?? [];
?>
<nav class="breadcrumbs" aria-label="Breadcrumb">
  <a href="<?= e(url('home')) ?>">Home</a>
  <span aria-hidden="true">/</span>
  <a href="<?= e(url('categories')) ?>">Categories</a>
  <span aria-hidden="true">/</span>
  <a href="<?= e(category_url($catId)) ?>"><?= e($cat['name'] ?? $catId) ?></a>
  <span aria-hidden="true">/</span>
  <span aria-current="page"><?= e($name) ?></span>
</nav>

<article class="command-doc" id="command-doc" data-slug="<?= e($slug) ?>">
  <header class="command-header">
    <div class="command-title-row">
      <h1 class="command-title"><code><?= e($name) ?></code></h1>
      <?= difficulty_badge((string) ($command['difficulty'] ?? 'beginner')) ?>
      <button class="btn btn-ghost bookmark-btn" type="button" data-bookmark="<?= e($slug) ?>" aria-pressed="false" aria-label="Bookmark this command">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M6 3h12v18l-6-4-6 4z"/></svg>
        <span data-bookmark-label>Bookmark</span>
      </button>
    </div>
    <p class="command-desc"><?= e($command['description'] ?? '') ?></p>
    <div class="tag-row">
      <?php foreach ($tags as $tag): ?>
        <span class="tag"><?= e($tag) ?></span>
      <?php endforeach; ?>
    </div>
  </header>

  <?php if (!empty($command['about'])): ?>
  <section class="doc-section" id="about">
    <h2>About <?= e($name) ?></h2>
    <?php foreach ($command['about'] as $para): ?>
      <p><?= e($para) ?></p>
    <?php endforeach; ?>
  </section>
  <?php endif; ?>

  <?php if (!empty($command['useCases'])): ?>
  <section class="doc-section" id="use-cases">
    <h2>When to use it</h2>
    <ul class="use-list">
      <?php foreach ($command['useCases'] as $use): ?>
        <li><?= e($use) ?></li>
      <?php endforeach; ?>
    </ul>
  </section>
  <?php endif; ?>

  <section class="doc-section" id="syntax">
    <h2>Syntax</h2>
    <div class="code-block terminal" data-copyable>
      <div class="code-head"><span class="code-lang">syntax</span><button class="copy-btn" type="button" aria-label="Copy syntax">Copy</button></div>
      <pre><code><?= e($command['syntax'] ?? '') ?></code></pre>
    </div>
  </section>

  <?php if ($flags !== []): ?>
  <section class="doc-section" id="options">
    <h2>Options &amp; flags</h2>
    <div class="table-wrap">
      <table class="flag-table">
        <thead><tr><th scope="col">Flag</th><th scope="col">Description</th></tr></thead>
        <tbody>
          <?php foreach ($flags as $flag): ?>
          <tr>
            <td><code class="flag-code"><?= e($flag['flag'] ?? '') ?></code></td>
            <td><?= e($flag['description'] ?? '') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
  <?php endif; ?>

  <?php if ($examples !== []): ?>
  <section class="doc-section" id="examples">
    <h2>Examples</h2>
    <?php foreach ($examples as $i => $ex): ?>
      <?php
      $exCmd  = (string) ($ex['command'] ?? '');
      $exOut  = $ex['output'] ?? null;
      $exExpl = (string) ($ex['explanation'] ?? '');
      ?>
      <div class="example">
        <h3 class="example-title">Example <?= $i + 1 ?></h3>
        <div class="code-block terminal" data-copyable>
          <div class="code-head">
            <span class="code-dots" aria-hidden="true"><i></i><i></i><i></i></span>
            <span class="code-lang">terminal</span>
            <button class="copy-btn" type="button" aria-label="Copy command">Copy</button>
          </div>
          <pre><code><span class="prompt" aria-hidden="true">$</span> <?= e($exCmd) ?></code></pre>
          <?php if (is_string($exOut) && $exOut !== ''): ?>
          <pre class="code-output" aria-label="Expected output"><code><?= e($exOut) ?></code></pre>
          <?php endif; ?>
        </div>
        <?php if ($exExpl !== ''): ?>
        <p class="example-explain"><strong>What happens:</strong> <?= e($exExpl) ?></p>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </section>
  <?php endif; ?>

  <?php if (!empty($command['mistakes'])): ?>
  <section class="doc-section" id="mistakes">
    <h2>Common mistakes</h2>
    <ul class="mistake-list">
      <?php foreach ($command['mistakes'] as $m): ?>
        <li><?= e($m) ?></li>
      <?php endforeach; ?>
    </ul>
  </section>
  <?php endif; ?>

  <?php if (!empty($command['warnings'])): ?>
  <section class="doc-section" id="warnings">
    <h2>Warnings</h2>
    <div class="callout callout-warning">
      <ul>
        <?php foreach ($command['warnings'] as $w): ?>
          <li><?= e($w) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>
  <?php endif; ?>

  <?php if (!empty($command['distroNotes'])): ?>
  <section class="doc-section" id="compatibility">
    <h2>Distribution compatibility</h2>
    <div class="callout callout-info">
      <p><?= e($command['distroNotes']) ?></p>
    </div>
  </section>
  <?php endif; ?>

  <?php if ($related !== []): ?>
  <section class="doc-section" id="related">
    <h2>Related commands</h2>
    <div class="related-grid">
      <?php foreach ($related as $relSlug):
        $rel = index_entry((string) $relSlug);
        if ($rel === null) { continue; } ?>
        <a class="related-card" href="<?= e(command_url((string) $rel['slug'])) ?>">
          <code><?= e($rel['name'] ?? $rel['slug']) ?></code>
          <span><?= e($rel['desc'] ?? '') ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <section class="doc-section" id="references">
    <h2>Official documentation</h2>
    <p>
      <a class="ext-link" href="<?= e($command['docs'] ?? '#') ?>" rel="noopener noreferrer" target="_blank">
        <?= e($command['docs'] ?? '') ?>
      </a>
    </p>
  </section>

  <nav class="pn-nav" aria-label="Command pagination">
    <?php if ($pn['prev'] !== null): ?>
      <a class="pn-card pn-prev" href="<?= e(command_url((string) $pn['prev']['slug'])) ?>">
        <span class="pn-dir">← Previous</span>
        <code><?= e($pn['prev']['name'] ?? $pn['prev']['slug']) ?></code>
      </a>
    <?php else: ?><span></span><?php endif; ?>
    <?php if ($pn['next'] !== null): ?>
      <a class="pn-card pn-next" href="<?= e(command_url((string) $pn['next']['slug'])) ?>">
        <span class="pn-dir">Next →</span>
        <code><?= e($pn['next']['name'] ?? $pn['next']['slug']) ?></code>
      </a>
    <?php else: ?><span></span><?php endif; ?>
  </nav>
</article>
