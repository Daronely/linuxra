/* LINUXORA — search.js: instant client-side search with overlay (Ctrl+K),
   keyboard navigation, highlighting, recent searches, and a home-page
   inline search mode. */
(function () {
  'use strict';

  const LS_RECENT = 'linuxora-recent-searches';
  let indexPromise = null;
  let index = [];

  function loadIndex() {
    if (!indexPromise) {
      indexPromise = fetch('./data/commands-index.json')
        .then((r) => r.json())
        .then((data) => { index = data; return data; })
        .catch(() => { index = []; return []; });
    }
    return indexPromise;
  }

  function getRecent() {
    try { return JSON.parse(localStorage.getItem(LS_RECENT)) || []; } catch (err) { return []; }
  }
  function pushRecent(q) {
    if (!q) return;
    let rec = getRecent().filter((s) => s !== q);
    rec.unshift(q);
    try { localStorage.setItem(LS_RECENT, JSON.stringify(rec.slice(0, 6))); } catch (err) { /* ignore */ }
  }

  /* ---------- scoring ---------- */
  function score(entry, q) {
    const name = entry.name.toLowerCase();
    if (name === q) return 1000;
    if (name.startsWith(q)) return 700;
    if (name.includes(q)) return 500;
    const tags = entry.tags.join(' ').toLowerCase();
    if (tags.includes(q)) return 300;
    if (entry.subcategory.toLowerCase().includes(q)) return 250;
    if (entry.keywords.includes(q)) return 150;
    /* all words present somewhere (multi-word queries like "copy files") */
    const words = q.split(/\s+/).filter(Boolean);
    if (words.length > 1 && words.every((w) => entry.keywords.includes(w))) return 120;
    return -1;
  }

  function search(query, limit) {
    const q = query.trim().toLowerCase();
    if (!q) return [];
    const hits = [];
    for (const entry of index) {
      const s = score(entry, q);
      if (s > 0) hits.push({ entry, s });
    }
    hits.sort((a, b) => b.s - a.s || a.entry.name.localeCompare(b.entry.name));
    return hits.slice(0, limit || 12).map((h) => h.entry);
  }

  function escapeHtml(s) {
    return s.replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  }
  function highlight(text, q) {
    const safe = escapeHtml(text);
    if (!q) return safe;
    const idx = text.toLowerCase().indexOf(q.toLowerCase());
    if (idx === -1) return safe;
    return escapeHtml(text.slice(0, idx)) + '<mark>' + escapeHtml(text.slice(idx, idx + q.length)) + '</mark>' + escapeHtml(text.slice(idx + q.length));
  }

  const SUGGESTIONS = ['copy files', 'delete folder', 'check memory', 'network ip', 'search text', 'compress files', 'disk space', 'running processes'];

  /* ---------- overlay UI ---------- */
  const overlay = document.getElementById('search-overlay');
  let panel = null, input = null, results = null, activeIdx = -1, currentHits = [];

  function buildOverlay() {
    if (!overlay || panel) return;
    overlay.innerHTML =
      '<div class="search-panel">' +
        '<div class="search-input-row">' +
          '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>' +
          '<input type="text" id="search-input" placeholder="Search commands, tasks, flags…" aria-label="Search commands" autocomplete="off" spellcheck="false">' +
          '<span class="search-esc">Esc</span>' +
        '</div>' +
        '<div class="search-results" id="search-results" role="listbox" aria-label="Search results"></div>' +
        '<div class="search-footer"><span>↑↓ navigate</span><span>↵ open</span><span>esc close</span></div>' +
      '</div>';
    panel = overlay.querySelector('.search-panel');
    input = overlay.querySelector('#search-input');
    results = overlay.querySelector('#search-results');

    input.addEventListener('input', () => renderResults(input.value));
    input.addEventListener('keydown', onKey);
    overlay.addEventListener('mousedown', (ev) => { if (ev.target === overlay) close(); });
  }

  function itemHtml(entry, q) {
    return '<a class="search-item" role="option" href="./?page=command&cmd=' + encodeURIComponent(entry.slug) + '">' +
      '<code>' + highlight(entry.name, q) + '</code>' +
      '<span class="s-desc">' + highlight(entry.desc, q) + '</span>' +
      '<span class="s-cat">' + escapeHtml(entry.categoryName) + '</span>' +
      '</a>';
  }

  function renderResults(query) {
    activeIdx = -1;
    const q = query.trim();
    if (!q) {
      const rec = getRecent();
      results.innerHTML = rec.length
        ? '<div class="recent-searches">' + rec.map((s) => '<button type="button" class="recent-chip" data-q="' + escapeHtml(s) + '">' + escapeHtml(s) + '</button>').join('') + '</div>'
        : '<div class="search-empty"><p>Type to search ' + index.length + ' commands — by name, task, or flag.</p>' +
          '<div class="suggestions">' + SUGGESTIONS.map((s) => '<button type="button" class="recent-chip" data-q="' + s + '">' + s + '</button>').join('') + '</div></div>';
      results.querySelectorAll('[data-q]').forEach((chip) => {
        chip.addEventListener('click', () => { input.value = chip.getAttribute('data-q'); input.focus(); renderResults(input.value); });
      });
      return;
    }
    currentHits = search(q, 12);
    if (!currentHits.length) {
      results.innerHTML =
        '<div class="search-empty"><p><strong>No results for “' + escapeHtml(q) + '”.</strong></p>' +
        '<p>Try a task instead of a command name:</p>' +
        '<div class="suggestions">' + SUGGESTIONS.map((s) => '<button type="button" class="recent-chip" data-q="' + s + '">' + s + '</button>').join('') + '</div></div>';
      results.querySelectorAll('[data-q]').forEach((chip) => {
        chip.addEventListener('click', () => { input.value = chip.getAttribute('data-q'); input.focus(); renderResults(input.value); });
      });
      return;
    }
    /* group by category */
    const byCat = {};
    currentHits.forEach((entry) => { (byCat[entry.categoryName] = byCat[entry.categoryName] || []).push(entry); });
    let html = '';
    Object.keys(byCat).forEach((cat) => {
      html += '<div class="search-group-label">' + escapeHtml(cat) + '</div>';
      html += byCat[cat].map((entry) => itemHtml(entry, q)).join('');
    });
    results.innerHTML = html;
    setActive(0);
  }

  function setActive(i) {
    const items = results.querySelectorAll('.search-item');
    if (!items.length) return;
    activeIdx = Math.max(0, Math.min(i, items.length - 1));
    items.forEach((el, j) => el.classList.toggle('selected', j === activeIdx));
    items[activeIdx].scrollIntoView({ block: 'nearest' });
  }

  function onKey(ev) {
    const items = results.querySelectorAll('.search-item');
    if (ev.key === 'ArrowDown') { ev.preventDefault(); setActive(activeIdx + 1); }
    else if (ev.key === 'ArrowUp') { ev.preventDefault(); setActive(activeIdx - 1); }
    else if (ev.key === 'Enter') {
      ev.preventDefault();
      if (activeIdx >= 0 && items[activeIdx]) {
        pushRecent(input.value.trim());
        window.location.href = items[activeIdx].getAttribute('href');
      }
    }
  }

  function open() {
    if (!overlay) return;
    buildOverlay();
    overlay.hidden = false;
    document.body.style.overflow = 'hidden';
    loadIndex().then(() => { input.focus(); renderResults(''); });
  }
  function close() {
    if (!overlay) return;
    overlay.hidden = true;
    document.body.style.overflow = '';
    if (input) input.value = '';
  }

  /* ---------- wiring ---------- */
  ['search-trigger', 'sidebar-search'].forEach((id) => {
    const btn = document.getElementById(id);
    if (btn) btn.addEventListener('click', open);
  });
  document.addEventListener('keydown', (ev) => {
    if ((ev.ctrlKey || ev.metaKey) && ev.key.toLowerCase() === 'k') {
      ev.preventDefault();
      if (overlay && !overlay.hidden) close(); else open();
    } else if (ev.key === 'Escape' && overlay && !overlay.hidden) {
      close();
    }
  });

  /* ---------- home page inline search ---------- */
  const homeInput = document.getElementById('home-search');
  const homeResults = document.getElementById('home-search-results');
  if (homeInput && homeResults) {
    let homeIdx = -1;
    function renderHome(q) {
      homeIdx = -1;
      const query = q.trim();
      if (!query) { homeResults.hidden = true; homeResults.innerHTML = ''; return; }
      loadIndex().then(() => {
        const hits = search(query, 8);
        if (!hits.length) {
          homeResults.innerHTML = '<div class="search-empty"><p>No matches for “' + escapeHtml(query) + '”. Try “copy files” or “check memory”.</p></div>';
        } else {
          homeResults.innerHTML = hits.map((entry) => itemHtml(entry, query)).join('');
        }
        homeResults.hidden = false;
      });
    }
    homeInput.addEventListener('input', () => renderHome(homeInput.value));
    homeInput.addEventListener('keydown', (ev) => {
      const items = homeResults.querySelectorAll('.search-item');
      if (ev.key === 'ArrowDown') { ev.preventDefault(); homeIdx = Math.min(homeIdx + 1, items.length - 1); }
      else if (ev.key === 'ArrowUp') { ev.preventDefault(); homeIdx = Math.max(homeIdx - 1, 0); }
      else if (ev.key === 'Enter' && items[homeIdx]) { ev.preventDefault(); pushRecent(homeInput.value.trim()); window.location.href = items[homeIdx].getAttribute('href'); return; }
      else if (ev.key === 'Enter') {
        ev.preventDefault();
        loadIndex().then(() => {
          const hits = search(homeInput.value, 1);
          if (hits.length) { pushRecent(homeInput.value.trim()); window.location.href = './?page=command&cmd=' + encodeURIComponent(hits[0].slug); }
        });
        return;
      } else { return; }
      items.forEach((el, j) => el.classList.toggle('selected', j === homeIdx));
    });
    document.addEventListener('click', (ev) => {
      if (!homeResults.contains(ev.target) && ev.target !== homeInput && !homeInput.value.trim()) {
        homeResults.hidden = true;
      }
    });
  }

  /* preload the index early on capable connections */
  if ('requestIdleCallback' in window) {
    requestIdleCallback(() => loadIndex());
  } else {
    setTimeout(loadIndex, 800);
  }
})();
