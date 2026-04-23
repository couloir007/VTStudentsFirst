# COMPONENTS.md — Component Reference

All components live in `source/patterns/`. Each has `.twig`, `.css`, `.yml`, and `.stories.jsx`.

## Elements (Atoms)

| Component | Path | Notes |
|---|---|---|
| `button` | elements/button | Primary/ghost/link variants |
| `date-badge` | elements/date-badge | Month + day display for events |
| `eyebrow` | elements/eyebrow | Small label above headlines |
| `resource-item` | elements/resource-item | External link card with type/publisher/date meta |
| `section-headline` | elements/section-headline | Section h2 with optional em |
| `section-label` | elements/section-label | Small caps label; modifiers: light/dark/warm/forest |
| `stat-item` | elements/stat-item | Large number + label (used in hero stats) |
| `title` | elements/title | Heading with configurable level |

---

## Components (Molecules)

| Component | Path | Notes |
|---|---|---|
| `legislator-contact` | components/legislator-contact | Full contact form — see CLAUDE.md for details |
| `mobile-button` | components/mobile-button | Hamburger/close toggle |
| `node-article` | components/node-article | Article card |
| `node-homepage` | components/node-homepage | Iterates schema_main_entity paragraphs |
| `person-card` | components/person-card | Horizontal avatar + name/role/quote layout |
| `testimonial` | components/testimonial | Quote block |
| `event-item` | components/event-item | Month/day badge + event details |

---

## Collections (Page Sections)

All collections render inside a `<section>` with `section-theme--*` class.

| Component | Path | Section theme | Notes |
|---|---|---|---|
| `hero-section` | collections/hero-section | dark | Mountain SVG bg, stats row |
| `issue-section` | collections/issue-section | light | Lead text + issue cards |
| `stakes-section` | collections/stakes-section | dark | 4-column stakes grid |
| `positions-section` | collections/positions-section | warm | Numbered position items |
| `accountability-section` | collections/accountability-section | light | 3-pillar grid |
| `myth-section` | collections/myth-section | dark | Myth/fact pairs |
| `voices-section` | collections/voices-section | warm | Testimonial grid + CTA |
| `action-section` | collections/action-section | bold | Steps + tabbed toolkit + legislator contact |
| `news-section` | collections/news-section | light | Article listing |
| `events-section` | collections/events-section | light | Upcoming events |
| `resource-list` | collections/resource-list | light | External link list using resource-item |

---

## Layouts (Page Shells)

| Component | Path | Notes |
|---|---|---|
| `page-layout` | layouts/page-layout | Full campaign page shell |
| `news-page` | layouts/news-page | News & Updates page shell |
| `share-your-story` | layouts/share-your-story | Webform page |
| `site-header` | layouts/site-header | Logo + nav + mobile button |
| `site-footer` | layouts/site-footer | Brand + nav columns + bottom bar |

---

## Component Data Contracts

### `resource-item`
```yaml
title:     string  # required — link text
url:       string  # required — external URL
summary:   string  # plain text (|striptags applied in template)
type:      string  # opinion | news | legislation | government | research
publisher: string  # e.g. VTDigger, Vermont Legislature
date:      string  # YYYY-MM-DD
```

### `resource-list`
```yaml
section_label:  string  # optional
headline:       string  # optional, supports |raw HTML
resource_items: array   # array of resource-item objects (see above)
```

### `person-card`
```yaml
name:             string
role:             string
location:         string  # town, VT
quote:            string
school_connection: string
image:            object  # media entity / img src
```

### `stat-item`
```yaml
number:   string  # e.g. '4%', '$184'
label:    string  # descriptor
modifier: string  # optional CSS modifier class
```

### `section-label`
```yaml
text:     string
modifier: string  # light | dark | warm | forest
```

### `event-item`
```yaml
month:  string  # e.g. 'Mar'
day:    string  # e.g. '25'
title:  string
meta:   string  # date/time/location line
body:   string  # description
signup:
  label: string
  url:   string
```

---

## Adding a New Component

1. Create directory: `source/patterns/{type}/{name}/`
2. Create four files: `{name}.twig`, `{name}.css`, `{name}.yml`, `{name}.stories.jsx`
3. Add `{{ attach_library('surface/{name}') }}` at top of `.twig` (elements only)
4. Add library entry to `surface.libraries.yml`
5. If it's a paragraph type, create `templates/paragraphs/paragraph--{bundle}.html.twig`
6. Run `npm run vite:build` and `drush cr`

### Library entry pattern (`surface.libraries.yml`):
```yaml
resource-item:
  css:
    component:
      dist/css/resource-item.css: {}

resource-list:
  css:
    component:
      dist/css/resource-list.css: {}
  dependencies:
    - surface/section
    - surface/resource-item
```

### Storybook story pattern:
```jsx
import componentName from './component-name.twig';
import componentData from './component-name.yml';

const settings = { title: 'Elements/Component Name' };

export const Default = {
  render: (args) => componentName(args),
  args: { ...componentData },
};

export default settings;
```

---

## CSS Conventions

- Use CSS nesting (`postcss-nested`) for BEM modifiers and states
- Use `&[hidden] { display: none }` on any flex/grid container that uses the `hidden` attribute
- Media queries use custom properties from `props/media.css`
- BEM naming: `.block__element--modifier`
- Never hardcode colors — always `var(--token-name)` from `props/brand.css`

```css
/* Correct */
.resource-item {
  border-block-end: 1px solid var(--mist);

  &:first-child {
    border-block-start: 1px solid var(--mist);
  }

  &[hidden] {
    display: none;
  }
}

/* Wrong */
.resource-item {
  border-bottom: 1px solid #c8ddd0;
}
```

---

## Section Theme CSS Variables

Each section theme sets these variables for child components to consume:

```css
.section-theme--dark {
  --section-bg:     var(--forest);
  --section-body:   var(--mist);
  --section-border: var(--pine);
}

.section-theme--light {
  --section-bg:     var(--light);
  --section-body:   var(--mid);
  --section-border: var(--mist);
}

.section-theme--warm {
  --section-bg:     var(--parchment);
  --section-body:   var(--mid);
  --section-border: var(--mist);
}
```

Child components use these: `background-color: var(--section-bg, var(--light))` with fallback.
