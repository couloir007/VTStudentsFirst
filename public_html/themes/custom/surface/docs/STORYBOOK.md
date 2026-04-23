# STORYBOOK.md — Storybook Component Development

Storybook runs at `http://localhost:6006` via `npm run storybook:dev` from `public_html/themes/custom/surface/`.

## Setup

- Framework: `@storybook/html-vite` (not React — stories render raw HTML from Twig)
- Twig rendering: `vite-plugin-twig-drupal` + `twig-drupal-filters` (Drupal filters available in twig.js)
- Stories: `source/patterns/**/*.stories.@(js|jsx|mjs|ts|tsx)`
- Static assets: `dist/` directory served as root (CSS, JS, images, fonts)
- YAML fixtures: `@modyfi/vite-plugin-yaml` — import `.yml` files directly in stories

---

## Story Structure

Every component story follows this pattern:

```jsx
import componentTemplate from './component-name.twig';
import componentData from './component-name.yml';

const settings = {
  title: 'Collections/Component Name',  // Sets sidebar position
};

export const Default = {
  render: (args) => componentTemplate(args),
  args: {
    ...componentData,             // Spread yml fixture as default args
  },
};

export const Variant = {
  render: (args) => componentTemplate(args),
  args: {
    ...componentData,
    some_prop: 'override value',  // Override specific args for variant
  },
};

export default settings;
```

---

## Story Sidebar Order

Defined in `.storybook/preview.js`:

```
Getting started → Base → Elements → Components → Collections → Layouts → Pages → Theme
```

Use `title` in the settings object to place stories:
- `'Elements/Resource Item'`
- `'Components/Person Card'`
- `'Collections/Resource List'`
- `'Layouts/Campaign Page'`

---

## Twig Namespaces in Storybook

The same `@namespace/` paths work in Storybook as in Drupal. Namespaces are configured in `vite.config.js` via `vite-plugin-twig-drupal` and mirror `surface.info.yml`:

```
@elements  → source/patterns/elements
@components → source/patterns/components
@collections → source/patterns/collections
@layouts   → source/patterns/layouts
```

---

## Data Files in Storybook

Static data files (`data/*.json`) are served from `.storybook/public/data/` in Storybook. The legislator contact tool fetches:
- `/data/vt-districts-by-town.json`
- `/data/vt-legislators.json`

Copy both files to `.storybook/public/data/` and keep in sync with `data/` when updated.

---

## Library Attachment in Storybook

`{{ attach_library('surface/library-name') }}` is ignored by twig.js — it's a Drupal-only call. CSS is loaded globally in Storybook via `source/patterns/styles.css` which imports all component CSS.

This means:
- CSS always renders in Storybook regardless of `attach_library`
- New CSS files must be imported in `styles.css` to appear in Storybook
- The `attach_library` call is still required in the `.twig` file for Drupal

---

## Drupal Behaviors in Storybook

`.storybook/decorators.jsx` calls `Drupal.attachBehaviors()` after each story renders via `useEffect`. This means JavaScript behaviors (like the legislator contact tool's `Drupal.behaviors.legislatorContact`) fire correctly in Storybook.

Drupal stubs are in `.storybook/drupal/drupal.js` and `.storybook/drupal/once.js`.

---

## Themes in Storybook

Two themes available via the toolbar (data attribute switching):
- `Surface` (default) — production brand colors
- `Other` — for comparison/testing

Set via `data-theme` attribute on the root element.

---

## yml Fixture Files

Every component should have a `.yml` fixture file with default data. This file is:
1. Imported in `.stories.jsx` as the default story args
2. Used as documentation of the component's data contract
3. Available for import in other stories that compose this component

Example:
```yaml
# resource-item.yml
---
title: "Waterford Parents: When 'School Choice' Simply Means Your Local High School"
url: 'https://vtdigger.org/2026/03/12/...'
summary: 'Eleven Waterford parents argue...'
type: 'opinion'
publisher: 'VTDigger'
date: '2026-03-12'
```

```jsx
import resourceItemData from './resource-item.yml';

export const Default = {
  render: (args) => resourceItem(args),
  args: { ...resourceItemData },
};
```

---

## Wrapping Element Components in Stories

Element components (like `resource-item`) render as `<li>` or other partial markup. Wrap them in the appropriate parent for correct display in Storybook:

```jsx
export const Default = {
  render: (args) => `<ul class="resource-list__items">${resourceItem(args)}</ul>`,
  args: { ...resourceItemData },
};
```

---

## Composing Stories from Other Stories

Import default args from child stories to build parent stories:

```jsx
import { Default as ResourceItemData } from '../resource-item/resource-item.stories.jsx';

export const Default = {
  render: (args) => resourceList(args),
  args: {
    section_label: 'In the Press',
    resource_items: [ResourceItemData.args, /* ... */],
  },
};
```

---

## Testing Twig Logic

For complex Twig logic (conditionals, loops, filters), create a test script in the theme root:

```javascript
// test_twig.js
import twig from 'twig';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const src = fs.readFileSync(
  path.resolve(__dirname, 'source/patterns/elements/resource-item/resource-item.twig'),
  'utf8'
);

const render = (data) => twig.twig({ data: src }).render(data).trim();

const out = render({
  title: 'Test Title',
  url: 'https://example.com',
  summary: '<p>Raw HTML summary</p>',
  type: 'opinion',
  publisher: 'VTDigger',
  date: '2026-03-12',
});

console.log(out.includes('Test Title') ? 'PASSED' : 'FAILED');
```

Run with: `node test_twig.js`

---

## Accessibility Addon

`@storybook/addon-a11y` runs axe accessibility checks on every story. Check the Accessibility tab in Storybook to catch ARIA and contrast issues before deploying.

The tab widget in `action-section` follows the ARIA Authoring Practices tab pattern:
- `role="tablist"` on container
- `role="tab"` + `aria-controls` + `aria-selected` + `tabindex` on each button
- `role="tabpanel"` + `aria-labelledby` + `hidden` on each panel
- Arrow key navigation via `action-section.js`
