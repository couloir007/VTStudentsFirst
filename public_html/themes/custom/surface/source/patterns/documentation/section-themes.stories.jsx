/**
 * Section Theme Modifiers
 *
 * Add a modifier class to any section element to get a coordinated
 * background + foreground color system via CSS custom properties.
 *
 * Available modifiers:
 *   .section-theme--dark   forest bg  → cream/mist/sage/gold
 *   .section-theme--light  light bg   → forest/mid/meadow/maple
 *   .section-theme--warm   parchment  → forest/mid/meadow/maple
 *   .section-theme--bold   maple-darker bg → white/cream/mist/gold
 *
 * All section components consume these tokens:
 *   --section-bg, --section-headline, --section-body,
 *   --section-label, --section-accent, --section-border,
 *   --section-card-bg, --section-card-border,
 *   --section-btn-bg, --section-btn-color, --section-btn-hover
 */

const settings = {
  title: 'Base/Section Themes',
};

const demo = (modifier, label) => `
  <section class="section-theme--${modifier}" style="padding: 3rem 4rem; margin-bottom: 2px;">
    <p style="
      font-family: var(--font-mono);
      font-size: 0.68rem;
      letter-spacing: 0.15em;
      text-transform: uppercase;
      color: var(--section-label);
      margin-bottom: 0.75rem;
    ">.section-theme--${modifier} — ${label}</p>

    <h2 style="
      font-family: var(--font-secondary);
      font-size: 2rem;
      font-weight: 900;
      color: var(--section-headline);
      margin-bottom: 0.5rem;
      line-height: 1.1;
    ">Section Headline — <em style="color: var(--section-accent); font-style: italic;">accented</em></h2>

    <p style="
      color: var(--section-body);
      font-size: 1rem;
      line-height: 1.65;
      max-width: 600px;
      margin-bottom: 1.5rem;
    ">Body text reads clearly against this background. Card borders, labels, and accents
    all resolve automatically from the modifier class.</p>

    <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.5rem;">
      <div style="
        background: var(--section-card-bg);
        border: 1px solid var(--section-card-border);
        border-radius: 3px;
        padding: 1rem 1.5rem;
        color: var(--section-body);
        font-size: 0.9rem;
      ">Card / panel background</div>

      <a href="#" style="
        background: var(--section-btn-bg);
        color: var(--section-btn-color);
        padding: 0.75rem 1.5rem;
        border-radius: 3px;
        font-family: var(--font-mono);
        font-size: 0.72rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        text-decoration: none;
        display: inline-block;
      ">CTA Button</a>

      <a href="#" style="
        color: var(--section-link);
        font-size: 0.9rem;
        text-decoration: underline;
      ">Inline link</a>
    </div>

    <hr style="border-color: var(--section-border); border-top-width: 1px; margin: 0;" />
  </section>
`;

export const AllThemes = {
  render: () => `
    <div>
      ${demo('dark',  'forest background')}
      ${demo('light', 'light background')}
      ${demo('warm',  'parchment background')}
      ${demo('bold',  'maple-darker background')}
    </div>
  `,
};

export const Dark  = { render: () => demo('dark',  'forest background') };
export const Light = { render: () => demo('light', 'light background') };
export const Warm  = { render: () => demo('warm',  'parchment background') };
export const Bold  = { render: () => demo('bold',  'maple-darker background') };

export default settings;
