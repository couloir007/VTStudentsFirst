<!-- <?php // phpcs:ignoreFile ?> -->
# CKEditor 5 Block Embed

A Drupal 10.3+/11 module that adds an **"Embed Block"** toolbar button to CKEditor 5,
letting editors embed any content block, view block, or active-theme region block
directly inside a rich-text field — without writing a line of shortcode.

Blocks are selected through a modal dialog, stored as a `<ck5-block-embed>` HTML
element in the body, and rendered live on the front end by a pluggable
`Ck5BlockEmbed` render plugin system. The editor preview reflects the rendered
output in real time.

---

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
  - [Enable the Filter](#enable-the-filter)
  - [Add the Toolbar Button](#add-the-toolbar-button)
  - [Permissions](#permissions)
- [Built-in Block Types](#built-in-block-types)
- [Creating a Custom Plugin](#creating-a-custom-plugin)
- [How It Works](#how-it-works)
  - [Editor Flow](#editor-flow)
  - [Filter Processing](#filter-processing)
- [Alter Hook](#alter-hook)
- [Hook Help](#hook-help)
- [Routes](#routes)
- [Troubleshooting](#troubleshooting)
- [Security](#security)
- [File Structure](#file-structure)
- [Testing](#testing)
- [Maintainers](#maintainers)

---

## Features

- **Three built-in block types** — Content blocks (`block_content`), View blocks
  (any block display in any enabled view), and Theme blocks (blocks placed in
  the active theme's regions)
- **Live editor preview** — the rendered block output is displayed inside the
  CKEditor canvas immediately after insertion, via a CSRF-protected preview
  endpoint
- **Pluggable render system** — any module can provide its own `#[Ck5BlockEmbed]`
  plugin to add new block types without touching this module
- **Alter hook** — `hook_ck5_block_embed_info_alter()` lets other modules modify
  the list of available plugins at runtime
- **Inline and block variants** — plugins declare `isInline(): bool` to control
  whether the embed is inserted as an inline or block-level element
- **Bubbleable metadata** — cache tags, cache contexts, and JS/CSS library
  attachments produced during block rendering are bubbled up to the
  `FilterProcessResult` so nothing is silently lost
- **Safe rendering** — every block render runs inside a `RenderContext` so
  early-rendering side effects are captured, not discarded
- **Dependency-injected** — all services use interface type-hints and proper DI;
  no `\Drupal::` static calls in any plugin or controller
- **CSRF-protected preview** — the preview route requires a per-editor CSRF
  token and validates the text-format permission before rendering
- **Permission-gated toolbar** — users without `use ck5 block embed button` never
  see the toolbar item, enforced both server-side and via
  `hook_editor_js_settings_alter()`

---

## Requirements

| Requirement | Version |
|---|---|
| PHP | 8.1 or later |
| Drupal | 10 or 11 |
| [CKEditor 5](https://www.drupal.org/docs/core-modules-and-themes/core-modules/ckeditor-5-module) | Drupal core |

To use **View blocks** the `views` core module must be enabled.
To use **Content blocks** the `block_content` core module must be enabled.
To use **Theme blocks** the `block` core module must be enabled and blocks must
be placed in the active theme's regions.

---

## Installation

### 1. Install via Composer

```bash
composer require drupal/ck5_block_embed
```

This is the recommended method. Composer will download the module and place it
in `modules/contrib/ck5_block_embed` automatically.

### 2. Enable the module

```bash
drush en ck5_block_embed
drush cr
```

---

## Configuration

### Enable the Filter

1. Go to **Admin → Configuration → Content authoring → Text formats and
   editors** (`/admin/config/content/formats`).
2. Click **Configure** next to the text format you want to use (e.g. *Full HTML*).
3. Under **Enabled filters**, check **Embed blocks**.
4. Save the format.

> **Important:** The toolbar button will insert embeds into the editor, but they
> will only be rendered on the front end if the **Embed blocks** filter is enabled
> on the same text format. Without the filter the raw `<ck5-block-embed>` tag is
> stored and displayed as-is.

### Add the Toolbar Button

On the same text format configuration page:

1. Drag the **Embed Block** button from the *Available buttons* area into the
   active toolbar.
2. Save the format.

The button will appear in the CKEditor toolbar for any user who has both access
to the text format and the `use ck5 block embed button` permission.

### Permissions

Go to **Admin → People → Permissions** (`/admin/people/permissions`) and grant
the following permission to the appropriate roles:

| Permission | Description |
|---|---|
| `use ck5 block embed button` | Allows the user to open the embed dialog and insert blocks |

Users who lack this permission will not see the toolbar button even if it is
configured on their text format — the button is stripped from the editor settings
at runtime via `hook_editor_js_settings_alter()`.

---

## Built-in Block Types

The module ships three `@Ck5BlockEmbed` plugins, each selectable from the embed
dialog.

### Content Block (`content_block`)

Embeds a `block_content` entity by its numeric ID.

- Lists all content blocks grouped by bundle in the dialog.
- Checks `block_content->access('view')` before rendering.
- Renders via `EntityViewBuilderInterface::view()`.

### View Block (`view_block`)

Embeds any block display from an enabled View.

- Lists all enabled views that have at least one block display, grouped by view
  label.
- Stores the selection as `{"view_id":"…","display_id":"…"}` JSON.
- Attaches `config:views.view.<view_id>` cache tags so the embed is invalidated
  when the view configuration changes.
- Uses `Views::getView()` and `$view->access($display_id)` before rendering.

### Theme Block (`theme_block`)

Embeds a block that is placed in the active theme's regions.

- Lists all blocks placed in the default theme, grouped by region.
- Stores the selection as `{"theme":"…","block_id":"…"}` JSON.
- Validates that the stored theme still matches the active block's theme before
  rendering, so stale config fails gracefully.

---

## Creating a Custom Plugin

Any module can contribute additional embed types by adding the
`#[Ck5BlockEmbed]` attribute and extending `Ck5BlockEmbedPluginBase`.
The legacy `@Ck5BlockEmbed` annotation is still discovered for backwards
compatibility, but new plugins should use the attribute.

### 1. Create the plugin class

Place the file in `src/Plugin/Ck5BlockEmbed/` inside your module.

```php
<?php

namespace Drupal\mymodule\Plugin\Ck5BlockEmbed;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ck5_block_embed\Attribute\Ck5BlockEmbed;
use Drupal\ck5_block_embed\Ck5BlockEmbedInterface;
use Drupal\ck5_block_embed\Ck5BlockEmbedPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Embeds a custom promotional banner.
 */
#[Ck5BlockEmbed(
  id: 'promo_banner',
  label: new TranslatableMarkup('Promo Banner'),
  description: new TranslatableMarkup('Embed a promotional banner by ID.'),
)]
class PromoBanner extends Ck5BlockEmbedPluginBase implements Ck5BlockEmbedInterface, ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return ['banner_id' => NULL];
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $id = $this->configuration['banner_id'];
    if (!$id) {
      return ['#markup' => $this->t('No banner selected.')];
    }
    return [
      '#theme' => 'promo_banner',
      '#banner_id' => $id,
      '#cache' => ['tags' => ['promo_banner:' . $id]],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['banner_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Banner'),
      '#options' => [1 => 'Summer Sale', 2 => 'Holiday Deals'],
      '#default_value' => $this->configuration['banner_id'],
      '#required' => TRUE,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function isInline(): bool {
    return FALSE;
  }

}
```

### 2. Plugin attribute keys

| Key | Type | Required | Description |
|---|---|---|---|
| `id` | `string` | Yes | Machine name; must be unique across all `@Ck5BlockEmbed` plugins |
| `label` | `TranslatableMarkup` | Yes | Human-readable name shown in the dialog's *Block category* select |
| `description` | `TranslatableMarkup` | No | Optional longer description |

### 3. Interface contract

Your plugin class must implement:

| Method | Description |
|---|---|
| `build(): array` | Return the render array for the block |
| `buildConfigurationForm(array $form, FormStateInterface $form_state): array` | Return the dialog form fields |
| `isInline(): bool` | Return `TRUE` if the embed should be inserted as an inline element |
| `defaultConfiguration(): array` | Return default configuration values |

`validateConfigurationForm()`, `submitConfigurationForm()`, and
`massageFormValues()` all have no-op defaults in `Ck5BlockEmbedPluginBase` and
only need to be overridden when custom logic is required.

---

## How It Works

### Editor Flow

```
1. Editor clicks the "Embed Block" toolbar button
      ↓
2. CKEditor JS opens a modal dialog via the Drupal off-canvas / dialog system
   GET /ck5-block-embed/dialog/default/{filter_format}
      ↓
3. Ck5BlockEmbedDialogForm renders:
   - If more than one plugin is registered → "Block category" select element
   - If exactly one plugin → the category select is hidden (value is fixed)
   - The selected plugin's buildConfigurationForm() output appears below
      ↓
4. Editor selects a block and clicks "Embed block"
   AJAX POST → ajaxSubmitForm() callback
      ↓
5. An EditorDialogSave Ajax command is dispatched carrying:
     data-plugin-id     = "content_block"      (selected plugin ID)
     data-plugin-config = {"block_id":5}        (JSON-encoded plugin config)
     data-button-id     = "default"
      ↓
6. CKEditor JS inserts a <ck5-block-embed> element into the document
      ↓
7. The editing plugin fires a preview request:
   POST /ck5-block-embed/preview/{editor}?token=<csrf>
   Body: {"plugin_id":"content_block","plugin_config":"{\"block_id\":5}"}
      ↓
8. Ck5BlockEmbedPreviewController creates the plugin instance, calls build(),
   renders via renderRoot(), and returns the HTML as an HTTP 200 response
      ↓
9. CKEditor JS replaces the placeholder with the rendered preview HTML
```

### Filter Processing

When a node (or any entity using the text format) is rendered on the front end,
`Plugin/Filter/Ck5BlockEmbed::process()` handles all `<ck5-block-embed>` tags:

```
1. preg_match_all() finds all <ck5-block-embed …> tags in the text
      ↓
2. For each tag:
   a. Extract data-plugin-id  (plain string)
   b. Extract data-plugin-config (html_entity_decode → json_decode → array)
   c. If plugin ID is missing → skip (leave tag in place, continue loop)
      ↓
3. ck5BlockEmbedPluginManager->createInstance($plugin_id, $config)
      ↓
4. $instance->build() → render array
      ↓
5. renderer->executeInRenderContext(RenderContext, fn() => render($build))
   Bubbleable metadata (cache tags, attachments) popped from context
   and merged into FilterProcessResult
      ↓
6. str_replace($original_tag, $rendered_html, $text)
      ↓
7. On exception → tag replaced with a translatable error message;
   processing continues for remaining tags
      ↓
8. FilterProcessResult::setProcessedText($text) returned with all
   accumulated cache metadata
```

---

## Alter Hook

Use `hook_ck5_block_embed_info_alter()` to modify the list of available
`@Ck5BlockEmbed` plugin definitions before they are returned to the dialog or
the filter. This follows Drupal's standard plugin alter convention.

```php
/**
 * Implements hook_ck5_block_embed_info_alter().
 */
function mymodule_ck5_block_embed_info_alter(array &$definitions): void {
  // Remove the theme_block plugin for this site.
  unset($definitions['theme_block']);

  // Override the label of the view_block plugin.
  if (isset($definitions['view_block'])) {
    $definitions['view_block']['label'] = t('Curated Views');
  }
}
```

The alter hook is registered in `Ck5BlockEmbedPluginManager::__construct()`
via `$this->alterInfo('ck5_block_embed_info')`, which means Drupal automatically
calls `hook_ck5_block_embed_info_alter()` on any module that implements it.

---

## Hook Help

The module implements `hook_help()` in `ck5_block_embed.module`, providing
contextual help text at three locations in the Drupal admin UI.

### Main help page — `help.page.ck5_block_embed`

Accessible at **Admin → Help → CKEditor 5 Block Embed**
(`/admin/help/ck5_block_embed`). Rendered as a structured HTML page with
**About** and **Uses** sections covering:

| Topic | Content |
|---|---|
| About | One-paragraph description of what the module does and how embeds are stored and rendered |
| Enabling the filter | Linked step to `/admin/config/content/formats` with an explanation of why the filter is mandatory |
| Adding the toolbar button | How to drag the button into the active toolbar |
| Granting permissions | Linked step to `/admin/people/permissions` explaining the `use ck5 block embed button` permission |
| Inserting a block | Dialog workflow: select category → select block → click Embed block → live preview |
| Editing an existing embed | How to re-open the dialog for an already-inserted embed |
| Built-in block types | Bullet list of Content Block, View Block, and Theme Block with their module dependencies and links |
| Adding custom block types | Summary of the `@Ck5BlockEmbed` plugin system with a pointer to the README example |
| Modifying the available plugin list | Summary of `hook_ck5_block_embed_info_alter()` |

### Inline help — Text formats overview (`filter.admin_overview`)

A short paragraph shown at the top of `/admin/config/content/formats` reminding
administrators that the **Embed blocks** filter must be enabled on a text format
before embeds will render on the front end. Includes a link back to the main
help page.

### Inline help — Permissions page (`user.admin_permissions`)

A short paragraph shown at the top of `/admin/people/permissions` explaining
that the **Administer embed block** permission controls visibility of the
toolbar button. Includes a link back to the main help page.

### Implementation notes

- All `hook_help()` output is returned as a plain PHP string of safe HTML, as
  required by Drupal core's help system.
- Internal route URLs are generated via `Url::fromRoute()` so they respect the
  site's path prefix and language negotiation — never hardcoded strings.
- The function returns an empty string `''` for all unrecognised route names,
  which is the correct behaviour per the `hook_help()` contract.
- The `RouteMatchInterface $route_match` parameter is accepted but intentionally
  unused; it is available for sub-path context if needed in future.

---

## Routes

| Route name | Path | Access | Description |
|---|---|---|---|
| `ck5_block_embed.dialog` | `/ck5-block-embed/dialog/{ck5_block_embed_button}/{filter_format}` | `use ck5 block embed button` | Renders the embed dialog form (AJAX) |
| `ck5_block_embed.preview` | `/ck5-block-embed/preview/{editor}` | Text format permission + CSRF token | Returns rendered block HTML for the editor preview |
| `ck5_block_embed.ck5_block_embed_button.icon` | `/ck5-block-embed/icon/{ck5_block_embed_button}` | Public | Serves the toolbar button SVG icon |

The preview route requires `_csrf_token: TRUE` — CKEditor JS automatically
appends the CSRF token as a query parameter when it requests the preview URL,
which is generated per-editor by `Plugin/CKEditor5Plugin/Ck5BlockEmbed::getDynamicPluginConfig()`.

---

## Troubleshooting

**Toolbar button does not appear**

- Confirm the **Embed Block** button has been dragged into the active toolbar on
  the text format configuration page.
- Confirm the authenticated user has the `use ck5 block embed button` permission.
- Run `drush cr` after any configuration change.

---

**Embeds render as raw `<ck5-block-embed>` tags on the front end**

The **Embed blocks** filter is not enabled on the text format. Go to
`/admin/config/content/formats`, configure the format, check **Embed blocks**
under *Enabled filters*, and save.

---

**"No ck5 block embed plugins were found" in the dialog**

All three built-in plugins have module dependencies:

| Plugin | Required module |
|---|---|
| `content_block` | `block_content` |
| `view_block` | `views` |
| `theme_block` | `block` |

Enable the relevant core modules and rebuild caches (`drush cr`).

---

**"The plugin used for this ck5 block embed is not enabled"**

An existing embed references a plugin ID that is no longer available (e.g. a
module providing it was uninstalled). Either re-enable the module that provides
the plugin or recreate the embed using an available plugin.

---

**Preview shows "Incorrect configuration. Please recreate this CK5 block embed"**

The `plugin_id` or `plugin_config` in the preview request is invalid or the
plugin threw an exception. Delete the embed element in the editor and insert it
again. Check `/admin/reports/dblog` for any PHP exceptions thrown during preview
rendering.

---

**Dialog opens but the block select list is empty**

- For `content_block`: no `block_content` entities exist. Create a custom block
  at `/block/add`.
- For `view_block`: no enabled views have a block display. Add a block display in
  Views UI.
- For `theme_block`: no blocks are placed in the active theme's regions. Place
  blocks at `/admin/structure/block`.

---

## Security

| Threat | Mitigation |
|---|---|
| **Unauthorised dialog access** | The dialog route uses a custom access callback (`Ck5BlockEmbedDialogForm::checkAccess`) that enforces the `use ck5 block embed button` permission. |
| **Preview rendered by wrong user** | `Ck5BlockEmbedPreviewController::checkAccess` verifies the requesting user holds `use text format <id>` for the editor's filter format. The route also enforces `_csrf_token: TRUE`. |
| **XSS via plugin config** | `data-plugin-config` from the preview request body is passed through `Xss::filter()` before being decoded. Plugin IDs are also filtered via `Xss::filter()`. |
| **Raw block output bypassing the text format** | Block render arrays are rendered through Drupal's render pipeline. Twig's auto-escaping and Drupal's `#markup` / `#plain_text` conventions apply normally. |
| **CSRF on preview endpoint** | CKEditor JS includes a per-editor CSRF token in the preview URL. The route's `_csrf_token: TRUE` requirement validates it before the controller is invoked. |
| **Permission strip for toolbar** | `hook_editor_js_settings_alter()` removes the `ck5BlockEmbed__default` toolbar item from CKEditor JS settings for users lacking `use ck5 block embed button`, preventing client-side workarounds. |
| **Filter bypass** | The `ck5_block_embed` filter is `TYPE_TRANSFORM_REVERSIBLE`. Editors need to be granted the text format before any embed is rendered. No embed output bypasses the format's filter pipeline. |
| **Stale theme block config** | `ThemeBlock::build()` validates that the stored theme matches the loaded block's actual theme before rendering. A mismatch returns a safe fallback rather than an unintended block. |

---

## File Structure

```
ck5_block_embed/
├── ck5_block_embed.ckeditor5.yml       CKEditor 5 plugin declaration (deriver)
├── ck5_block_embed.info.yml            Module metadata and dependencies
├── ck5_block_embed.libraries.yml       JS/CSS library definitions
├── ck5_block_embed.module              hook_editor_js_settings_alter,
│                                       hook_module_preuninstall
├── ck5_block_embed.permissions.yml     'use ck5 block embed button' permission
├── ck5_block_embed.routing.yml         Dialog, preview, and icon routes
├── ck5_block_embed.services.yml        plugin.manager.ck5_block_embed service
├── css/
│   ├── ck5_block_embed.admin.css       Admin-only toolbar button styles
│   └── ck5_block_embed.css             Front-end embed styles
├── icons/
│   └── Ck5BlockEmbed.svg              Toolbar button icon (served by icon route)
├── js/
│   ├── build/
│   │   └── ck5BlockEmbed.js           Compiled CKEditor 5 plugin bundle
│   └── ckeditor5_plugins/ck5BlockEmbed/src/
│       ├── ck5BlockEmbed.js            Plugin entry point
│       ├── command.js                  InsertCk5BlockEmbed command
│       ├── editing.js                  Model/view conversion + preview refresh
│       ├── index.js                    Plugin exports
│       ├── toolbar.js                  Toolbar button definition
│       ├── ui.js                       Dialog open/close handling
│       └── utils.js                    Shared helpers
└── src/
    ├── Annotation/
    │   └── Ck5BlockEmbed.php           @Ck5BlockEmbed annotation (BC only)
    ├── Attribute/
    │   └── Ck5BlockEmbed.php           #[Ck5BlockEmbed] plugin attribute
    ├── Ck5BlockEmbedInterface.php      Plugin interface (build, isInline, …)
    ├── Ck5BlockEmbedPluginBase.php     Abstract base with default implementations
    ├── Ck5BlockEmbedPluginManager.php  DefaultPluginManager subclass
    ├── Controller/
    │   ├── Ck5BlockEmbedIconController.php     Serves the SVG toolbar icon
    │   └── Ck5BlockEmbedPreviewController.php  Renders a block for editor preview
    ├── Form/
    │   └── Ck5BlockEmbedDialogForm.php  AJAX dialog: plugin select + config form
    └── Plugin/
        ├── Ck5BlockEmbed/
        │   ├── ContentBlock.php        Embeds block_content entities
        │   ├── ThemeBlock.php          Embeds theme-placed blocks
        │   └── ViewBlock.php           Embeds view block displays
        ├── CKEditor5Plugin/
        │   └── Ck5BlockEmbed.php       Dynamic config: preview/dialog/icon URLs
        ├── Deriver/
        │   └── Ck5BlockEmbedDeriver.php  Generates the 'default' plugin derivative
        └── Filter/
            └── Ck5BlockEmbed.php       Text filter: replaces tags with rendered HTML
```

---

## Testing

Tests are in `tests/src/` and follow Drupal's PHPUnit conventions.

### Test structure

| File | Type | Covers |
|---|---|---|
| `Unit/Plugin/Ck5BlockEmbed/ContentBlockTest` | Unit | Default config, access check, fallback markup, success render via view builder |
| `Unit/Plugin/Ck5BlockEmbed/ThemeBlockTest` | Unit | Default config, invalid JSON, missing JSON keys, theme mismatch, success render |
| `Unit/Plugin/Ck5BlockEmbed/ViewBlockTest` | Unit | Default config, invalid JSON, missing `view_id`, missing `display_id` |
| `Unit/Plugin/Filter/Ck5BlockEmbedFilterTest` | Unit | No-op on plain text, tag replacement, HTML-entity config decoding, missing plugin ID skip, exception error string, multiple concurrent tags, `trustedCallbacks()` |
| `Unit/Controller/Ck5BlockEmbedIconControllerTest` | Unit | SVG response headers, SVG file content, `checkAccess` always allows |
| `Unit/Controller/Ck5BlockEmbedPreviewControllerTest` | Unit | Access allowed/denied by text-format permission, full render, config decoding, missing plugin ID, plugin exception fallback |

### Running tests

From the Drupal root:

```bash
# All tests for this module
vendor/bin/phpunit -c core/phpunit.xml.dist modules/custom/ck5_block_embed

# Unit tests only (fast, no Drupal bootstrap)
vendor/bin/phpunit -c core/phpunit.xml.dist modules/custom/ck5_block_embed/tests/src/Unit

# By test group
vendor/bin/phpunit -c core/phpunit.xml.dist --group ck5_block_embed
```

### What is tested

**Filter — no-op path** `testProcessReturnsUnchangedTextWhenNoEmbedTags()` confirms
that text with no `<ck5-block-embed>` tags passes through `process()` unchanged
and the plugin manager is never called.

**Filter — replacement pipeline** `testProcessReplacesEmbedTagWithRenderedOutput()`
mocks `executeInRenderContext()` and `render()`, asserts the tag is fully replaced
by the rendered output, and that the raw tag is absent from the result.

**Filter — HTML-entity config decoding** `testProcessDecodesHtmlEntityEncodedPluginConfig()`
verifies that HTML-encoded JSON in `data-plugin-config` (as browsers produce it)
is decoded to a plain array before being passed to `createInstance()`.

**Filter — missing plugin ID** `testProcessSkipsTagWithMissingPluginId()` asserts
that a tag without `data-plugin-id` is left in the body untouched and
`createInstance()` is never called.

**Filter — exception handling** `testProcessRendersErrorOnPluginException()` confirms
that a plugin manager exception produces an inline error string instead of
crashing the page and that the raw embed tag is removed.

**Preview — access control** `testCheckAccessAllowedWhenUserHasPermission()` and
`testCheckAccessDeniedWhenUserLacksPermission()` exercise both branches of the
`checkAccess()` callback against the text-format permission check.

**Preview — config forwarding** `testPreviewPassesDecodedPluginConfigToManager()`
asserts that the JSON string in the request body is decoded to an array before
`createInstance()` is called, not forwarded as a raw string.

---

## Maintainers

- **Module author:** [Sujan Shrestha](https://www.drupal.org/u/sujan-shrestha)
