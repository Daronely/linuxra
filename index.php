<?php
/**
 * LINUXORA — front controller.
 * All requests funnel through this file; routing is query-string based so it
 * works on any Apache/cPanel host with or without mod_rewrite.
 */

declare(strict_types=1);

require __DIR__ . '/includes/functions.php';

$page = current_page();

$titles = [
    'home'            => 'LINUXORA — The Complete Linux Command Encyclopedia',
    'categories'      => 'Command Categories — LINUXORA',
    'category'        => 'Category — LINUXORA',
    'command'         => 'Command — LINUXORA',
    'getting-started' => 'Getting Started with Linux — LINUXORA',
    'fundamentals'    => 'Linux Fundamentals — LINUXORA',
    'popular'         => 'Popular Commands — LINUXORA',
    'recent'          => 'Recently Added — LINUXORA',
    'bookmarks'       => 'Your Bookmarks — LINUXORA',
    'learning'        => 'Learning Roadmap — LINUXORA',
    'tools'           => 'Educational Tools — LINUXORA',
    'about'           => 'About LINUXORA',
];

$pageTitle = $titles[$page] ?? $titles['home'];
$pageDesc  = 'Linux commands explained with accurate syntax, flags, examples, and terminal output.';

$categories = get_categories();
$index      = get_index();

// Page-specific data / title overrides.
$command = null;
$category = null;

if ($page === 'command') {
    $command = get_command(query_slug('cmd'));
    if ($command === null) {
        http_response_code(404);
        $pageTitle = 'Command not found — LINUXORA';
    } else {
        $pageTitle = $command['name'] . ' command explained — LINUXORA';
        $pageDesc  = (string) ($command['description'] ?? $pageDesc);
    }
}

if ($page === 'category') {
    $category = get_category(query_slug('cat'));
    if ($category === null) {
        http_response_code(404);
        $pageTitle = 'Category not found — LINUXORA';
    } else {
        $pageTitle = $category['name'] . ' commands — LINUXORA';
        $pageDesc  = (string) ($category['short'] ?? $pageDesc);
    }
}

require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/sidebar.php';
?>
<main class="main" id="main">
  <?php
  $view = __DIR__ . '/pages/' . $page . '.php';
  if (is_readable($view)) {
      require $view;
  } else {
      echo '<section class="section"><h1>Page not found</h1><p>The requested page does not exist.</p></section>';
  }
  ?>
</main>
<?php
require __DIR__ . '/includes/footer.php';
