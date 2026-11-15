<?php
/** LINUXORA — Linux Fundamentals guide. */
require_once __DIR__ . '/includes/functions.php';
?>
<nav class="breadcrumbs" aria-label="Breadcrumb">
  <a href="<?= e(url('home')) ?>">Home</a><span aria-hidden="true">/</span><span aria-current="page">Linux Fundamentals</span>
</nav>

<article class="doc-article">
  <header class="article-header">
    <h1>Linux Fundamentals</h1>
    <p class="page-intro">The mental model behind every command: the filesystem tree, permissions, users, processes, pipes, and packages. Read this once and the whole encyclopedia makes more sense.</p>
    <?= difficulty_badge('beginner') ?>
  </header>

  <section class="doc-section" id="the-tree">
    <h2>Everything is a file, and it all hangs from /</h2>
    <p>Linux exposes nearly everything as files: documents, devices (<code>/dev/sda</code>), kernel statistics (<code>/proc</code>, <code>/sys</code>), even sockets. The filesystem is a single tree rooted at <code>/</code>. An <strong>absolute path</strong> (<code>/etc/hosts</code>) starts at the root; a <strong>relative path</strong> (<code>../notes</code>) starts from where you are now, which <code>pwd</code> shows.</p>
    <p>Directories are files that list other files. Filenames starting with a dot (like <code>.bashrc</code>) are hidden — show them with <code>ls -a</code>. Case matters: <code>Notes.txt</code> and <code>notes.txt</code> are different files.</p>
  </section>

  <section class="doc-section" id="permissions-model">
    <h2>Permissions: rwx for owner, group, and others</h2>
    <p>Every file carries three permission triads. Read the long listing left to right:</p>
    <div class="code-block terminal" data-copyable>
      <div class="code-head"><span class="code-dots" aria-hidden="true"><i></i><i></i><i></i></span><span class="code-lang">terminal</span><button class="copy-btn" type="button" aria-label="Copy">Copy</button></div>
      <pre><code><span class="prompt" aria-hidden="true">$</span> ls -l script.sh
-rwxr-xr-- 1 user devs 1200 Jan 12 14:02 script.sh</code></pre>
    </div>
    <p><code>-</code> (regular file), then <code>rwx</code> — the owner <code>user</code> may read, write, and execute; <code>r-x</code> — the group <code>devs</code> may read and execute; <code>r--</code> — everyone else may only read. Each permission is also a number: r=4, w=2, x=1, so <code>rwxr-xr--</code> is <code>754</code>. Change them with <a href="<?= e(command_url('chmod')) ?>"><code>chmod</code></a>, ownership with <a href="<?= e(command_url('chown')) ?>"><code>chown</code></a> — or play with the <a href="<?= e(url('tools')) ?>#permissions-calculator">Permissions Calculator</a> until it is second nature.</p>
    <p>For directories the meaning shifts: <code>r</code> lists contents, <code>w</code> creates/deletes entries inside, <code>x</code> allows entering the directory.</p>
  </section>

  <section class="doc-section" id="users-root">
    <h2>Users, groups, and root</h2>
    <p>Linux is multi-user from the ground up. Every process runs as a user; every file has an owner. One special user, <strong>root</strong> (UID 0), bypasses all permission checks. Day to day you work as a normal user and borrow root power only for specific commands with <a href="<?= e(command_url('sudo')) ?>"><code>sudo</code></a>, which logs every use.</p>
    <p>Groups collect users so permissions can be granted to teams. Inspect your own identity with <a href="<?= e(command_url('id')) ?>"><code>id</code></a> and <a href="<?= e(command_url('whoami')) ?>"><code>whoami</code></a>; see who else is logged in with <a href="<?= e(command_url('who')) ?>"><code>who</code></a>.</p>
  </section>

  <section class="doc-section" id="processes">
    <h2>Processes: programs in motion</h2>
    <p>Every running program is a process with a PID, an owner, and a memory/CPU footprint. The shell starts processes, connects them, and reports their exit codes. Key ideas:</p>
    <ul class="use-list">
      <li><strong>Foreground vs background:</strong> append <code>&amp;</code> to run in the background; <a href="<?= e(command_url('jobs')) ?>"><code>jobs</code></a> lists them.</li>
      <li><strong>Signals:</strong> <a href="<?= e(command_url('kill')) ?>"><code>kill</code></a> sends signals — <code>SIGTERM</code> (15) asks politely, <code>SIGKILL</code> (9) cannot be ignored.</li>
      <li><strong>Monitoring:</strong> <a href="<?= e(command_url('ps')) ?>"><code>ps aux</code></a> snapshots, <a href="<?= e(command_url('top')) ?>"><code>top</code></a> and <a href="<?= e(command_url('htop')) ?>"><code>htop</code></a> watch live.</li>
    </ul>
  </section>

  <section class="doc-section" id="pipes-redirection">
    <h2>Pipes and redirection: the Unix superpower</h2>
    <p>Every command reads <strong>stdin</strong>, writes <strong>stdout</strong>, and reports errors on <strong>stderr</strong>. The shell can rewire all three:</p>
    <div class="code-block terminal" data-copyable>
      <div class="code-head"><span class="code-dots" aria-hidden="true"><i></i><i></i><i></i></span><span class="code-lang">terminal</span><button class="copy-btn" type="button" aria-label="Copy">Copy</button></div>
      <pre><code><span class="prompt" aria-hidden="true">$</span> ls -lh &gt; listing.txt      # stdout to a file (overwrite)
<span class="prompt" aria-hidden="true">$</span> echo note &gt;&gt; log.txt      # append
<span class="prompt" aria-hidden="true">$</span> sort names.txt | uniq -u  # pipe: output of sort feeds uniq
<span class="prompt" aria-hidden="true">$</span> grep -r TODO . 2&gt;/dev/null # discard error messages</code></pre>
    </div>
    <p>Pipes are why Linux tools are small and composable: <a href="<?= e(command_url('grep')) ?>"><code>grep</code></a> filters, <a href="<?= e(command_url('sort')) ?>"><code>sort</code></a> orders, <a href="<?= e(command_url('wc')) ?>"><code>wc</code></a> counts — and any combination becomes a new tool without writing a program.</p>
  </section>

  <section class="doc-section" id="packages">
    <h2>Software comes from your distribution</h2>
    <p>On Linux you rarely download installers from websites. Each distribution runs curated repositories, and a package manager installs, updates, and removes software from them with dependency resolution:</p>
    <div class="table-wrap">
      <table class="flag-table">
        <thead><tr><th scope="col">Family</th><th scope="col">Manager</th><th scope="col">Install example</th></tr></thead>
        <tbody>
          <tr><td>Debian, Ubuntu, Mint</td><td><a href="<?= e(command_url('apt')) ?>"><code>apt</code></a></td><td><code>sudo apt install git</code></td></tr>
          <tr><td>Fedora, RHEL, AlmaLinux</td><td><a href="<?= e(command_url('dnf')) ?>"><code>dnf</code></a></td><td><code>sudo dnf install git</code></td></tr>
          <tr><td>Arch, Manjaro</td><td><a href="<?= e(command_url('pacman')) ?>"><code>pacman</code></a></td><td><code>sudo pacman -S git</code></td></tr>
          <tr><td>openSUSE</td><td><a href="<?= e(command_url('zypper')) ?>"><code>zypper</code></a></td><td><code>sudo zypper install git</code></td></tr>
        </tbody>
      </table>
    </div>
    <p>The full comparison, including release models and audiences, is in <a href="<?= e(url('tools')) ?>#distro-reference">Distro Reference</a>.</p>
  </section>

  <section class="doc-section" id="exit-codes">
    <h2>Exit codes: how commands report success</h2>
    <p>Every command ends with a numeric exit code, stored in <code>$?</code>. <code>0</code> means success; anything else is a specific failure. Scripts and the <code>&amp;&amp;</code> / <code>||</code> operators depend on it:</p>
    <div class="code-block terminal" data-copyable>
      <div class="code-head"><span class="code-dots" aria-hidden="true"><i></i><i></i><i></i></span><span class="code-lang">terminal</span><button class="copy-btn" type="button" aria-label="Copy">Copy</button></div>
      <pre><code><span class="prompt" aria-hidden="true">$</span> grep -q root /etc/passwd &amp;&amp; echo found || echo missing
found
<span class="prompt" aria-hidden="true">$</span> ls /nope
ls: cannot access '/nope': No such file or directory
<span class="prompt" aria-hidden="true">$</span> echo $?
2</code></pre>
    </div>
    <p>This is also how you should read error messages: the command name, the problem, and often a hint. When a command "doesn't work", check the exit code and the exact wording before guessing.</p>
  </section>

  <section class="doc-section" id="next-steps">
    <h2>Where to go next</h2>
    <p>You now have the model. Put it to work with the <a href="<?= e(url('learning')) ?>">roadmaps</a>: the beginner path walks through file management and text tools; the intermediate path adds networking, permissions, and services; the advanced path covers security, kernel interfaces, and namespaces. Every command page links to related commands, so following your curiosity works too.</p>
  </section>
</article>
