# TEMPLATES.md — Drupal Template System

Templates live in `public_html/themes/custom/surface/templates/`. They are Drupal Twig templates — separate from the Storybook component source in `source/patterns/`.

## Template vs Component

| | Template (`templates/`) | Component (`source/patterns/`) |
|---|---|---|
| Purpose | Bridge Drupal fields → component | Reusable markup + styles |
| Data source | Drupal field objects | Static yml fixtures / story args |
| Rendered by | Drupal | Storybook (twig.js) + Drupal |
| Naming convention | `node--page.html.twig` | `resource-list.twig` |

---

## Directory Structure

```
templates/
  content/
    node--page.html.twig               ← All Basic Pages (non-homepage)
    node--page--front.html.twig        ← Homepage only (theme suggestion hook)
    node--about-page.html.twig
    node--event.html.twig              ← Event teaser
    node--event--full.html.twig        ← Event full display
    node--news-updates--full.html.twig
    node--person--profile-card.html.twig
    node--article.html.twig
    node--teaser.html.twig
  paragraphs/
    paragraph--hero-section.html.twig
    paragraph--issue-section.html.twig
    paragraph--stakes-section.html.twig
    paragraph--positions-section.html.twig
    paragraph--accountability-section.html.twig
    paragraph--myth-section.html.twig
    paragraph--voices-section.html.twig
    paragraph--action-section.html.twig
    paragraph--resource-list.html.twig
    paragraph--events-section.html.twig
    paragraph--event-item.html.twig
  field/
    field--node--field-image.html.twig
    field--node--field-tags.html.twig
    field--node--schema-has-part--news-updates.html.twig
    field--node--title.html.twig
  layout/
    page.html.twig
    region--header.html.twig
    region--footer.html.twig
    region--content.html.twig
  navigation/
    menu--main.html.twig
    menu--footer.html.twig
  webform/
    webform--testimonial-submission.html.twig
```

---

## Node Template Pattern

### Homepage — `node--page--front.html.twig`
Added by `surface_theme_suggestions_node_alter()` via `$suggestions[] = 'node__page__front'`. Wraps the `node-homepage.twig` component:

```twig
{% include '@components/node-homepage/node-homepage.twig' with {
  'content': content.schema_main_entity,
  'attributes': attributes,
  'label': label,
  'page': page,
  'url': url,
} %}
```

### Generic Page — `node--page.html.twig`
Iterates over both paragraph fields directly. No component wrapper needed:

```twig
<article {{ attributes }}>
  {{ title_prefix }}{{ title_suffix }}
  <div class="node__content">
    <div class="node__inner">

      {# schema_main_entity — primary Layout field #}
      {% for idx, item in content.schema_main_entity %}
        {% if item['#paragraph'] is defined %}{{ item }}{% endif %}
      {% endfor %}

      {# field_sections — supplementary sections #}
      {% for idx, item in content.field_sections %}
        {% if item['#paragraph'] is defined %}{{ item }}{% endif %}
      {% endfor %}

    </div>
  </div>
</article>
```

**Key distinction:** `schema_main_entity` is labeled "Layout" in the Drupal node edit UI. Homepage paragraphs (hero, issue, stakes, etc.) go here. `field_sections` is for supplementary sections like Resource Lists on inner pages.

---

## Paragraph Template Pattern

```twig
{{ attach_library('surface/resource-list') }}

{% set resource_items = [] %}
{% for item in paragraph.field_resource_items %}
  {% set entity = item.entity %}
  {% set resource_items = resource_items|merge([{
    title:     entity.schema_name.value,
    url:       entity.schema_url.uri,
    summary:   entity.schema_description.value,
    type:      entity.schema_type.value,
    publisher: entity.schema_publisher.value,
    date:      entity.schema_date_published.value,
  }]) %}
{% endfor %}

{% include "@collections/resource-list/resource-list.twig" with {
  section_label:  'In the Press',
  headline:       'This is what public education looks like in the <em>Northeast Kingdom.</em>',
  resource_items: resource_items,
  attributes:     attributes,
} only %}
```

Always use `only` on component includes to prevent Drupal variable bleed.

---

## Field Access Reference

```twig
paragraph.field_name.value           ← plain text
paragraph.field_name.uri             ← link URL
paragraph.field_name.title           ← link text
paragraph.field_name.processed       ← filtered HTML (WYSIWYG — always use .processed)
paragraph.field_name.entity          ← referenced entity (single)
paragraph.field_name.entity.field_x.value  ← field on referenced entity

{% for item in paragraph.field_name %}  ← iterate multiple values
  {{ item.entity.field_x.value }}
{% endfor %}

content.field_name                   ← rendered field in node template
content.field_name|field_value       ← rendered field without wrapper markup
```

---

## Manage Display Requirements

For `content.field_name` to be available in a node template, the field must be in the **enabled** section (not Disabled) in **Structure → Content Types → [Type] → Manage Display**.

Common debugging steps when a field isn't rendering:
1. Check Manage Display — is it enabled?
2. Check the paragraph type's own Manage Display — are child fields visible?
3. Verify the template iterates or prints the field
4. `drush cr`

---

## Theme Suggestion Hook

In `includes/html.theme`:

```php
function surface_theme_suggestions_node_alter(array &$suggestions, array $variables) {
  $node = $variables['elements']['#node'];
  if ($node->bundle() === 'page' && \Drupal::service('path.matcher')->isFrontPage()) {
    $suggestions[] = 'node__page__front';
  }
}
```

**Rule:** Always append to `$suggestions[]`. Drupal picks the last matching template — appending makes the new suggestion most specific. Never splice or reverse the array.

---

## Schema.org Field Convention

Fields mapped to Schema.org properties use `schema_` prefix:

| Drupal field | Schema.org |
|---|---|
| `schema_name` | `name` / `headline` |
| `schema_url` | `url` |
| `schema_description` | `description` |
| `schema_date_published` | `datePublished` |
| `schema_publisher` | `publisher.name` |
| `schema_type` | drives `@type` |
| `schema_main_entity` | `mainEntity` on WebPage |
| `schema_has_part` | `hasPart` |

`resource_item` paragraph `@type` is conditional on `schema_type` value:
`opinion` → `OpinionNewsArticle`, `news` → `NewsArticle`, `legislation` → `Legislation`, `government` → `GovernmentService`, `research` → `Report`
