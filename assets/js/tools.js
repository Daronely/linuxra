/* LINUXORA — tools.js: permissions calculator, cron builder, size converter.
   All client-side; no data leaves the browser. */
(function () {
  'use strict';

  /* ================= Permissions Calculator ================= */
  const permPanel = document.querySelector('[data-perm-calc]');
  if (permPanel) {
    const boxes = permPanel.querySelectorAll('input[data-perm]');
    const specials = permPanel.querySelectorAll('input[data-special]');
    const octalOut = document.getElementById('perm-octal');
    const symOut = document.getElementById('perm-symbolic');
    const explain = document.getElementById('perm-explain');
    const chmodOut = document.getElementById('perm-chmod');

    function triadValue(who) {
      let v = 0, sym = '';
      permPanel.querySelectorAll('input[data-perm="' + who + '"]').forEach((cb) => {
        v += Number(cb.getAttribute('data-value'));
        if (cb.checked) sym += cb.getAttribute('data-char'); else sym += '-';
      });
      return { v, sym };
    }

    function explainMode(octal, who, grp, oth, special) {
      const parts = [];
      const whoName = { owner: 'the owner', group: 'the group', others: 'everyone else' };
      [['owner', who], ['group', grp], ['others', oth]].forEach(([name, t]) => {
        const can = [];
        if (t.v & 4) can.push('read');
        if (t.v & 2) can.push('write');
        if (t.v & 1) can.push('execute/enter');
        parts.push(name === 'owner' ? 'The owner can ' + (can.join(', ') || 'nothing') :
          name === 'group' ? 'the group can ' + (can.join(', ') || 'nothing') :
          'everyone else can ' + (can.join(', ') || 'nothing'));
      });
      let specialNote = '';
      if (special >= 4) specialNote += ' Setuid: the program runs with the file owner\u2019s privileges.';
      if (special >= 2 && special < 4 || special === 2 || special === 3 || special === 6 || special === 7) {
        if (special & 2) specialNote += ' Setgid: on a directory, new files inherit the directory\u2019s group.';
      }
      if (special & 1) specialNote += ' Sticky bit: on a directory, users can delete only their own files.';
      void whoName;
      return octal + ' = ' + parts.join('; ') + '.' + specialNote;
    }

    function update() {
      const who = triadValue('owner');
      const grp = triadValue('group');
      const oth = triadValue('others');
      let special = 0;
      specials.forEach((cb) => { if (cb.checked) special += Number(cb.getAttribute('data-value')); });
      const octal = (special ? String(special) : '') + who.v + grp.v + oth.v;
      const specialSym = special & 4 ? (who.v & 1 ? 's' : 'S') : special & 1 ? (oth.v & 1 ? 't' : 'T') : '';
      const grpSym = special & 2 ? (grp.v & 1 ? 's' : 'S') : '';
      const sym = who.sym.slice(0, 2) + (specialSym || who.sym[2]) + grp.sym.slice(0, 2) + (grpSym || grp.sym[2]) + oth.sym;
      octalOut.textContent = octal;
      symOut.textContent = sym;
      explain.textContent = explainMode(octal, who, grp, oth, special);
      chmodOut.textContent = '$ chmod ' + octal + ' file   # or: chmod u=' + who.sym.replace(/-/g, '').split('').join(',') + ' file';
      void specialNote;
    }

    permPanel.addEventListener('change', update);
    update();
  }

  /* ================= Cron Expression Builder ================= */
  const cronPanel = document.querySelector('[data-cron-builder]');
  if (cronPanel) {
    const fields = {};
    cronPanel.querySelectorAll('[data-cron-field]').forEach((inp) => {
      fields[inp.getAttribute('data-cron-field')] = inp;
    });
    const exprOut = document.getElementById('cron-expression');
    const explainOut = document.getElementById('cron-explain');

    const FIELD_INFO = [
      ['minute', 'minute (0\u201359)'],
      ['hour', 'hour (0\u201323)'],
      ['day of month', 'day of month (1\u201331)'],
      ['month', 'month (1\u201312)'],
      ['day of week', 'day of week (0\u20137, 0 and 7 = Sunday)']
    ];

    function describe(val, label) {
      if (val === '*') return 'every ' + label;
      if (val.startsWith('*/')) return 'every ' + val.slice(2) + ' ' + (label === 'minute' ? 'minutes' : label === 'hour' ? 'hours' : label + 's');
      if (val.includes(',')) return 'at ' + label + 's ' + val.split(',').join(', ');
      if (val.includes('-')) return 'each ' + label + ' from ' + val.split('-')[0] + ' through ' + val.split('-')[1];
      return 'at ' + label + ' ' + val;
    }

    function update() {
      const vals = FIELD_INFO.map(([key]) => (fields[key].value.trim() || '*'));
      const expr = vals.join(' ');
      exprOut.textContent = expr;
      const parts = FIELD_INFO.map(([key, label], i) => describe(vals[i], label));
      explainOut.textContent = 'Schedule: ' + parts.join(', ') + '. ' +
        'Fields run left to right: minute, hour, day of month, month, day of week. ' +
        'Note: if BOTH day-of-month and day-of-week are restricted (not *), cron fires when EITHER matches.';
    }

    cronPanel.querySelectorAll('[data-cron-preset]').forEach((btn) => {
      btn.addEventListener('click', () => {
        const expr = btn.getAttribute('data-cron-preset').split(' ');
        FIELD_INFO.forEach(([key], i) => { fields[key].value = expr[i] || '*'; });
        update();
      });
    });
    cronPanel.addEventListener('input', update);
    update();
  }

  /* ================= File Size Converter ================= */
  const sizePanel = document.querySelector('[data-size-converter]');
  if (sizePanel) {
    const valueIn = document.getElementById('size-value');
    const unitIn = document.getElementById('size-unit');
    const tbody = sizePanel.querySelector('tbody');
    const note = document.getElementById('size-note');
    const DECIMAL = { B: 1, KB: 1e3, MB: 1e6, GB: 1e9, TB: 1e12 };
    const BINARY = { B: 1, KB: 1024, MB: 1048576, GB: 1073741824, TB: 1099511627776 };
    const LABELS = { B: 'B', KB: 'kB / KiB', MB: 'MB / MiB', GB: 'GB / GiB', TB: 'TB / TiB' };

    function fmt(n) {
      if (!isFinite(n)) return '—';
      if (n !== 0 && (Math.abs(n) < 0.001 || Math.abs(n) >= 1e15)) return n.toExponential(4);
      return Number(n.toPrecision(8)).toLocaleString('en-US', { maximumFractionDigits: 6 });
    }

    function update() {
      const standard = sizePanel.querySelector('input[name="size-standard"]:checked').value;
      const table = standard === 'binary' ? BINARY : DECIMAL;
      const v = parseFloat(valueIn.value);
      if (isNaN(v) || v < 0) { tbody.innerHTML = '<tr><td colspan="2">Enter a non-negative number.</td></tr>'; return; }
      const bytes = v * table[unitIn.value];
      let rows = '';
      Object.keys(table).forEach((unit) => {
        rows += '<tr><td>' + LABELS[unit] + '</td><td><code>' + fmt(bytes / table[unit]) + '</code></td></tr>';
      });
      tbody.innerHTML = rows;
      if (standard === 'binary') {
        note.textContent = 'Binary (IEC) standard: each step multiplies by 1024. This is what memory sizes and tools like ls -h / free report. Strictly, these units are written KiB, MiB, GiB, TiB.';
      } else {
        note.textContent = 'Decimal (SI) standard: each step multiplies by 1000. This is what disk and SSD vendors use for capacity labels — one reason a "1 TB" drive shows as ~931 GiB in the OS.';
      }
    }

    valueIn.addEventListener('input', update);
    unitIn.addEventListener('change', update);
    sizePanel.addEventListener('change', update);
    update();
  }
})();
