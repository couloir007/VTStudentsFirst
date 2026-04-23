# CLAUDE.md — OurKidsOurSchoolsVT

## Project Overview

**OurKidsOurSchoolsVT** (ourkidsourschoolsvt.org) is a Drupal 10 advocacy campaign site for a Vermont coalition preserving school choice and tuitioning access in rural towns that depend on independent schools — St. Johnsbury Academy, Lyndon Institute, Burr and Burton Academy, Thetford Academy, and Riverside School.

**Stack:** Drupal 10 · Composer · Drush · Lando (local) · Pantheon (hosting)  
**Frontend:** Custom `surface` theme · Vite · Storybook · PostCSS · Biome · Stylelint

**Deeper documentation:**
- `public_html/themes/custom/surface/TEMPLATES.md` — Drupal template system
- `public_html/themes/custom/surface/STORYBOOK.md` — Storybook component development
- `public_html/themes/custom/surface/COMPONENTS.md` — Component reference and conventions
- `DRUPAL.md` — Content types, paragraph types, modules, config sync

---

## Directory Layout

```
/
  CLAUDE.md                              ← this file
  DRUPAL.md                              ← Drupal config reference
  composer.json
  public_html/
    themes/custom/surface/               ← all frontend work
      source/patterns/                   ← component source (twig, css, yml, stories)
        elements/                        ← atoms
        components/                      ← molecules
        collections/                     ← page sections
        layouts/                         ← page shells
      props/                             ← CSS custom properties
      dist/                              ← Vite build output (DO NOT edit)
      data/                              ← JSON data files
      includes/                          ← PHP hook files
      .storybook/                        ← Storybook config
      surface.info.yml                   ← theme info + Twig namespaces
      surface.libraries.yml              ← CSS/JS library definitions
      surface.theme                      ← main PHP preprocess hooks
      TEMPLATES.md
      STORYBOOK.md
      COMPONENTS.md
    modules/custom/ourkids_outreach/     ← custom legislator outreach module
    templates/                           ← Drupal Twig templates
      content/                           ← node--*.html.twig
      paragraphs/                        ← paragraph--*.html.twig
      field/, layout/, navigation/, webform/
```

---

## Backend Commands

```bash
composer install              # Install PHP dependencies
drush status                  # Check Drupal status
drush cr                      # Clear all caches — run after any config/template/PHP change
drush cim                     # Import config from sync/
drush cex                     # Export config to sync/
```

---

## Frontend Commands

All from `public_html/themes/custom/surface/`:

```bash
npm install                   # Install dependencies
npm run vite:build            # Production build → dist/
npm run vite:watch            # Watch mode
npm run storybook:dev         # Storybook at localhost:6006
npm run storybook:build       # Build static Storybook
npm run build                 # Full: lint + format + stylelint + vite:build
npm run watch                 # vite:watch + storybook:dev concurrently
npm run lint:fix              # Fix JS/TS (Biome)
npm run format:write          # Fix formatting (Biome)
npm run stylelint:fix         # Fix CSS (Stylelint)
```

---

## Architecture Principles

- **Component-driven:** All UI built in Storybook first (`source/patterns/`), then bridged to Drupal via paragraph templates
- **Templates bridge Drupal → Storybook:** `templates/paragraphs/paragraph--*.html.twig` maps Drupal field objects to component variables, then includes the Storybook component with `only`
- **Twig namespaces:** Always use `@elements/`, `@components/`, `@collections/`, `@layouts/` — never relative paths
- **Field naming:** `schema_` prefix for Schema.org-mapped fields; `field_` prefix for structural Drupal fields
- **CSS tokens:** Never hardcode colors — always `var(--token)` from `props/brand.css`
- **`dist/` is build output:** Never edit directly — always edit source and run `vite:build`

---

## CSS Architecture

- PostCSS with `postcss-nested` (CSS nesting), `postcss-preset-env`, `postcss-import`
- Global tokens in `props/` — brand colors, fonts, media queries, sizes, animations
- Biome for JS/TS, Stylelint for CSS
- Always run `npm run build` before committing

---

## Critical Rules

1. `display: flex` overrides `[hidden]` — add `&[hidden] { display: none }` to any flex container that toggles visibility
2. Always use `only` on Twig component includes from paragraph templates
3. `drush cr` after any PHP change, new library, or new template suggestion
4. `|striptags` for plain text fields, `|raw` only for trusted HTML
5. Theme suggestions: always `$suggestions[] = 'suggestion'` — never splice or reverse
6. `field_sections` ≠ `schema_main_entity` — homepage paragraphs go in `schema_main_entity`; other page sections go in `field_sections`
