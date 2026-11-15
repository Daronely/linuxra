<?php
/** LINUXORA — document head, topbar, and layout opening. */
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle ?? 'LINUXORA') ?></title>
  <meta name="description" content="<?= e($pageDesc ?? 'The complete Linux command encyclopedia.') ?>">
  <meta name="theme-color" content="#0b0d12">
  <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='7' fill='%230b0d12'/%3E%3Cpath d='M9 11l5 5-5 5' stroke='%237c6cff' stroke-width='2.6' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3Cpath d='M17 21h6' stroke='%2322d3ee' stroke-width='2.6' stroke-linecap='round'/%3E%3C/svg%3E">
  <link rel="stylesheet" href="./assets/css/style.css">
  <link rel="stylesheet" href="./assets/css/components.css">
  <link rel="stylesheet" href="./assets/css/responsive.css">
  <script>
    /* Theme before first paint to avoid a flash of the wrong theme. */
    (function () {
      try {
        var saved = localStorage.getItem('linuxora-theme');
        if (saved === 'light' || saved === 'dark') {
          document.documentElement.setAttribute('data-theme', saved);
        }
      } catch (err) { /* private mode — default dark stays */ }
    })();
  </script>
</head>
<body>
<a class="skip-link" href="#main">Skip to main content</a>

<div class="shell">
  <div class="topbar">
    <button class="icon-btn mobile-only" id="sidebar-toggle" aria-label="Open navigation menu" aria-expanded="false" aria-controls="sidebar">
      <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
    </button>
    <a class="topbar-logo mobile-only" href="<?= e(url('home')) ?>">
      <span class="logo-mark" aria-hidden="true">❯_</span><span class="logo-text">LINUXORA</span>
    </a>
    <button class="search-trigger" id="search-trigger" type="button" aria-label="Search commands (Ctrl+K)">
      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
      <span class="search-trigger-label">Search commands…</span>
      <kbd>Ctrl</kbd><kbd>K</kbd>
    </button>
    <div class="topbar-actions">
      <button class="icon-btn" id="theme-toggle" type="button" aria-label="Switch to light theme">
        <svg class="icon-sun" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.9 4.9l1.4 1.4m11.4 11.4l1.4 1.4M2 12h2m16 0h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
        <svg class="icon-moon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>
      </button>
    </div>
  </div>
  <div class="layout">
