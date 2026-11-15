<?php
/**
 * LINUXORA — shared helpers.
 * Pure PHP 8+, no extensions beyond json/mbstring-free basics.
 */

declare(strict_types=1);

/** HTML-escape any value for safe output. */
function e(null|string|int|float|bool $v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Absolute path to a file inside data/. */
function data_path(string $rel): string
{
    return __DIR__ . '/../data/' . $rel;
}

/** Load a JSON file from data/ as an array; empty array when missing or invalid. */
function load_json(string $rel): array
{
    static $cache = [];
    if (isset($cache[$rel])) {
        return $cache[$rel];
    }
    $file = data_path($rel);
    $data = [];
    if (is_readable($file)) {
        $decoded = json_decode((string) file_get_contents($file), true);
        if (is_array($decoded)) {
            $data = $decoded;
        }
    }
    $cache[$rel] = $data;
    return $data;
}

/** All categories, ordered by their number. */
function get_categories(): array
{
    return load_json('categories.json');
}

/** Single category by id, or null. */
function get_category(string $id): ?array
{
    foreach (get_categories() as $cat) {
        if (($cat['id'] ?? '') === $id) {
            return $cat;
        }
    }
    return null;
}

/** The lightweight search index (slug, name, desc, tags, flags, …). */
function get_index(): array
{
    return load_json('commands-index.json');
}

/** Full command document by slug; null when unknown. Slug is strictly filtered. */
function get_command(string $slug): ?array
{
    $safe = preg_replace('/[^a-z0-9+-]/', '', strtolower($slug)) ?? '';
    if ($safe === '') {
        return null;
    }
    $cmd = load_json('commands/' . $safe . '.json');
    return $cmd === [] ? null : $cmd;
}

/** Real, generated statistics (data/stats.json written by the build step). */
function get_stats(): array
{
    $s = load_json('stats.json');
    return $s === [] ? ['commands' => 0, 'categories' => 0, 'subcategories' => 0, 'examples' => 0, 'flags' => 0] : $s;
}

/** Build an in-site URL. */
function url(string $page = 'home', array $params = []): string
{
    $query = array_merge(['page' => $page], $params);
    return './?' . str_replace('%2F', '/', http_build_query($query));
}

/** URL for a command page. */
function command_url(string $slug): string
{
    return url('command', ['cmd' => $slug]);
}

/** URL for a category page. */
function category_url(string $id): string
{
    return url('category', ['cat' => $id]);
}

/** URL-safe anchor id for subcategory names (mirrors slugify() in JS). */
function slugify(string $s): string
{
    $s = strtolower(trim($s));
    $s = preg_replace('/[^a-z0-9]+/', '-', $s) ?? '';
    return trim($s, '-') ?: 'section';
}

/** Difficulty badge markup. */
function difficulty_badge(string $level): string
{
    $class = match ($level) {
        'beginner' => 'badge-beginner',
        'intermediate' => 'badge-intermediate',
        default => 'badge-advanced',
    };
    return '<span class="badge ' . $class . '">' . e(ucfirst($level)) . '</span>';
}

/** Index entry for a slug, or null. */
function index_entry(string $slug): ?array
{
    foreach (get_index() as $entry) {
        if (($entry['slug'] ?? '') === $slug) {
            return $entry;
        }
    }
    return null;
}

/** Previous/next commands in index order for footers on command pages. */
function prev_next(string $slug): array
{
    $index = get_index();
    $pos = null;
    foreach ($index as $i => $entry) {
        if (($entry['slug'] ?? '') === $slug) {
            $pos = $i;
            break;
        }
    }
    if ($pos === null) {
        return ['prev' => null, 'next' => null];
    }
    return [
        'prev' => $index[$pos - 1] ?? null,
        'next' => $index[$pos + 1] ?? null,
    ];
}

/** Command of the day — deterministic per calendar date, based on real data. */
function daily_command(): array
{
    $index = get_index();
    if ($index === []) {
        return [];
    }
    $seed = (int) date('Ymd');
    return $index[$seed % count($index)];
}

/** Curated popular command slugs (all exist in the dataset; validated at build time). */
function popular_slugs(): array
{
    return ['ls', 'cd', 'grep', 'chmod', 'sudo', 'apt', 'systemctl', 'tar', 'ssh', 'find', 'ip', 'top'];
}

/** Recently added slugs — the most recently authored documentation entries. */
function recent_slugs(): array
{
    return ['rmdir', 'taskset', 'nsenter', 'unshare', 'update-grub', 'lsof', 'strace', 'gdb'];
}

/** Full command documents for a list of slugs (skips unknown ones). */
function commands_for_slugs(array $slugs): array
{
    $out = [];
    foreach ($slugs as $slug) {
        $cmd = get_command($slug);
        if ($cmd !== null) {
            $out[] = $cmd;
        }
    }
    return $out;
}

/** Compact card markup for a command (used in grids). */
function command_card(array $cmd): string
{
    $name = e($cmd['name'] ?? $cmd['slug'] ?? '');
    $slug = e($cmd['slug'] ?? '');
    $desc = e($cmd['description'] ?? '');
    $cat  = e($cmd['category'] ?? '');
    ob_start(); ?>
    <a class="cmd-card" href="<?= command_url((string) ($cmd['slug'] ?? '')) ?>" data-command-card>
      <div class="cmd-card-head">
        <code class="cmd-card-name"><?= $name ?></code>
        <?= difficulty_badge((string) ($cmd['difficulty'] ?? 'beginner')) ?>
      </div>
      <p class="cmd-card-desc"><?= $desc ?></p>
      <div class="cmd-card-foot">
        <span class="cmd-card-cat"><?= e(category_name((string) ($cmd['category'] ?? ''))) ?></span>
        <span class="cmd-card-open" aria-hidden="true">Docs →</span>
      </div>
      <input type="hidden" name="cmd-slug" value="<?= $slug ?>">
      <input type="hidden" name="cmd-cat" value="<?= $cat ?>">
    </a>
    <?php return (string) ob_get_clean();
}

/** Category display name by id. */
function category_name(string $id): string
{
    $cat = get_category($id);
    return $cat['name'] ?? ucfirst(str_replace('-', ' ', $id));
}

/** Current page id for the router. */
function current_page(): string
{
    $allowed = [
        'home', 'categories', 'category', 'command', 'getting-started',
        'fundamentals', 'popular', 'recent', 'bookmarks', 'learning', 'tools', 'about',
    ];
    $page = isset($_GET['page']) && is_string($_GET['page']) ? $_GET['page'] : 'home';
    return in_array($page, $allowed, true) ? $page : 'home';
}

/** Safe query parameter (letters, digits, dash only). */
function query_slug(string $key): string
{
    $raw = isset($_GET[$key]) && is_string($_GET[$key]) ? $_GET[$key] : '';
    return preg_replace('/[^a-z0-9+-]/', '', strtolower($raw)) ?? '';
}
