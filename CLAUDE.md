# CLAUDE.md (VTStudentsFirst)

Project management and development guidelines for the VTStudentsFirst project (Surface theme).

## Build and Install

### Backend (Drupal)
- `composer install` - Install PHP dependencies.
- `drush status` - Check Drupal status.
- `drush cr` - Clear Drupal cache.

### Frontend (Surface Theme)
The theme is located at `public_html/themes/custom/surface/`.
- `cd public_html/themes/custom/surface && npm install` - Install frontend dependencies.
- `npm run vite:build` - Production asset build (run from theme directory).
- `npm run vite:watch` - Development/Watch assets (run from theme directory).
- `npm run storybook:dev` - Start Storybook development server.
- `npm run build` - Full build (lint + format + stylelint + vite:build).

## Lint and Format

All commands below should be run from `public_html/themes/custom/surface/`.

- `npm run lint:check` - Check JS/TS linting using Biome.
- `npm run lint:fix` - Fix JS/TS linting issues.
- `npm run format:check` - Check code formatting.
- `npm run format:write` - Fix code formatting.
- `npm run stylelint:check` - Check CSS linting.
- `npm run stylelint:fix` - Fix CSS linting issues.

## Test

- `npm run storybook:test` - Run Storybook interaction tests.
- `node test_twig.js` - Verify Twig template logic (requires a `test_twig.js` script in the theme directory).

## Code Style and Guiding Principles

- **Component-Driven Development**: All UI components are developed in Storybook located in `source/patterns/`.
- **Component Structure**:
    - `elements/`: Basic atoms (titles, buttons).
    - `components/`: Complex molecules.
    - `collections/`: Sections and groupings.
    - `layouts/`: Page-level templates.
- **CSS Architecture**:
    - Uses PostCSS with `postcss-nested` and `postcss-preset-env`.
    - Global variables are in the `props/` directory at the project root.
    - Media queries use custom media properties from `props/media.css`.
- **Twig Templates**: Storybook uses `twig.js`. Drupal uses native Twig. Ensure compatibility between both.
- **Linting**: Biome is used for JS/TS/JSON, and Stylelint for CSS. Always lint and format before committing.
