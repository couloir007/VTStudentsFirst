# Module Development Guide: ourkids_outreach

This module provides an administrative tool for sending tracked outreach emails to Vermont legislators.

## 1. Overview
The module allows site administrators to send personalized emails to legislators in the Vermont House and Senate. It tracks progress locally in the browser and sends emails via Drupal's mail system with HTML support.

- **Admin Path**: `/admin/ourkids/legislator-outreach`
- **Permissions**: Requires `administer site configuration`

## 2. Architecture

### Backend (PHP/Drupal)
- **Controller**: `src/Controller/LegislatorOutreachController.php`
    - `page()`: Renders the main admin interface with legislator lists.
    - `preview()`: AJAX endpoint returning JSON for email preview (Subject, Body, etc.).
    - `send()`: AJAX endpoint (POST) that triggers `mailManager->mail()`.
- **Module File**: `ourkids_outreach.module`
    - Implements `hook_theme()` for the admin page template.
    - Implements `hook_mail()` to handle the `legislator_outreach` mail key, ensuring HTML headers are set.
- **Routing**: `ourkids_outreach.routing.yml` defines paths for the page, preview, and send actions.

### Frontend (JS/CSS/Twig)
- **JavaScript**: `js/outreach.js`
    - Manages the interactive UI (tabs, modal).
    - Uses `localStorage` (key: `ourkids_outreach_sent`) to persist the "Sent" status across sessions.
    - Handles AJAX requests to the preview and send endpoints.
    - **Security**: Includes `X-CSRF-Token` in the POST request to the send endpoint.
- **CSS**: `css/outreach.css`
    - Specific styles for the admin table, tabs, and preview modal.
- **Template**: `templates/ourkids-outreach-page.html.twig`
    - Defines the structure of the admin dashboard and the preview modal.

## 3. Email Tracking Mechanism
Emails are generated with tracking links (if configured in the body content). The `ref` parameter is used to identify individual legislators.
The UI uses this `ref` (typically a slugified name/district combination) to track which emails have been sent during the current administrative session (saved in browser `localStorage`).

## 4. Key Workflows

### Previewing an Email
1. Admin clicks "Preview & Send".
2. JS fetches data from `/admin/ourkids/legislator-outreach/preview?ref=...`.
3. Modal displays the rendered HTML in an `iframe`.

### Sending an Email
1. Admin clicks "Send Email" in the modal.
2. JS sends a POST request to `/admin/ourkids/legislator-outreach/send`.
3. Controller validates the CSRF token and `ref`.
4. `Drupal::service('plugin.manager.mail')->mail()` is called.
5. On success, JS updates `localStorage` and the UI to mark the row as sent.
