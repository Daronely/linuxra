<?php
/** LINUXORA — footer, layout closing, scripts. */
require_once __DIR__ . '/functions.php';
$stats = get_stats();
?>
  </div><!-- /.layout -->
</div><!-- /.shell -->

<footer class="site-footer">
  <div class="footer-inner">
    <div class="footer-brand">
      <a class="logo" href="<?= e(url('home')) ?>">
        <span class="logo-mark" aria-hidden="true">❯_</span>
        <span class="logo-text">LINUXORA</span>
      </a>
      <p>The complete Linux command encyclopedia. Accurate documentation, practical examples, and a safe place to practice — <?= (int) ($stats['commands'] ?? 0) ?> commands and growing.</p>
    </div>
    <nav class="footer-col" aria-label="Learn">
      <h3>Learn</h3>
      <a href="<?= e(url('getting-started')) ?>">Getting Started</a>
      <a href="<?= e(url('fundamentals')) ?>">Linux Fundamentals</a>
      <a href="<?= e(url('learning')) ?>">Learning Roadmap</a>
      <a href="<?= e(url('popular')) ?>">Popular Commands</a>
    </nav>
    <nav class="footer-col" aria-label="Explore">
      <h3>Explore</h3>
      <a href="<?= e(url('categories')) ?>">All Categories</a>
      <a href="<?= e(url('recent')) ?>">Recently Added</a>
      <a href="<?= e(url('bookmarks')) ?>">Your Bookmarks</a>
      <a href="<?= e(url('tools')) ?>">Educational Tools</a>
    </nav>
    <nav class="footer-col" aria-label="Project">
      <h3>Project</h3>
      <a href="<?= e(url('about')) ?>">About LINUXORA</a>
      <a href="https://www.kernel.org/doc/html/latest/" rel="noopener noreferrer" target="_blank">Kernel Docs</a>
      <a href="https://www.gnu.org/manual/manual.html" rel="noopener noreferrer" target="_blank">GNU Manuals</a>
      <a href="https://man7.org/linux/man-pages/" rel="noopener noreferrer" target="_blank">man7.org</a>
    </nav>
  </div>
  <div class="footer-bottom">
    <span>LINUXORA — an independent educational reference. Not affiliated with the Linux Foundation.</span>
    <span>Linux® is the registered trademark of Linus Torvalds.</span>
  </div>
</footer>

<div class="search-overlay" id="search-overlay" hidden role="dialog" aria-modal="true" aria-label="Command search"></div>

<script src="./assets/js/navigation.js" defer></script>
<script src="./assets/js/search.js" defer></script>
<script src="./assets/js/terminal.js" defer></script>
<script src="./assets/js/tools.js" defer></script>
<script src="./assets/js/app.js" defer></script>
</body>
</html>
