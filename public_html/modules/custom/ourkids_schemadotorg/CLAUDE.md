# CLAUDE.md — ourkids_schemadotorg

Custom Schema.org JSON-LD alterations for OurKidsOurSchoolsVT. Hooks into SchemaBlueprints' JSON-LD pipeline to fix output that SchemaBlueprints cannot handle via config alone.

---

## Architecture

```
ourkids_schemadotorg/
  ourkids_schemadotorg.info.yml
  ourkids_schemadotorg.module    ← two hooks wired to JsonLd classes
  composer.json                  ← PSR-4 autoload declaration
  src/JsonLd/
    PersonJsonLd.php             ← Person node alterations
    ArticleJsonLd.php            ← Article node alterations
```

---

## Hooks

### `hook_schemadotorg_jsonld_alter()`
Fires once per page request on the **outer wrapper** data array (e.g. `ProfilePage`). Used when you need to alter the relationship between wrapper and `mainEntity` — moving or removing properties that SchemaBlueprints places at the wrong level.

Current use: Person nodes — removes `jobTitle` from `ProfilePage` wrapper, injects it onto `mainEntity.Person`.

### `hook_schemadotorg_jsonld_schema_type_entity_alter()`
Fires **per entity** during the JSON-LD build. `$data` is the entity's own property array. Used for fixing or augmenting individual entity output.

Current use: dispatches to `PersonJsonLd::alter()` and `ArticleJsonLd::alter()` by bundle.

---

## PersonJsonLd

**Problem solved:** SchemaBlueprints maps `body` → `description` but outputs the full rendered HTML including `<p>` tags. JSON-LD `description` must be plain text.

**Fix:** `preg_replace` converts `</p><p>` sequences to `\n\n` before `strip_tags()` — preserves paragraph spacing as whitespace.

**Also handles:** `sameAs` from `schema_same_as` multi-value link field. Single value outputs a string; multiple values output an array. This is the key E-E-A-T signal for Google — populate `schema_same_as` with VTDigger author page, LinkedIn, etc.

---

## ArticleJsonLd

**Problem solved:** Same `description` HTML leakage as Person.

**Also adds:** `articleBody` as plain text from `body.processed` — Google uses this for content indexing on news and opinion pieces.

---

## Adding a New Bundle

1. Create `src/JsonLd/{BundleName}JsonLd.php` following the existing pattern
2. Add a `use` statement in `ourkids_schemadotorg.module`
3. Add a match arm in `ourkids_schemadotorg_schemadotorg_jsonld_schema_type_entity_alter()`

```php
match ($entity->bundle()) {
  'person'  => PersonJsonLd::alter($data, $entity, $bubbleable_metadata),
  'article' => ArticleJsonLd::alter($data, $entity, $bubbleable_metadata),
  'event'   => EventJsonLd::alter($data, $entity, $bubbleable_metadata),  // new
  default   => NULL,
};
```

4. `drush cr`

---

## Important Notes

- The `hook_schemadotorg_jsonld_schema_type_entity_alter()` hook receives `$data` for the **entity being built**, not the outer page wrapper. For `ProfilePage` nodes, this hook fires for the `Person` entity (mainEntity), not the `ProfilePage` itself.
- To alter the outer `ProfilePage` data — including nested `mainEntity` — use `hook_schemadotorg_jsonld_alter()` instead, which receives the full assembled array.
- `jobTitle` placement: SchemaBlueprints puts it on `ProfilePage` because `schema_job_title` is mapped at the node level. The `hook_schemadotorg_jsonld_alter()` hook walks the data array, removes it from non-Person types, and injects it on `mainEntity.Person`.

---

## Dependencies

Declared in `ourkids_schemadotorg.info.yml`:
- `schemadotorg:schemadotorg`
- `schemadotorg:schemadotorg_jsonld`

Both must be enabled. `drush en ourkids_schemadotorg` will verify.
