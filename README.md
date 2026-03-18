# WP Query Builder UI

A WordPress plugin that lets developers build complete `WP_Query` arguments through a friendly admin UI, then generate ready-to-use PHP code and/or a WordPress shortcode.

## Features

- Visual builder covering all major `WP_Query` parameter groups:
  - Basic (post type, status, post count)
  - Author
  - Date query (with nested date conditions)
  - Meta query (custom field conditions)
  - Taxonomy query (multiple tax terms with relation logic)
  - Order / Orderby
  - Pagination (posts per page, offset, paged)
  - Parent post
  - Post include/exclude picker
  - Search (`s`)
  - Advanced (mime type, comment count, permissions, etc.)
- PHP code generation with syntax highlighting
- Shortcode generation (opt-in via Settings)
- Save, duplicate, and delete named queries
- Live query preview (returns matched post titles)
- Dark mode support

## Requirements

- WordPress 5.5+
- PHP 7.4+

## Installation

1. Upload the `WPQueryBuilderUI` folder to `wp-content/plugins/`.
2. Activate the plugin from **Plugins > Installed Plugins**.
3. Navigate to **Query Builder** in the WordPress admin menu.

## Usage

### Builder

Go to **Query Builder > Builder**, configure your query parameters across the available sections, then click **Generate Code** to produce the PHP snippet or shortcode string.

### Saved Queries

Queries can be saved with a name from the Builder page. All saved queries are listed under **Query Builder > Saved Queries** where you can load, duplicate, or delete them.

### Shortcode

The shortcode feature is disabled by default. Enable it under **Query Builder > Settings**, then use the tag in any post or page:

```
[wpqbui id="QUERY_ID"]
```

Optional `template` attribute — path relative to the active theme:

```
[wpqbui id="42" template="wpqbui/my-template.php"]
```

If no custom template is provided the plugin falls back to `wpqbui/query-results.php` in the active theme, and then to its own bundled `partials/shortcode-results.php`.

## Architecture

```
WPQueryBuilderUI/
├── wpqbui-plugin.php          # Entry point, constants, bootstrap
├── wpqbui-settings.php        # Settings registration
├── assets/
│   ├── css/                   # admin.css, dark.css
│   └── js/                    # Module JS files (codegen, AJAX, sections)
├── includes/
│   ├── admin/                 # Admin page controller
│   ├── ajax/                  # Individual AJAX action handlers
│   ├── codegen/               # PHP + shortcode code generators
│   ├── query/                 # Definition, sanitizer, validator, repository, previewer
│   └── shortcode/             # Front-end [wpqbui] shortcode handler
└── partials/
    ├── admin-page-*.php       # Top-level admin page templates
    ├── builder/               # Per-section builder partials
    └── shortcode-results.php  # Default front-end output template
```

All AJAX requests are routed through `class-wpqbui-ajax-handler.php` using a single nonce (`wpqbui_ajax`).

Saved queries are stored in the custom database table `{prefix}wpqbui_queries`, created on activation.

## License

WP Query Builder UI is licensed under the **GNU General Public License v2.0 or later**.
See [LICENSE](LICENSE) for the full text.
