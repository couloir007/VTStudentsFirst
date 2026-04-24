# CLAUDE.md — OurKidsOurSchoolsVT (Surface Theme)

## Project Context

**OurKidsOurSchoolsVT** (ourkidsourschoolsvt.org) is a Vermont advocacy campaign site built on Drupal 10. The coalition advocates for preserving school choice and tuitioning access in rural Vermont towns that depend on independent schools — St. Johnsbury Academy, Lyndon Institute, Burr and Burton Academy, Thetford Academy, and Riverside School.

The frontend is a custom theme called **surface** using Vite for asset bundling and Storybook for component development.

See the `docs/` directory for deeper documentation:
- `docs/TEMPLATES.md` — Drupal template system
- `docs/STORYBOOK.md` — Storybook component development
- `docs/COMPONENTS.md` — Component reference and conventions
- `../../DRUPAL.md` — Content types, paragraph types, modules, config sync (repo root)
- `../../modules/custom/ourkids_outreach/CLAUDE.md` — Legislator outreach module
- `../../modules/custom/ourkids_schemadotorg/CLAUDE.md` — Schema.org customization module

---

## Directory Layout

```
public_html/
  themes/custom/surface/         ← All frontend work happens here
    source/patterns/             ← Component source (twig, css, yml, stories)
      elements/                  ← Atoms
      components/                ← Molecules
      collections/               ← Page sections
      layouts/                   ← Page shells
    props/                       ← CSS custom properties
    dist/                        ← Vite build output — DO NOT edit directly
    data/                        ← JSON data files
    includes/                    ← PHP hook files
    .storybook/                  ← Storybook config
    surface.info.yml             ← Theme info + Twig namespace declarations
    surface.libraries.yml        ← Library definitions
    surface.theme                ← Main PHP file
  templates/                     ← Drupal Twig templates (separate from source)
    content/                     ← node--*.html.twig
    paragraphs/                  ← paragraph--*.html.twig
    field/, layout/, navigation/ ← Other template types
```

---

## Build Commands

All from `public_html/themes/custom/surface/`:

```bash
npm run vite:build       # Production build → dist/
npm run vite:watch       # Watch mode
npm run storybook:dev    # Storybook at localhost:6006
npm run build            # Full: lint + format + stylelint + vite:build
npm run watch            # vite:watch + storybook:dev
npm run lint:fix         # Biome JS/TS fix
npm run format:write     # Biome format fix
npm run stylelint:fix    # CSS lint fix
```

After any PHP or template change: `drush cr`

---

## Twig Namespaces

Defined in `surface.info.yml`. Always use namespaced includes:

```twig
@elements/resource-item/resource-item.twig
@components/person-card/person-card.twig
@collections/resource-list/resource-list.twig
@layouts/site-header/site-header.twig
```

Never use relative paths (`./`, `../`).

---

## Design Tokens

Brand colors in `props/brand.css`. Never hardcode hex values.

| Token | Use |
|---|---|
| `--forest` | Dark backgrounds, headers |
| `--maple` | Primary CTA, links, accents |
| `--maple-dark` | CTA hover |
| `--parchment` | Warm section backgrounds |
| `--mist` | Borders, subtle backgrounds |
| `--ink` | Body text |
| `--mid` | Secondary/muted text |
| `--light` | Page background |
| `--bark` | Warm brown accent |
| `--gold` | Highlight color |

Fonts: `var(--font-primary)` (Source Serif 4), `var(--font-mono)` (DM Mono).

Section themes set via class: `section-theme--dark`, `section-theme--light`, `section-theme--warm`, `section-theme--bold`.

---

## Field Naming Convention

- **`schema_` prefix** — fields mapping directly to Schema.org properties: `schema_name`, `schema_url`, `schema_description`, `schema_date_published`, `schema_publisher`, `schema_type`
- **`field_` prefix** — structural Drupal fields: `field_sections`, `field_resource_items`, `field_steps`

---

## Key Data Files

Located at `data/` (served at `/themes/custom/surface/data/`):

- **`vt-districts-by-town.json`** — Vermont House + Senate districts per town (2022 Act 89 reapportionment). Structure: `{ "TownName": { "houseDistricts": [], "senateDistricts": [] } }`. Multi-district towns store arrays.
- **`vt-legislators.json`** — Current Vermont legislators: name, district, email, chamber.

Copy both to `.storybook/public/data/` for Storybook access.

---

## Legislator Contact Tool

The most complex component (`source/patterns/collections/legislator-contact/`).

- Town lookup debounced 300ms on `input` event
- Ambiguous towns (Barre, Rutland, Newport, St. Albans, Essex) prompt City vs Town clarification
- Multi-district towns show House picker (`#lc-district-picker`) — populated once, selection preserved
- Multi-senate towns show Senate picker (`#lc-senate-district-picker`) — Burlington, Colchester, Essex only
- Salutation pre-fills as user types: `Dear Representative Smith & Senator Jones,`
- Name suffix handling: Jr, Sr, II, III, IV stripped before last name extraction
- Submit snapshots picker hidden state — only blocks if pickers *just* appeared
- Google Civic API fallback fires only when JSON fetch fails, not on district matching failure

GA4 events: `lc_form_start`, `lc_form_submit_attempt` (role), `lc_form_submit` (rep_count, house_district, senate_district), `lc_email_opened`, `lc_message_copied`, `lc_confirmation_shown`

---

## Common Mistakes to Avoid

1. **Never edit `dist/`** — always edit source, run `vite:build`
2. **Never use relative Twig paths** — always `@namespace/`
3. **Never hardcode hex colors** — always `var(--token)`
4. **`display: flex` overrides `[hidden]`** — add `&[hidden] { display: none }` to any flex container that toggles visibility
5. **`field_sections` ≠ `schema_main_entity`** — homepage paragraphs go in `schema_main_entity`; other sections in `field_sections`
6. **Always `only`** on Twig includes from paragraph templates — prevents Drupal variable bleed
7. **`drush cr` after** adding a library, changing PHP, or adding a template suggestion
8. **`|striptags` for plain text, `|raw` for trusted HTML** — never `|raw` on user input or arbitrary field values
9. **Theme suggestions append to end** — `$suggestions[] = 'node__page__front'` — never splice or reverse
