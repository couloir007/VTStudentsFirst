# VTStudentsFirst

VTStudentsFirst is a Drupal-based web application featuring the custom **Surface** theme and various custom modules, including the **OurKids Legislator Outreach** tool.

## Project Structure

- `public_html/`: Drupal web root.
  - `themes/custom/surface/`: The main frontend theme (Vite + Storybook).
  - `modules/custom/ourkids_outreach/`: Custom module for legislator outreach.
- `composer.json`: PHP dependency management.
- `package.json`: Frontend dependency management (root).
- `.junie/`: Project guidelines and agent documentation.
- `CLAUDE.md`: Quick reference for commands and style guides.

## Getting Started

### Prerequisites

- PHP 8.1+
- Composer
- Node.js & npm
- MariaDB/MySQL or PostgreSQL (as configured in your local environment)

### Backend Setup (Drupal)

1. Install PHP dependencies:
   ```bash
   composer install
   ```

2. Initialize your `settings.php` and database connection (standard Drupal setup).

3. Clear cache and check status:
   ```bash
   drush cr
   drush status
   ```

### Frontend Setup (Surface Theme)

The theme uses a modern, decoupled workflow with Vite for bundling and Storybook for component-driven development.

1. Navigate to the theme directory:
   ```bash
   cd public_html/themes/custom/surface/
   ```

2. Install dependencies:
   ```bash
   npm install
   ```

3. Build assets for production:
   ```bash
   npm run vite:build
   ```

4. Run development server (with watch mode and Storybook):
   ```bash
   npm run watch
   ```

## Development and Testing

### Commands

Run these within the `public_html/themes/custom/surface/` directory:

- `npm run lint:check`: Run Biome linting.
- `npm run format:check`: Check code formatting.
- `npm run stylelint:check`: Check CSS linting.
- `npm run storybook:dev`: Start Storybook server on port 6006.
- `npm run storybook:test`: Run Storybook interaction tests.

### Component-Driven Development

All UI components are developed in isolation using Storybook. They are located in `source/patterns/`, categorized into:
- `elements/`: Atoms (buttons, titles).
- `components/`: Molecules.
- `collections/`: Sections.
- `layouts/`: Page-level templates.

## Custom Modules

### OurKids Legislator Outreach
Located at `public_html/modules/custom/ourkids_outreach/`.
This module provides an administrative tool for sending tracked outreach emails to Vermont legislators.
- Admin Path: `/admin/ourkids/legislator-outreach`

## Guidelines

For detailed development guidelines, refer to:
- `CLAUDE.md`: General command and style reference.
- `.junie/AGENTS.md`: Detailed theme development documentation.
- `public_html/modules/custom/ourkids_outreach/.junie/AGENTS.md`: Outreach module documentation.
