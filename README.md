# LINUXORA — The Complete Linux Command Encyclopedia

A complete, professional Linux documentation website. **147 commands** across **20 categories** with 524 worked examples, 748 documented flags, instant search, a simulated terminal, learning roadmaps, and interactive educational tools.

Built exclusively with **HTML5, CSS3, vanilla JavaScript (ES6+), and PHP 8**. No frameworks, no database, no build step. Runs on any ordinary shared host (cPanel + Apache).

---

## Features

- **Command encyclopedia** — every command documents description, full explanation, use cases, syntax, flags with explanations, multiple examples with expected terminal output and output explanations, common mistakes, warnings, distribution compatibility (GNU vs BSD noted), related commands, difficulty, and official documentation links.
- **20 categories, 99 subcategories** — from File & Directory Management to Advanced Linux (namespaces, cgroups, /proc).
- **Instant search** — client-side, keyboard-navigable (`Ctrl`+`K`), with highlighting, category grouping, recent searches, and task-based matching ("copy files" → cp, rsync, scp).
- **Simulated terminal** — a safe, browser-only educational shell with a virtual in-memory filesystem (`help`, `pwd`, `ls`, `cd`, `mkdir`, `touch`, `cat`, `echo`, `clear`, `whoami`, `uname`, `date`, `history`, plus `rm`, `rmdir`, `cp`, `mv`). Clearly labeled as a simulation; no PHP executes anything.
- **Learning features** — bookmarks, recently viewed, per-path progress, three roadmaps (beginner / intermediate / advanced), command of the day, random discovery. All stored in `localStorage`; no registration.
- **Tools** — Permissions Calculator (rwx ↔ octal with explanation), Cron Expression Builder, File Size Converter (decimal vs binary), Distro Reference (7 distributions), Common Linux Paths (15 paths).
- **Design** — dark-first (deep charcoal, purple + cyan accents), glassmorphism, terminal-inspired components, full light/dark theme system, CSS variables throughout, reduced-motion support, semantic HTML with ARIA and keyboard navigation.

## Project structure

```
linuxora/
├── index.php              # front controller (query-string routing)
├── .htaccess              # security headers, caching, optional pretty URLs
├── assets/
│   ├── css/
│   │   ├── style.css      # variables, reset, layout, typography, themes
│   │   ├── components.css # hero, cards, terminal, search, tools, paths
│   │   └── responsive.css # laptop / tablet / phone / print
│   └── js/
│       ├── app.js         # theme, TOC + scroll-spy, copy, bookmarks, progress
│       ├── search.js      # Ctrl+K overlay + home inline search
│       ├── terminal.js    # simulated terminal (virtual filesystem)
│       ├── navigation.js  # sidebar drawer, category tree
│       └── tools.js       # permissions calculator, cron, size converter
├── data/
│   ├── commands/          # one JSON file per command (147 files)
│   ├── categories.json    # 20 categories with intros, concepts, subcategories
│   ├── commands-index.json# search index
│   ├── learning-paths.json# three roadmaps
│   └── stats.json         # real site statistics (used on the homepage)
├── includes/
│   ├── functions.php      # helpers: escaping, JSON loading, routing data
│   ├── header.php         # <head>, topbar, layout opening
│   ├── sidebar.php        # navigation with expandable category tree
│   ├── footer.php         # footer + scripts
│   └── command-view.php   # full command documentation renderer
└── pages/
    ├── home.php  categories.php  category.php  command.php
    ├── getting-started.php  fundamentals.php  popular.php  recent.php
    ├── bookmarks.php  learning.php  tools.php  about.php
```

## Deploying to cPanel shared hosting

1. **Upload the files.** In cPanel open **File Manager**, navigate to `public_html` (or a subfolder such as `public_html/linuxora`). Upload the project files so that `index.php` and `.htaccess` sit directly in that directory. You can upload the ZIP and use **Extract**.

2. **PHP version.** In cPanel open **Select PHP Version** (or *MultiPHP Manager*) and choose **PHP 8.0 or newer**. No PHP extensions are needed beyond the defaults (`json` is enabled everywhere).

3. **That's it.** Visit your domain. The site works immediately:
   - `https://yourdomain.com/` → homepage
   - `https://yourdomain.com/?page=command&cmd=cp` → the `cp` command
   - `https://yourdomain.com/?page=category&cat=networking` → Networking category

   If mod_rewrite is available, `https://yourdomain.com/commands/cp` also works (optional bonus; the site itself always uses query-string links, so nothing breaks without it).

4. **Permissions.** All files can be `644`, directories `755`. Nothing needs to be writable — the site never writes to disk.

5. **HTTPS.** Enable AutoSSL / Let's Encrypt in cPanel as usual.

### Local testing without a server

Opening `index.php` directly in a browser will not work (PHP must be executed). Options:

- Any PHP-capable host (upload first, test live), or
- PHP locally: `php -S localhost:8080` inside the project folder, then open `http://localhost:8080`.

## Updating content

- **Add or edit a command:** create/modify `data/commands/<slug>.json` following the existing schema (`slug`, `name`, `category`, `subcategory`, `difficulty`, `description`, `syntax`, `flags[]`, `examples[]`, `related[]`, `tags[]`, `docs`, and optional `about`, `useCases`, `mistakes`, `warnings`, `distroNotes`).
- **Regenerate the search index and stats:** after changing data, update `data/commands-index.json` and `data/stats.json` (the `build/` directory contains the generator used to produce everything and is **not** needed on the server — do not upload it, or keep it; `.htaccess` blocks access either way).
- **Categories:** edit `data/categories.json` (names, intros, concepts, subcategory groupings).

## Security notes

- All user input (query parameters) is strictly validated — page names are whitelisted, slugs are filtered to `a-z0-9+-`, and every dynamic value is HTML-escaped via `htmlspecialchars`.
- The simulated terminal runs **only in JavaScript** against an in-memory virtual filesystem. The PHP layer contains **no** `exec`, `shell_exec`, `system`, `passthru`, or any other command execution — it only reads JSON files and renders HTML.
- `includes/` and `build/` are blocked in `.htaccess`; directory listing is disabled.

## Accuracy

Documentation was written to be technically accurate: no invented options or outputs, GNU vs BSD differences noted where relevant, and every command page links to official upstream documentation (GNU manuals, man7.org, kernel.org, openssh.com, and distribution-specific sources).
