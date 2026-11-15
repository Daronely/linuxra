/* LINUXORA — app.js: theme, TOC + scroll-spy, copy buttons, bookmarks,
   recently viewed, random command, learning progress. */
(function () {
  'use strict';

  const LS = {
    theme: 'linuxora-theme',
    bookmarks: 'linuxora-bookmarks',
    viewed: 'linuxora-recently-viewed',
    progress: 'linuxora-progress'
  };

  function lsGet(key, fallback) {
    try {
      const raw = localStorage.getItem(key);
      return raw ? JSON.parse(raw) : fallback;
    } catch (err) { return fallback; }
  }
  function lsSet(key, value) {
    try { localStorage.setItem(key, JSON.stringify(value)); } catch (err) { /* full or blocked */ }
  }
  window.LinuxoraStore = { get: lsGet, set: lsSet, LS: LS };

  /* ---------- theme ---------- */
  const themeBtn = document.getElementById('theme-toggle');
  function applyThemeLabel() {
    if (!themeBtn) return;
    const cur = document.documentElement.getAttribute('data-theme') || 'dark';
    themeBtn.setAttribute('aria-label', cur === 'dark' ? 'Switch to light theme' : 'Switch to dark theme');
  }
  if (themeBtn) {
    themeBtn.addEventListener('click', () => {
      const cur = document.documentElement.getAttribute('data-theme') === 'light' ? 'light' : 'dark';
      const next = cur === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-theme', next);
      try { localStorage.setItem(LS.theme, next); } catch (err) { /* ignore */ }
      applyThemeLabel();
    });
    applyThemeLabel();
  }

  /* ---------- copy buttons ---------- */
  document.querySelectorAll('[data-copyable]').forEach((block) => {
    const btn = block.querySelector('.copy-btn');
    if (!btn) return;
    const pres = block.querySelectorAll('pre');
    const cmdPre = pres[0];
    btn.addEventListener('click', () => {
      let text = '';
      if (cmdPre) {
        text = cmdPre.innerText.replace(/^\$\s?/gm, '');
      }
      const done = () => {
        btn.textContent = 'Copied!';
        btn.classList.add('copied');
        setTimeout(() => { btn.textContent = 'Copy'; btn.classList.remove('copied'); }, 1400);
      };
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(done).catch(() => {});
      } else {
        const ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); done(); } catch (err) { /* ignore */ }
        ta.remove();
      }
    });
  });

  /* ---------- TOC + scroll-spy ---------- */
  const main = document.getElementById('main');
  const layout = document.querySelector('.layout');
  const headings = main
    ? Array.from(main.querySelectorAll('.doc-section h2, .doc-article .doc-section h2, article h2'))
    : [];

  if (main && headings.length >= 3 && layout) {
    const toc = document.createElement('nav');
    toc.className = 'toc';
    toc.setAttribute('aria-label', 'Table of contents');
    const list = document.createElement('ul');
    headings.forEach((h, i) => {
      const section = h.closest('section, article');
      if (!section.id) section.id = 'section-' + (i + 1);
      const li = document.createElement('li');
      const a = document.createElement('a');
      a.href = '#' + section.id;
      a.textContent = h.textContent;
      li.appendChild(a);
      list.appendChild(li);
    });
    toc.appendChild(list);
    const title = document.createElement('p');
    title.className = 'toc-title';
    title.textContent = 'On this page';
    toc.insertBefore(title, toc.firstChild);
    layout.appendChild(toc);

    const links = Array.from(toc.querySelectorAll('a'));
    const targets = links.map((a) => document.getElementById(a.getAttribute('href').slice(1))).filter(Boolean);

    function spy() {
      const fromTop = window.scrollY + 120;
      let current = targets[0];
      targets.forEach((t) => { if (t.offsetTop <= fromTop) current = t; });
      links.forEach((a) => a.classList.toggle('active', current && a.getAttribute('href') === '#' + current.id));
    }
    let ticking = false;
    window.addEventListener('scroll', () => {
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(() => { spy(); ticking = false; });
    }, { passive: true });
    spy();
  }

  /* ---------- bookmarks ---------- */
  function getBookmarks() { return lsGet(LS.bookmarks, []); }

  function renderBookmarksPage() {
    const grid = document.getElementById('bookmarks-grid');
    if (!grid) return;
    const slugs = getBookmarks();
    const clearBtn = document.getElementById('clear-bookmarks');
    if (!slugs.length) {
      grid.innerHTML = '<p class="empty-note">' + (grid.dataset.emptyNote || 'Nothing bookmarked yet.') + '</p>';
      if (clearBtn) clearBtn.hidden = true;
      return;
    }
    if (clearBtn) {
      clearBtn.hidden = false;
      clearBtn.onclick = () => {
        lsSet(LS.bookmarks, []);
        renderBookmarksPage();
      };
    }
    grid.innerHTML = '<p class="empty-note">Loading bookmarks…</p>';
    fetch('./data/commands-index.json')
      .then((r) => r.json())
      .then((index) => {
        const bySlug = {};
        index.forEach((entry) => { bySlug[entry.slug] = entry; });
        grid.innerHTML = slugs
          .filter((s) => bySlug[s])
          .map((s) => {
            const c = bySlug[s];
            return '<a class="cmd-card" href="./?page=command&cmd=' + encodeURIComponent(c.slug) + '">' +
              '<div class="cmd-card-head"><code class="cmd-card-name">' + c.name + '</code>' +
              '<span class="badge badge-' + c.difficulty + '">' + c.difficulty.charAt(0).toUpperCase() + c.difficulty.slice(1) + '</span></div>' +
              '<p class="cmd-card-desc">' + c.desc + '</p>' +
              '<div class="cmd-card-foot"><span class="cmd-card-cat">' + c.categoryName + '</span></div></a>';
          })
          .join('') || '<p class="empty-note">Bookmarked commands no longer exist.</p>';
      })
      .catch(() => { grid.innerHTML = '<p class="empty-note">Could not load bookmarks.</p>'; });
  }

  function syncBookmarkButtons() {
    const saved = getBookmarks();
    document.querySelectorAll('[data-bookmark]').forEach((btn) => {
      const slug = btn.getAttribute('data-bookmark');
      const isSaved = saved.includes(slug);
      btn.classList.toggle('bookmarked', isSaved);
      btn.setAttribute('aria-pressed', isSaved ? 'true' : 'false');
      const label = btn.querySelector('[data-bookmark-label]');
      if (label) label.textContent = isSaved ? 'Bookmarked' : 'Bookmark';
    });
  }

  document.addEventListener('click', (ev) => {
    const btn = ev.target.closest('[data-bookmark]');
    if (!btn) return;
    const slug = btn.getAttribute('data-bookmark');
    let saved = getBookmarks();
    if (saved.includes(slug)) {
      saved = saved.filter((s) => s !== slug);
    } else {
      saved.push(slug);
    }
    lsSet(LS.bookmarks, saved);
    syncBookmarkButtons();
  });

  /* ---------- recently viewed ---------- */
  function trackViewed() {
    const doc = document.getElementById('command-doc');
    if (!doc) return;
    const slug = doc.getAttribute('data-slug');
    if (!slug) return;
    let viewed = lsGet(LS.viewed, []).filter((s) => s !== slug);
    viewed.unshift(slug);
    lsSet(LS.viewed, viewed.slice(0, 12));
  }

  function renderViewed() {
    const grid = document.getElementById('recently-viewed-grid');
    if (!grid) return;
    const viewed = lsGet(LS.viewed, []);
    if (!viewed.length) {
      grid.innerHTML = '<p class="empty-note">' + (grid.dataset.emptyNote || 'Nothing viewed yet.') + '</p>';
      return;
    }
    fetch('./data/commands-index.json')
      .then((r) => r.json())
      .then((index) => {
        const bySlug = {};
        index.forEach((entry) => { bySlug[entry.slug] = entry; });
        grid.innerHTML = viewed
          .filter((s) => bySlug[s])
          .map((s) => {
            const c = bySlug[s];
            return '<a class="cmd-card" href="./?page=command&cmd=' + encodeURIComponent(c.slug) + '">' +
              '<div class="cmd-card-head"><code class="cmd-card-name">' + c.name + '</code>' +
              '<span class="badge badge-' + c.difficulty + '">' + c.difficulty.charAt(0).toUpperCase() + c.difficulty.slice(1) + '</span></div>' +
              '<p class="cmd-card-desc">' + c.desc + '</p></a>';
          })
          .join('') || '<p class="empty-note">Nothing viewed yet.</p>';
      })
      .catch(() => { grid.innerHTML = '<p class="empty-note">Could not load history.</p>'; });
  }

  /* ---------- learning progress ---------- */
  function renderProgress() {
    const pathCmds = document.querySelectorAll('[data-path-cmd]');
    if (!pathCmds.length) return;
    const visited = lsGet(LS.viewed, []);
    const progress = lsGet(LS.progress, {});
    const perPath = {};

    pathCmds.forEach((a) => {
      const path = a.getAttribute('data-path-cmd');
      perPath[path] = perPath[path] || { total: 0, done: 0 };
      perPath[path].total += 1;
      const href = a.getAttribute('href') || '';
      const match = href.match(/cmd=([a-z0-9+-]+)/);
      if (match && visited.indexOf(match[1]) !== -1) {
        a.classList.add('visited');
        perPath[path].done += 1;
      }
    });

    Object.keys(perPath).forEach((path) => {
      const label = document.querySelector('[data-path-progress="' + path + '"]');
      if (label) label.textContent = perPath[path].done + ' / ' + perPath[path].total + ' commands';
    });

    /* mark whole steps done */
    document.querySelectorAll('.path-steps').forEach((ol) => {
      ol.querySelectorAll('.path-step').forEach((step) => {
        const cmds = step.querySelectorAll('.path-cmd');
        if (!cmds.length) return;
        const doneCount = step.querySelectorAll('.path-cmd.visited').length;
        const tag = step.querySelector('[data-step-progress]');
        if (tag) tag.textContent = doneCount + '/' + cmds.length;
        step.classList.toggle('done', doneCount === cmds.length);
      });
    });
    void progress;
  }

  /* ---------- random command ---------- */
  function randomCommand() {
    fetch('./data/commands-index.json')
      .then((r) => r.json())
      .then((index) => {
        if (!index.length) return;
        const pick = index[Math.floor(Math.random() * index.length)];
        window.location.href = './?page=command&cmd=' + encodeURIComponent(pick.slug);
      })
      .catch(() => { /* stay put */ });
  }
  ['random-command', 'random-command-2'].forEach((id) => {
    const btn = document.getElementById(id);
    if (btn) btn.addEventListener('click', randomCommand);
  });

  /* ---------- init ---------- */
  trackViewed();
  syncBookmarkButtons();
  renderBookmarksPage();
  renderViewed();
  renderProgress();
})();
