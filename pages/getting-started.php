<?php
/** LINUXORA — Getting Started guide. */
require_once __DIR__ . '/includes/functions.php';
?>
<nav class="breadcrumbs" aria-label="Breadcrumb">
  <a href="<?= e(url('home')) ?>">Home</a><span aria-hidden="true">/</span><span aria-current="page">Getting Started</span>
</nav>

<article class="doc-article">
  <header class="article-header">
    <h1>Getting Started with Linux</h1>
    <p class="page-intro">New to the terminal? This guide takes you from opening a shell to running your first real commands — with a safe simulator to practice in.</p>
    <?= difficulty_badge('beginner') ?>
  </header>

  <section class="doc-section" id="what-is-linux">
    <h2>What is Linux, exactly?</h2>
    <p>When people say "Linux" they usually mean a <strong>GNU/Linux distribution</strong>: the Linux kernel (the program that talks to your hardware, started by Linus Torvalds in 1991) plus the GNU userland (the shell, coreutils like <code>ls</code> and <code>cp</code>, compilers) plus thousands of packages assembled by a distribution such as Ubuntu, Debian, Fedora, or Arch.</p>
    <p>Linux runs most of the internet's servers, all of the top supercomputers, Android phones, and countless embedded devices. Learning its command line is one of the most transferable skills in computing: the same commands work on a laptop, a cloud server, and a Raspberry Pi.</p>
  </section>

  <section class="doc-section" id="opening-a-terminal">
    <h2>Opening a terminal</h2>
    <p>The <strong>terminal</strong> (or console) is a window that runs a <strong>shell</strong> — a program that reads your commands and executes them. The default shell on virtually every distribution is <strong>bash</strong>; <code>zsh</code> and <code>fish</code> are popular alternatives.</p>
    <ul class="use-list">
      <li><strong>Desktop Linux:</strong> press <kbd>Ctrl</kbd>+<kbd>Alt</kbd>+<kbd>T</kbd> on most desktops, or find "Terminal" in the application menu.</li>
      <li><strong>macOS:</strong> the Terminal app gives you a Unix shell — most Linux commands work the same way there.</li>
      <li><strong>Windows:</strong> use WSL (Windows Subsystem for Linux) for a real Linux environment, or SSH into a remote server.</li>
      <li><strong>Servers:</strong> you connect remotely with <a href="<?= e(command_url('ssh')) ?>"><code>ssh</code></a> — after that it is the same shell.</li>
    </ul>
    <p>The line where you type is the <strong>prompt</strong>. It typically shows <code>user@host:~$</code> — your username, the machine's name, the current directory (<code>~</code> means your home directory), and <code>$</code> for a normal user. A <code>#</code> instead of <code>$</code> means you are root — the administrator.</p>
  </section>

  <section class="doc-section" id="first-commands">
    <h2>Your first five commands</h2>
    <p>These five answer the questions every beginner has: <em>where am I, what is here, and what is this machine?</em></p>
    <div class="code-block terminal" data-copyable>
      <div class="code-head"><span class="code-dots" aria-hidden="true"><i></i><i></i><i></i></span><span class="code-lang">terminal</span><button class="copy-btn" type="button" aria-label="Copy">Copy</button></div>
      <pre><code><span class="prompt" aria-hidden="true">$</span> pwd
/home/user
<span class="prompt" aria-hidden="true">$</span> ls
Documents  Downloads  notes.txt
<span class="prompt" aria-hidden="true">$</span> whoami
user
<span class="prompt" aria-hidden="true">$</span> uname -a
Linux laptop 6.8.0-45-generic #45-Ubuntu SMP x86_64 GNU/Linux
<span class="prompt" aria-hidden="true">$</span> date
Tue Jan 14 09:30:12 UTC 2025</code></pre>
    </div>
    <p><code>pwd</code> prints your current directory, <code>ls</code> lists its contents, <code>whoami</code> shows your username, <code>uname -a</code> describes the kernel, and <code>date</code> shows the time. Try them right now in the simulator on the <a href="<?= e(url('home')) ?>">homepage</a>.</p>
  </section>

  <section class="doc-section" id="getting-help">
    <h2>Getting help: man pages and --help</h2>
    <p>Every command on this site documents its important flags, but the complete reference is always on your machine:</p>
    <div class="code-block terminal" data-copyable>
      <div class="code-head"><span class="code-dots" aria-hidden="true"><i></i><i></i><i></i></span><span class="code-lang">terminal</span><button class="copy-btn" type="button" aria-label="Copy">Copy</button></div>
      <pre><code><span class="prompt" aria-hidden="true">$</span> man ls          # full manual for ls (press q to quit)
<span class="prompt" aria-hidden="true">$</span> ls --help       # short summary of options
<span class="prompt" aria-hidden="true">$</span> whatis cp       # one-line description
cp (1)               - copy files and directories</code></pre>
    </div>
    <p>Man pages are organized into numbered sections: 1 = user commands, 5 = file formats (like <code>man 5 crontab</code>), 8 = administration. Searching this site gives you a friendlier version of the same information, with links to the official docs on every command page.</p>
  </section>

  <section class="doc-section" id="filesystem-tour">
    <h2>A thirty-second tour of the filesystem</h2>
    <p>Linux has one tree rooted at <code>/</code>. There are no drive letters — USB disks, network shares, and extra partitions are <em>mounted</em> somewhere inside the tree.</p>
    <div class="table-wrap">
      <table class="flag-table">
        <thead><tr><th scope="col">Path</th><th scope="col">What lives there</th></tr></thead>
        <tbody>
          <tr><td><code>/home/you</code></td><td>Your personal files (written as <code>~</code> in the shell).</td></tr>
          <tr><td><code>/etc</code></td><td>System-wide configuration files.</td></tr>
          <tr><td><code>/var/log</code></td><td>Logs — the first place to look when something breaks.</td></tr>
          <tr><td><code>/usr/bin</code></td><td>Most of your executables, including the commands in this encyclopedia.</td></tr>
          <tr><td><code>/tmp</code></td><td>Scratch space, wiped on reboot.</td></tr>
        </tbody>
      </table>
    </div>
    <p>The full tour with fifteen paths lives in <a href="<?= e(url('tools')) ?>#common-paths">Educational Tools → Common Linux Paths</a>.</p>
  </section>

  <section class="doc-section" id="practice-safely">
    <h2>Practice safely</h2>
    <p>Real shells do real things — <code>rm</code> does not ask twice and there is no recycle bin. Three habits keep beginners safe:</p>
    <ul class="use-list">
      <li>Use the <a href="<?= e(url('home')) ?>">simulated terminal</a> on this site to experiment freely — it is a virtual filesystem in your browser, clearly labeled, and cannot touch anything real.</li>
      <li>On a real system, prefer <code>rm -i</code> (ask before deleting) until <code>rm</code> reflexes are solid, and double-check wildcards before pressing Enter.</li>
      <li>Understand every part of a command copied from the internet before running it — especially anything with <code>sudo</code>, <code>curl … | bash</code>, or <code>rm -rf</code>.</li>
    </ul>
    <p>When you are ready, continue with <a href="<?= e(url('fundamentals')) ?>">Linux Fundamentals</a>, or jump into the <a href="<?= e(url('learning')) ?>">Beginner Roadmap</a>.</p>
  </section>
</article>
