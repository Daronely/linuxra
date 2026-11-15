/* LINUXORA — navigation.js: sidebar drawer, category tree, section toggles. */
(function () {
  'use strict';

  const sidebar = document.getElementById('sidebar');
  const backdrop = document.getElementById('sidebar-backdrop');
  const toggleBtn = document.getElementById('sidebar-toggle');
  const closeBtn = document.getElementById('sidebar-close');

  function openDrawer() {
    if (!sidebar) return;
    sidebar.classList.add('drawer-open');
    if (backdrop) backdrop.hidden = false;
    if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
  }

  function closeDrawer() {
    if (!sidebar) return;
    sidebar.classList.remove('drawer-open');
    if (backdrop) backdrop.hidden = true;
    if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
  }

  if (toggleBtn) toggleBtn.addEventListener('click', openDrawer);
  if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
  if (backdrop) backdrop.addEventListener('click', closeDrawer);

  document.addEventListener('keydown', (ev) => {
    if (ev.key === 'Escape' && sidebar && sidebar.classList.contains('drawer-open')) {
      closeDrawer();
      if (toggleBtn) toggleBtn.focus();
    }
  });

  /* Category tree: expand/collapse individual categories. */
  document.querySelectorAll('.cat-toggle').forEach((btn) => {
    btn.addEventListener('click', () => {
      const item = btn.closest('.cat-item');
      if (!item) return;
      const open = item.classList.toggle('open');
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  });

  /* Top-level "Command Categories" section toggle. */
  document.querySelectorAll('[data-nav-section]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const tree = document.getElementById('cat-tree');
      if (!tree) return;
      const open = tree.dataset.open === '1';
      tree.dataset.open = open ? '0' : '1';
      btn.setAttribute('aria-expanded', open ? 'false' : 'true');
    });
  });

  /* Highlight the sidebar subcategory matching the current hash. */
  function highlightSub() {
    const hash = decodeURIComponent(window.location.hash || '');
    document.querySelectorAll('.sub-link').forEach((a) => {
      a.classList.toggle('active', a.getAttribute('href') === ('./' + hash) || a.getAttribute('href').endsWith(hash));
    });
    if (hash) {
      const target = document.querySelector(hash);
      if (target) {
        const catItem = target.closest('.cat-item');
        if (catItem) catItem.classList.add('open');
      }
    }
  }
  highlightSub();
  window.addEventListener('hashchange', highlightSub);
})();
