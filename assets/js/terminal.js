/* LINUXORA — terminal.js: educational terminal SIMULATION.
   Runs entirely in the browser against a virtual in-memory filesystem.
   No PHP, no network, no exec — nothing typed here can affect any real system. */
(function () {
  'use strict';

  const body = document.getElementById('term-body');
  const input = document.getElementById('term-input');
  const resetBtn = document.getElementById('term-reset');
  if (!body || !input) return;

  /* ---------- virtual filesystem ---------- */
  function dir(children) { return { type: 'dir', children: children || {} }; }
  function file(content) { return { type: 'file', content: content || '' }; }

  const fsRoot = dir({
    home: dir({
      user: dir({
        'welcome.txt': file('Welcome to LINUXORA!\n\nThis is a simulated terminal: a tiny virtual\nfilesystem living in your browser tab. Nothing\nyou type here runs on any real computer.\n\nTry: ls, cat welcome.txt, mkdir projects'),
        Documents: dir({ 'todo.txt': file('1. learn ls and cd\n2. practice grep\n3. read about permissions\n') }),
        Downloads: dir({}),
        '.bashrc': file('# simulated bash config\nexport PS1="\\u@linuxora:\\w$ "\n')
      })
    }),
    etc: dir({ hostname: file('linuxora\n'), hosts: file('127.0.0.1 localhost\n') }),
    tmp: dir({}),
    usr: dir({ bin: dir({}), share: dir({}) }),
    var: dir({ log: dir({}) })
  });

  let cwd = ['home', 'user'];
  let history = [];

  function nodeAt(parts) {
    let node = fsRoot;
    for (const part of parts) {
      if (node.type !== 'dir' || !node.children[part]) return null;
      node = node.children[part];
    }
    return node;
  }

  function resolvePath(arg) {
    if (!arg || arg === '.') return cwd.slice();
    let parts;
    if (arg.startsWith('/')) parts = [];
    else if (arg === '~' || arg.startsWith('~/')) { parts = ['home', 'user']; arg = arg.slice(2); }
    else parts = cwd.slice();
    for (const seg of arg.split('/')) {
      if (seg === '' || seg === '.') continue;
      if (seg === '..') parts.pop();
      else parts.push(seg);
    }
    return parts;
  }

  function pathString(parts) {
    return '/' + parts.join('/');
  }
  function displayCwd() {
    const p = pathString(cwd);
    return p === '/home/user' ? '~' : p.startsWith('/home/user/') ? '~' + p.slice(10) : p;
  }

  /* ---------- output helpers ---------- */
  function esc(s) {
    return String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  }
  function print(text, cls) {
    const div = document.createElement('div');
    div.className = 't-line' + (cls ? ' ' + cls : '');
    div.textContent = text;
    body.appendChild(div);
  }
  function printHtml(html, cls) {
    const div = document.createElement('div');
    div.className = 't-line' + (cls ? ' ' + cls : '');
    div.innerHTML = html;
    body.appendChild(div);
  }
  function printCmd(cmd) {
    printHtml('<span class="prompt">user@linuxora:' + esc(displayCwd()) + '$</span> ' + esc(cmd), 't-cmd');
  }

  /* ---------- banner ---------- */
  function banner() {
    printHtml('<span class="t-dim">LINUXORA simulated terminal — educational only, nothing is executed on a server. Type</span> <span class="t-ok">help</span> <span class="t-dim">to see available commands.</span>');
  }

  /* ---------- commands ---------- */
  const commands = {
    help: {
      desc: 'list available commands',
      run() {
        print('Available commands (simulated):');
        print('  pwd              print working directory');
        print('  ls [-l] [-a]     list directory contents');
        print('  cd PATH          change directory (.. and ~ work)');
        print('  mkdir NAME       create a directory');
        print('  touch NAME       create an empty file');
        print('  cat FILE         print a file\u2019s contents');
        print('  echo TEXT        print text ( > and >> write files )');
        print('  rm NAME          delete a file');
        print('  rmdir NAME       delete an empty directory');
        print('  cp SRC DEST      copy a file');
        print('  mv SRC DEST      move or rename');
        print('  clear            clear the screen');
        print('  whoami           print current user');
        print('  uname [-a]       print system information (simulated)');
        print('  date             print date and time');
        print('  history          show command history');
        print('  help             this list');
      }
    },
    pwd: { desc: 'print working directory', run(args) { void args; print(pathString(cwd)); } },
    ls: {
      desc: 'list directory contents',
      run(args) {
        const flags = args.filter((a) => a.startsWith('-')).join('');
        const target = args.find((a) => !a.startsWith('-'));
        const parts = resolvePath(target);
        const node = nodeAt(parts);
        if (!node) { print('ls: cannot access \'' + (target || '') + '\': No such file or directory', 't-err'); return; }
        if (node.type === 'file') { print(target); return; }
        const names = Object.keys(node.children).sort();
        const showAll = flags.includes('a');
        const visible = showAll ? names : names.filter((n) => !n.startsWith('.'));
        if (!visible.length) return;
        if (flags.includes('l')) {
          for (const name of visible) {
            const child = node.children[name];
            const isDir = child.type === 'dir';
            const perms = isDir ? 'drwxr-xr-x' : '-rw-r--r--';
            const size = isDir ? 4096 : child.content.length;
            print(perms + ' 1 user user ' + String(size).padStart(5) + ' Jan 14 10:00 ' + name + (isDir ? '/' : ''));
          }
        } else {
          print(visible.map((n) => node.children[n].type === 'dir' ? n + '/' : n).join('  '));
        }
      }
    },
    cd: {
      desc: 'change directory',
      run(args) {
        const parts = resolvePath(args[0] || '~');
        const node = nodeAt(parts);
        if (!node) { print('cd: ' + (args[0] || '') + ': No such file or directory', 't-err'); return; }
        if (node.type !== 'dir') { print('cd: ' + args[0] + ': Not a directory', 't-err'); return; }
        cwd = parts;
      }
    },
    mkdir: {
      desc: 'create a directory',
      run(args) {
        if (!args[0]) { print('mkdir: missing operand', 't-err'); return; }
        const parts = resolvePath(args[0]);
        const name = parts.pop();
        const parent = nodeAt(parts);
        if (!parent || parent.type !== 'dir') { print('mkdir: cannot create directory \'' + args[0] + '\': No such file or directory', 't-err'); return; }
        if (parent.children[name]) { print('mkdir: cannot create directory \'' + args[0] + '\': File exists', 't-err'); return; }
        parent.children[name] = dir({});
      }
    },
    touch: {
      desc: 'create an empty file',
      run(args) {
        if (!args[0]) { print('touch: missing file operand', 't-err'); return; }
        const parts = resolvePath(args[0]);
        const name = parts.pop();
        const parent = nodeAt(parts);
        if (!parent || parent.type !== 'dir') { print('touch: cannot touch \'' + args[0] + '\': No such file or directory', 't-err'); return; }
        if (!parent.children[name]) parent.children[name] = file('');
      }
    },
    cat: {
      desc: 'print file contents',
      run(args) {
        if (!args[0]) { print('cat: missing file operand', 't-err'); return; }
        for (const arg of args) {
          const node = nodeAt(resolvePath(arg));
          if (!node) { print('cat: ' + arg + ': No such file or directory', 't-err'); continue; }
          if (node.type === 'dir') { print('cat: ' + arg + ': Is a directory', 't-err'); continue; }
          print(node.content.replace(/\n$/, ''));
        }
      }
    },
    echo: {
      desc: 'print text; supports > and >> redirection',
      run(args) {
        let text = args.join(' ');
        let redirect = null, target = null, append = false;
        const gt = args.indexOf('>'), gtf = args.indexOf('>>');
        if (gtf !== -1) { redirect = gtf; append = true; }
        else if (gt !== -1) { redirect = gt; }
        if (redirect !== null) {
          text = args.slice(0, redirect).join(' ');
          target = args[redirect + 1];
          if (!target) { print('bash: syntax error near unexpected token `newline\u2019', 't-err'); return; }
        } else if (text.startsWith('"') && text.endsWith('"')) {
          text = text.slice(1, -1);
        }
        if (redirect === null) { print(text); return; }
        const parts = resolvePath(target);
        const name = parts.pop();
        const parent = nodeAt(parts);
        if (!parent || parent.type !== 'dir') { print('bash: ' + target + ': No such file or directory', 't-err'); return; }
        const existing = parent.children[name];
        if (existing && existing.type === 'file' && append) {
          existing.content += text + '\n';
        } else {
          parent.children[name] = file(text + '\n');
        }
      }
    },
    rm: {
      desc: 'delete a file',
      run(args) {
        const target = args.find((a) => !a.startsWith('-'));
        if (!target) { print('rm: missing operand', 't-err'); return; }
        const parts = resolvePath(target);
        const name = parts.pop();
        const parent = nodeAt(parts);
        const node = parent && parent.children[name];
        if (!node) { print('rm: cannot remove \'' + target + '\': No such file or directory', 't-err'); return; }
        if (node.type === 'dir' && Object.keys(node.children).length) {
          print('rm: cannot remove \'' + target + '\': Is a directory (use rmdir when empty \u2014 the simulator has no -r)', 't-err');
          return;
        }
        delete parent.children[name];
      }
    },
    rmdir: {
      desc: 'delete an empty directory',
      run(args) {
        if (!args[0]) { print('rmdir: missing operand', 't-err'); return; }
        const parts = resolvePath(args[0]);
        const name = parts.pop();
        const parent = nodeAt(parts);
        const node = parent && parent.children[name];
        if (!node || node.type !== 'dir') { print('rmdir: failed to remove \'' + args[0] + '\': No such file or directory', 't-err'); return; }
        if (Object.keys(node.children).length) { print('rmdir: failed to remove \'' + args[0] + '\': Directory not empty', 't-err'); return; }
        delete parent.children[name];
      }
    },
    cp: {
      desc: 'copy a file',
      run(args) {
        if (args.length < 2) { print('cp: missing destination file operand', 't-err'); return; }
        const src = nodeAt(resolvePath(args[0]));
        if (!src || src.type !== 'file') { print('cp: cannot stat \'' + args[0] + '\': No such file or directory', 't-err'); return; }
        const destParts = resolvePath(args[1]);
        let destParent = nodeAt(destParts.slice(0, -1));
        let destName = destParts[destParts.length - 1];
        if (destParent && destParent.children[destName] && destParent.children[destName].type === 'dir') {
          destParent = destParent.children[destName];
          destName = args[0].split('/').pop();
        }
        if (!destParent || destParent.type !== 'dir') { print('cp: cannot create \'' + args[1] + '\': No such file or directory', 't-err'); return; }
        destParent.children[destName] = file(src.content);
      }
    },
    mv: {
      desc: 'move or rename',
      run(args) {
        if (args.length < 2) { print('mv: missing destination file operand', 't-err'); return; }
        const srcParts = resolvePath(args[0]);
        const srcName = srcParts.pop();
        const srcParent = nodeAt(srcParts);
        const src = srcParent && srcParent.children[srcName];
        if (!src) { print('mv: cannot stat \'' + args[0] + '\': No such file or directory', 't-err'); return; }
        const destParts = resolvePath(args[1]);
        let destParent = nodeAt(destParts.slice(0, -1));
        let destName = destParts[destParts.length - 1];
        if (destParent && destParent.children[destName] && destParent.children[destName].type === 'dir') {
          destParent = destParent.children[destName];
          destName = srcName;
        }
        if (!destParent || destParent.type !== 'dir') { print('mv: cannot move to \'' + args[1] + '\': No such file or directory', 't-err'); return; }
        destParent.children[destName] = src;
        delete srcParent.children[srcName];
      }
    },
    clear: { desc: 'clear the screen', run() { body.innerHTML = ''; banner(); } },
    whoami: { desc: 'print current user', run() { print('user'); } },
    uname: {
      desc: 'print system information (simulated values)',
      run(args) {
        if (args.join('').includes('a')) {
          print('Linux linuxora 6.8.0-simulated #1 SMP x86_64 GNU/Linux');
        } else {
          print('Linux');
        }
      }
    },
    date: { desc: 'print date and time', run() { print(new Date().toString()); } },
    history: {
      desc: 'show command history',
      run() {
        history.forEach((cmd, i) => print('  ' + String(i + 1).padStart(3) + '  ' + cmd));
      }
    }
  };

  /* ---------- input handling ---------- */
  function execute(raw) {
    const line = raw.trim();
    printCmd(raw);
    if (!line) return;
    history.push(line);

    /* support simple chaining with && like a real shell lesson */
    const segments = line.split('&&').map((s) => s.trim()).filter(Boolean);
    for (const segment of segments) {
      const parts = segment.split(/\s+/);
      const name = parts[0];
      const args = parts.slice(1);
      const cmd = commands[name];
      if (!cmd) {
        print(name + ': command not found (this simulator only knows a small set \u2014 type help)', 't-err');
        break;
      }
      cmd.run(args);
    }
  }

  input.addEventListener('keydown', (ev) => {
    if (ev.key === 'Enter') {
      execute(input.value);
      input.value = '';
      body.scrollTop = body.scrollHeight;
    } else if (ev.key === 'ArrowUp') {
      ev.preventDefault();
      /* simple history navigation */
      const prev = history[history.length - 1 - (input.dataset.hIndex ? Number(input.dataset.hIndex) : -1) - 1];
      if (prev) {
        input.dataset.hIndex = String(history.length - 1 - (input.dataset.hIndex ? Number(input.dataset.hIndex) : -1) - 1);
        input.value = prev;
      }
    } else if (ev.key === 'ArrowDown') {
      ev.preventDefault();
      const idx = input.dataset.hIndex ? Number(input.dataset.hIndex) : -1;
      if (idx >= 0 && idx < history.length - 1) {
        input.dataset.hIndex = String(idx + 1);
        input.value = history[idx + 1];
      } else {
        delete input.dataset.hIndex;
        input.value = '';
      }
    }
  });

  /* clicking anywhere on the terminal focuses the input */
  body.closest('.code-block').addEventListener('click', (ev) => {
    if (window.getSelection && String(window.getSelection())) return;
    if (ev.target.closest('button')) return;
    input.focus();
  });

  if (resetBtn) {
    resetBtn.hidden = false;
    resetBtn.addEventListener('click', () => {
      body.innerHTML = '';
      banner();
      input.value = '';
      input.focus();
    });
  }

  banner();
})();
