<?php
/** LINUXORA — about page. */
require_once __DIR__ . '/includes/functions.php';
$stats = get_stats();
?>
<nav class="breadcrumbs" aria-label="Breadcrumb">
  <a href="<?= e(url('home')) ?>">Home</a><span aria-hidden="true">/</span><span aria-current="page">About</span>
</nav>

<article class="doc-article">
  <header class="article-header">
    <h1>About LINUXORA</h1>
    <p class="page-intro">Why this encyclopedia exists, how it is built, and what it will and will not do.</p>
  </header>

  <section class="doc-section" id="mission">
    <h2>The mission</h2>
    <p>LINUXORA is a free, complete reference for the Linux command line. It exists because most command documentation is either too shallow (a list of one-liners) or too raw (a man page written for people who already understand the system). Every command here gets the full treatment: what it is for, how the syntax works, what each flag does, several worked examples with the output you should expect, the mistakes people actually make, and the warnings that save systems.</p>
    <p>Current contents: <strong><?= (int) $stats['commands'] ?></strong> commands in <strong><?= (int) $stats['categories'] ?></strong> categories, <strong><?= (int) $stats['examples'] ?></strong> worked examples, and <strong><?= (int) $stats['flags'] ?></strong> documented flags — all stored locally and served as static JSON.</p>
  </section>

  <section class="doc-section" id="accuracy">
    <h2>Accuracy policy</h2>
    <ul class="use-list">
      <li><strong>Nothing is invented.</strong> Options, behaviors, and outputs reflect how the commands actually work. Where output varies (UUIDs, sizes, dates), examples show a realistic representative value.</li>
      <li><strong>GNU vs BSD is called out.</strong> Commands whose flags differ between GNU/Linux and macOS/BSD note the difference explicitly on their pages.</li>
      <li><strong>Distribution differences matter.</strong> Package management, service management, and firewall tooling are documented per family (apt/dnf/pacman/zypper, systemd/sysvinit, ufw/firewalld/iptables).</li>
      <li><strong>Official references are linked.</strong> Every command page links to its authoritative upstream documentation.</li>
    </ul>
  </section>

  <section class="doc-section" id="privacy">
    <h2>Privacy by design</h2>
    <p>There are no accounts, no tracking, and no server-side storage of anything you do. Bookmarks, recently viewed commands, learning progress, theme choice, and recent searches live in your browser's localStorage and never leave your machine. Clearing site data resets everything.</p>
  </section>

  <section class="doc-section" id="simulated-terminal">
    <h2>The simulated terminal</h2>
    <p>The terminal on the homepage is an educational simulation written in plain JavaScript. It maintains a small virtual filesystem in memory and recognizes a curated set of safe commands (<code>help</code>, <code>pwd</code>, <code>ls</code>, <code>cd</code>, <code>mkdir</code>, <code>touch</code>, <code>cat</code>, <code>echo</code>, <code>clear</code>, <code>whoami</code>, <code>uname</code>, <code>date</code>, <code>history</code>).</p>
    <p>It is <strong>not</strong> a real shell, and no input you type is ever passed to a server or operating system — the site's PHP only reads JSON files and renders HTML. This is a deliberate safety boundary: you can experiment without risk, and the site can be hosted anywhere without exposing a shell.</p>
  </section>

  <section class="doc-section" id="technology">
    <h2>Technology</h2>
    <p>Built with HTML5, CSS3, vanilla JavaScript (ES6+), and PHP 8. No frameworks, no build step, no database — the entire site is flat PHP files and static JSON, which means it runs on any ordinary shared host with cPanel and Apache. All interactivity (search, terminal, tools, progress) is client-side JavaScript.</p>
  </section>

  <section class="doc-section" id="credits">
    <h2>Credits and trademarks</h2>
    <p>Linux® is the registered trademark of Linus Torvalds. LINUXORA is an independent educational project and is not affiliated with the Linux Foundation, any distribution vendor, or any other documentation project. Command documentation links to official sources including GNU manuals, man7.org, kernel.org, and openssh.com.</p>
  </section>
</article>
