<?php
/** LINUXORA — educational tools. */
require_once __DIR__ . '/includes/functions.php';
?>
<nav class="breadcrumbs" aria-label="Breadcrumb">
  <a href="<?= e(url('home')) ?>">Home</a><span aria-hidden="true">/</span><span aria-current="page">Educational Tools</span>
</nav>

<section class="section">
  <h1>Educational Tools</h1>
  <p class="page-intro">Interactive helpers that run entirely in your browser — no data leaves this page.</p>
</section>

<!-- ─────────────── Permissions Calculator ─────────────── -->
<section class="section tool-section" id="permissions-calculator">
  <h2>Permissions Calculator</h2>
  <p class="page-intro">Toggle read, write, and execute for each triad. The octal and symbolic forms update live, with an explanation of what the resulting mode allows. (Note: <code>rwxr-xr-x</code> is <code>755</code>, not <code>777</code> — <code>777</code> means <em>everyone can write</em>.)</p>

  <div class="tool-panel" data-perm-calc>
    <div class="perm-grid">
      <?php foreach (['Owner' => 'owner', 'Group' => 'group', 'Others' => 'others'] as $label => $who): ?>
      <fieldset class="perm-triad">
        <legend><?= e($label) ?></legend>
        <?php foreach ([['Read', 'r', 4], ['Write', 'w', 2], ['Execute', 'x', 1]] as [$name, $char, $val]): ?>
        <label class="perm-check">
          <input type="checkbox" data-perm="<?= e($who) ?>" data-value="<?= $val ?>" data-char="<?= e($char) ?>">
          <span><?= e($name) ?> <code><?= e($char) ?></code> (<?= $val ?>)</span>
        </label>
        <?php endforeach; ?>
      </fieldset>
      <?php endforeach; ?>
      <fieldset class="perm-triad">
        <legend>Special bits</legend>
        <?php foreach ([['Setuid (4)', 4000, 's'], ['Setgid (2)', 2000, 's'], ['Sticky (1)', 1000, 't']] as [$name, $val, $char]): ?>
        <label class="perm-check">
          <input type="checkbox" data-special data-value="<?= $val ?>" data-char="<?= e($char) ?>">
          <span><?= e($name) ?></span>
        </label>
        <?php endforeach; ?>
      </fieldset>
    </div>
    <div class="perm-result">
      <div class="perm-octal"><output id="perm-octal" aria-live="polite">755</output><span class="perm-octal-label">octal mode</span></div>
      <div class="perm-symbolic"><code id="perm-symbolic">rwxr-xr-x</code><span class="perm-octal-label">symbolic</span></div>
      <p class="perm-explain" id="perm-explain" aria-live="polite"></p>
      <div class="code-block terminal" data-copyable>
        <div class="code-head"><span class="code-lang">terminal</span><button class="copy-btn" type="button" aria-label="Copy chmod command">Copy</button></div>
        <pre><code id="perm-chmod"></code></pre>
      </div>
    </div>
  </div>
</section>

<!-- ─────────────── Cron Expression Builder ─────────────── -->
<section class="section tool-section" id="cron-builder">
  <h2>Cron Expression Builder</h2>
  <p class="page-intro">Build a standard five-field crontab expression. Pick presets or set fields manually — the expression and a plain-language explanation update live.</p>

  <div class="tool-panel" data-cron-builder>
    <div class="cron-presets">
      <?php foreach ([['Every minute', '* * * * *'], ['Every hour (at :00)', '0 * * * *'], ['Daily at midnight', '0 0 * * *'], ['Daily at 09:00', '0 9 * * *'], ['Every Sunday 03:30', '30 3 * * 0'], ['Every 15 minutes', '*/15 * * * *'], ['First of the month', '0 0 1 * *']] as [$label, $expr]): ?>
      <button class="btn btn-ghost btn-sm" type="button" data-cron-preset="<?= e($expr) ?>"><?= e($label) ?></button>
      <?php endforeach; ?>
    </div>
    <div class="cron-fields">
      <?php foreach ([['minute', '0–59'], ['hour', '0–23'], ['day of month', '1–31'], ['month', '1–12 or JAN–DEC'], ['day of week', '0–7 (0 and 7 = Sunday)']] as [$field, $range]): ?>
      <label class="cron-field">
        <span><?= e($field) ?> <small><?= e($range) ?></small></span>
        <input type="text" data-cron-field="<?= e($field) ?>" value="*" inputmode="text" autocomplete="off" spellcheck="false">
      </label>
      <?php endforeach; ?>
    </div>
    <div class="cron-result">
      <div class="code-block terminal" data-copyable>
        <div class="code-head"><span class="code-lang">crontab -e</span><button class="copy-btn" type="button" aria-label="Copy cron expression">Copy</button></div>
        <pre><code id="cron-expression">* * * * *</code></pre>
      </div>
      <p class="cron-explain" id="cron-explain" aria-live="polite"></p>
    </div>
  </div>
</section>

<!-- ─────────────── File Size Converter ─────────────── -->
<section class="section tool-section" id="size-converter">
  <h2>File Size Converter</h2>
  <p class="page-intro">Convert between B, KB, MB, GB, and TB. Choose <strong>decimal</strong> (SI: 1&nbsp;kB = 1000&nbsp;B, what disk vendors use) or <strong>binary</strong> (IEC: 1&nbsp;KiB = 1024&nbsp;B, what <code>ls -h</code> and memory numbers use) — the gap reaches ~10% at terabyte scale.</p>

  <div class="tool-panel" data-size-converter>
    <div class="size-controls">
      <label class="size-field">
        <span>Value</span>
        <input type="number" id="size-value" value="1" min="0" step="any" inputmode="decimal">
      </label>
      <label class="size-field">
        <span>Unit</span>
        <select id="size-unit">
          <option value="B">B</option><option value="KB" selected>KB</option><option value="MB">MB</option>
          <option value="GB">GB</option><option value="TB">TB</option>
        </select>
      </label>
      <fieldset class="size-standard">
        <legend>Standard</legend>
        <label><input type="radio" name="size-standard" value="decimal" checked> Decimal (kB, 1000)</label>
        <label><input type="radio" name="size-standard" value="binary"> Binary (KiB, 1024)</label>
      </fieldset>
    </div>
    <div class="table-wrap">
      <table class="flag-table" id="size-results" aria-live="polite">
        <thead><tr><th scope="col">Unit</th><th scope="col">Value</th></tr></thead>
        <tbody></tbody>
      </table>
    </div>
    <p class="size-note" id="size-note"></p>
  </div>
</section>

<!-- ─────────────── Distro Reference ─────────────── -->
<section class="section tool-section" id="distro-reference">
  <h2>Linux Distribution Reference</h2>
  <p class="page-intro">Seven distributions you will actually meet, compared by package manager, release model, and intended audience.</p>

  <div class="table-wrap">
    <table class="flag-table distro-table">
      <thead><tr><th scope="col">Distribution</th><th scope="col">Package manager</th><th scope="col">Release model</th><th scope="col">Audience</th><th scope="col">Key characteristics</th></tr></thead>
      <tbody>
        <tr><td><strong>Ubuntu</strong></td><td><a href="<?= e(command_url('apt')) ?>"><code>apt</code></a> (.deb)</td><td>6-month releases; LTS every 2 years with 5 years of support</td><td>Beginners, desktops, cloud</td><td>Huge community and documentation; Snap preinstalled; the most common server distro in tutorials.</td></tr>
        <tr><td><strong>Debian</strong></td><td><a href="<?= e(command_url('apt')) ?>"><code>apt</code></a> (.deb)</td><td>Stable releases roughly every 2 years; very long support</td><td>Servers, stability-focused users</td><td>Ubuntu's upstream; conservative, rock-solid, entirely community-run.</td></tr>
        <tr><td><strong>Fedora</strong></td><td><a href="<?= e(command_url('dnf')) ?>"><code>dnf</code></a> / <a href="<?= e(command_url('yum')) ?>"><code>yum</code></a> (.rpm)</td><td>6-month releases, ~13 months of support each</td><td>Developers, enthusiasts</td><td>Upstream of RHEL; ships new tech first (Wayland, PipeWire); sponsored by Red Hat.</td></tr>
        <tr><td><strong>Arch Linux</strong></td><td><a href="<?= e(command_url('pacman')) ?>"><code>pacman</code></a></td><td>Rolling release — always current</td><td>Advanced users, learners</td><td>Minimal by default, superb wiki, AUR user repository; you assemble the system yourself.</td></tr>
        <tr><td><strong>Linux Mint</strong></td><td><a href="<?= e(command_url('apt')) ?>"><code>apt</code></a> (.deb)</td><td>Follows Ubuntu LTS; ~5 years of support</td><td>Windows converts, desktops</td><td>Cinnamon desktop out of the box; no Snap by default; tuned for familiarity.</td></tr>
        <tr><td><strong>openSUSE</strong></td><td><a href="<?= e(command_url('zypper')) ?>"><code>zypper</code></a> (.rpm)</td><td>Leap (fixed) and Tumbleweed (rolling)</td><td>Workstations, sysadmins</td><td>YaST control center, Btrfs snapshots with rollback (Snapper), strong QA.</td></tr>
        <tr><td><strong>AlmaLinux</strong></td><td><a href="<?= e(command_url('dnf')) ?>"><code>dnf</code></a> (.rpm)</td><td>RHEL-compatible, ~10 years per major release</td><td>Enterprise servers, hosting</td><td>Free RHEL rebuild; the standard for cPanel/whm hosting stacks.</td></tr>
      </tbody>
    </table>
  </div>
  <p class="size-note">Universal formats (<a href="<?= e(command_url('flatpak')) ?>"><code>flatpak</code></a>, and Snap on Ubuntu) install the same package across all of these distributions.</p>
</section>

<!-- ─────────────── Common Linux Paths ─────────────── -->
<section class="section tool-section" id="common-paths">
  <h2>Common Linux Paths</h2>
  <p class="page-intro">The directories every user and administrator should know. Defined by the Filesystem Hierarchy Standard (FHS).</p>

  <div class="table-wrap">
    <table class="flag-table">
      <thead><tr><th scope="col">Path</th><th scope="col">Purpose</th></tr></thead>
      <tbody>
        <tr><td><code>/</code></td><td>Root of the single filesystem tree. Everything hangs from here; there are no drive letters.</td></tr>
        <tr><td><code>/home</code></td><td>Personal directories of ordinary users (<code>/home/alice</code>). Shorthand <code>~</code> in the shell.</td></tr>
        <tr><td><code>/root</code></td><td>The root user's home directory — separate from <code>/</code> so it works even when other mounts fail.</td></tr>
        <tr><td><code>/etc</code></td><td>System-wide configuration: <code>/etc/passwd</code>, <code>/etc/fstab</code>, <code>/etc/ssh/sshd_config</code>. "Editable Text Configuration" is a popular backronym.</td></tr>
        <tr><td><code>/var</code></td><td>Variable data: logs in <code>/var/log</code>, package caches, mail spools, web roots like <code>/var/www</code>.</td></tr>
        <tr><td><code>/usr</code></td><td>Userland programs and data; <code>/usr/bin</code> holds most commands in this encyclopedia. On modern distros <code>/bin</code> is a symlink to <code>/usr/bin</code>.</td></tr>
        <tr><td><code>/bin</code></td><td>Essential user binaries (<code>ls</code>, <code>cp</code>, <code>cat</code>) — kept minimal so the system boots without other mounts.</td></tr>
        <tr><td><code>/sbin</code></td><td>System binaries for administration (<code>fdisk</code>, <code>mount</code>, <code>iptables</code>) — historically root-only.</td></tr>
        <tr><td><code>/tmp</code></td><td>World-writable scratch space, usually cleared on reboot. Never store anything valuable here.</td></tr>
        <tr><td><code>/dev</code></td><td>Device files: <code>/dev/sda</code> (disk), <code>/dev/null</code> (the void), <code>/dev/random</code>. Everything is a file.</td></tr>
        <tr><td><code>/proc</code></td><td>Virtual filesystem exposing kernel/process state: <code>/proc/cpuinfo</code>, <code>/proc/1234/</code> per process. Not real files on disk.</td></tr>
        <tr><td><code>/sys</code></td><td>Virtual filesystem exposing kernel device/driver state (sysfs) — the modern interface to hardware settings.</td></tr>
        <tr><td><code>/opt</code></td><td>Optional/add-on software packages that bundle their own tree, e.g. <code>/opt/google/chrome</code>.</td></tr>
        <tr><td><code>/mnt</code></td><td>Manual, temporary mount point for sysadmin use.</td></tr>
        <tr><td><code>/media</code></td><td>Automatic mount points for removable media (USB drives, DVDs) — desktops create <code>/media/USER/LABEL</code>.</td></tr>
      </tbody>
    </table>
  </div>
</section>
