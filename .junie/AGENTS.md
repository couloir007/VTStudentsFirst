# Project Development Guide: VTStudentsFirst (Surface Theme)

This guide provides project-specific details for advanced developers working on the `surface` Drupal theme.

## 1. Build and Configuration Instructions

The `surface` theme uses a decoupled approach, utilizing **Vite** for asset bundling and **Storybook** for component development.

### Theme Setup
The theme is located at `public_html/themes/custom/surface/`.

1. **Install Dependencies**:
   ```bash
   cd public_html/themes/custom/surface/
   npm install
   ```

2. **Build Assets**:
   - Production Build: `npm run vite:build`
   - Development/Watch: `npm run vite:watch`

3. **Storybook Development**:
   - Start Storybook: `npm run storybook:dev`
   - Build Storybook: `npm run storybook:build`

### Drupal Configuration
The project uses **Composer** for backend dependency management and **Drush** for site operations.
- Root directory contains `composer.json`.
- Drupal web root is `public_html/`.

## 2. Testing Information

### Component Testing (Storybook)
Visual and interaction testing should be performed within Storybook.
- Run `npm run storybook:test` to execute configured Storybook tests.

### Logic/Template Testing (Twig)
Since Storybook uses `twig.js`, you can verify Twig template logic using a Node.js script.

#### Verified Example: Verifying a Twig Template
Create a script (e.g., `test_twig.js`) in the `surface/` theme directory:

```javascript
import twig from 'twig';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const templatePath = path.resolve(__dirname, 'source/patterns/elements/title/title.twig');
const templateSource = fs.readFileSync(templatePath, 'utf8');

const render = (data) => {
  const template = twig.twig({ data: templateSource });
  return template.render(data).trim();
};

const data = { title: { level: 1, text: 'Hello World' } };
const output = render(data);
console.log('Output:', output);
if (output.includes('<h1>') && output.includes('Hello World')) {
  console.log('PASSED');
} else {
  console.log('FAILED');
  process.exit(1);
}
```
Run it with: `node test_twig.js`

## 3. Additional Development Information

### Code Style and Linting
The project enforces code style using **Biome** for JavaScript/TypeScript and **Stylelint** for CSS.

- **Check Linting/Formatting**: `npm run lint:check` and `npm run format:check`
- **Fix Issues Automatically**: `npm run lint:fix` and `npm run format:write`
- **CSS Linting**: `npm run stylelint:check`

### CSS Architecture
- The project uses **PostCSS** with `postcss-nested` and `postcss-preset-env`.
- Global variables (props) are defined in `props/` directory at the theme root.
- Media queries use custom media properties defined in `props/media.css`.

### Component Structure
Patterns are organized by type in `source/patterns/`:
- `elements/`: Basic atoms (titles, buttons).
- `components/`: More complex molecules.
- `collections/`: Sections and groupings.
- `layouts/`: Page-level templates.

Each pattern typically consists of:
- `.twig`: Markup.
- `.css`: Styles.
- `.yml`: Default data.
- `.stories.jsx`: Storybook configuration.
- `.mdx`: Documentation.
