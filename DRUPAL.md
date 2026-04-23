# DRUPAL.md — Drupal Configuration Reference

## Environment

- **Drupal version:** 10
- **Local dev:** Lando (`lando start`, `lando drush`)
- **Hosting:** Pantheon
- **Web root:** `public_html/`
- **Theme:** `public_html/themes/custom/surface/`
- **Modules (custom):** `public_html/modules/custom/ourkids_outreach/`

---

## Key Commands

```bash
drush cr                    # Clear all caches — run after any config/template/PHP change
drush cim                   # Import config from sync/
drush cex                   # Export config to sync/
drush en module_name        # Enable a module
drush pmu module_name       # Uninstall a module
```

---

## Content Types

| Type | Machine name | Used for |
|---|---|---|
| Basic Page | `page` | Homepage, About, News & Updates, any landing page |
| Article | `article` | News articles, blog posts |
| Event | `event` | Campaign events, legislative sessions |
| Person | `person` | Coalition member profiles |
| Organization | `organization` | NGO entity (OurKidsOurSchoolsVT) |
| Resource | — | Replaced by `resource_item` paragraph type |

---

## Paragraph Types

| Bundle | Machine name | Attaches to | Notes |
|---|---|---|---|
| Hero Section | `hero_section` | Page `schema_main_entity` | Homepage only in practice |
| Issue Section | `issue_section` | Page `schema_main_entity` | |
| Stakes Section | `stakes_section` | Page `schema_main_entity` | |
| Positions Section | `positions_section` | Page `schema_main_entity` | |
| Accountability Section | `accountability_section` | Page `schema_main_entity` | |
| Myth Section | `myth_section` | Page `schema_main_entity` | |
| Voices Section | `voices_section` | Page `schema_main_entity` | |
| Action Section | `action_section` | Page `schema_main_entity` | Contains legislator contact tool |
| Resource List | `resource_list` | Page `schema_main_entity` or `field_sections` | Container for resource_items |
| Resource Item | `resource_item` | `resource_list.field_resource_items` | Child paragraph |
| Events Section | `events_section` | Page `schema_main_entity` | |
| Event Item | `event_item` | `events_section.field_events` | Child paragraph |

---

## Key Fields

### Basic Page (`page`) content type

| Field | Machine name | Type | Notes |
|---|---|---|---|
| Layout | `schema_main_entity` | Entity ref (Paragraph) | Primary paragraph field — labeled "Layout" in edit UI |
| Sections | `field_sections` | Entity ref (Paragraph) | Supplementary sections |
| Image | `schema_image` | Media | |
| About | `schema_about` | Entity ref (Node) | Points to Organization node |

### Resource Item paragraph fields (all `schema_` prefix)

| Field | Machine name | Type |
|---|---|---|
| Title | `schema_name` | Text (plain) |
| URL | `schema_url` | Link (external) |
| Summary | `schema_description` | Text (plain, long) |
| Type | `schema_type` | List (text): opinion/news/legislation/government/research |
| Publisher | `schema_publisher` | Text (plain) |
| Date | `schema_date_published` | Date |

### Person content type fields

| Field | Machine name | Type |
|---|---|---|
| Name | `schema_name` | Text (plain) |
| Job Title | `schema_job_title` | Text (plain) |
| Location | `schema_address` | Text (plain) — locality only |
| Bio | `schema_description` | Text (long) |
| Image | `schema_image` | Media |
| Quote | `field_quote` | Text (plain, long) |
| School Connection | `field_school_connection` | Text (plain) |

---

## Libraries Configuration (`surface.libraries.yml`)

Pattern for a new library:
```yaml
component-name:
  css:
    component:
      dist/css/component-name.css: {}
  js:
    dist/js/component-name.js: {}    # only if JS needed
  dependencies:
    - surface/section                 # list any dependencies
    - surface/resource-item
```

Libraries are attached in Twig via `{{ attach_library('surface/library-name') }}` or in paragraph templates.

The Action Section library currently depends on:
```yaml
action-section:
  css:
    component:
      dist/css/action-section.css: {}
  js:
    dist/js/action-section.js: {}
  dependencies:
    - surface/section
    - surface/step-item
    - surface/legislator-contact
```

---

## Modules

### SchemaBlueprints
Maps Drupal content type fields to Schema.org structured data. Configuration in `sync/` as `schemadotorg.schemadotorg_mapping.*.yml` files. The `schema_` field prefix convention is how content editors and developers know which fields are schema-mapped.

### Components (module)
Enables Twig namespace support via `surface.info.yml` `components.namespaces` configuration. Required for `@elements/`, `@collections/` etc. to work in Drupal.

### Twig Tweak
Provides `drupal_entity()`, `drupal_menu()`, `drupal_block()` and other Twig functions used in templates.

### Twig Field Value
Provides `|field_value` filter for rendering fields without wrapper markup.

### ourkids_outreach (custom)
Route: `/admin/ourkids/legislator-outreach`
- Reads `vt-legislators.json` from theme data directory
- Tabbed House/Senate legislator list with preview modal
- Sends HTML emails via Drupal Mail API (Brevo SMTP)
- Tracks outreach progress in localStorage
- Ref token format: `{initials}-{chamber}-{district_initials}`

---

## Webforms

### Testimonial Submission (`testimonial_submission`)
- Route: `/share-your-story`
- Template: `webform--testimonial-submission.html.twig`
- Import/export: `drush webform:import` / `drush webform:export`

---

## Theme Settings

Available at **Appearance → Surface → Settings**:

- `civic_api_key` — Google Civic Information API key (fallback for legislator lookup when JSON data fails)

Accessed in PHP via `theme_get_setting('civic_api_key')` and passed to templates via `surface_preprocess_paragraph()`.

---

## Config Sync

Configuration lives in `sync/sync/`. Key files:

- `field.field.node.page.schema_main_entity.yml` — Layout field config including allowed paragraph bundles
- `field.field.node.page.field_sections.yml` — Sections field config
- `field.field.paragraph.resource_list.field_resource_items.yml` — Resource list field config
- `schemadotorg.schemadotorg_mapping.paragraph.resource_item.yml` — Schema mapping for resource items
- `core.entity_view_display.node.page.default.yml` — Which fields are enabled/disabled in display

After Junie makes Drupal config changes, export with `drush cex` and commit `sync/` changes.

---

## Deployment Checklist

After frontend changes:
1. `npm run vite:build` (from theme directory)
2. `drush cr`

After Drupal config changes:
1. `drush cex` to export
2. Commit `sync/` directory
3. On server: `drush cim` then `drush cr`

After adding a new library:
1. Add to `surface.libraries.yml`
2. `npm run vite:build`
3. `drush cr`

After adding a new paragraph type:
1. Create paragraph template in `templates/paragraphs/`
2. Create Storybook component in `source/patterns/`
3. Add library to `surface.libraries.yml`
4. Ensure paragraph bundle is in the allowed bundles list of the field it attaches to
5. `npm run vite:build` + `drush cr`
