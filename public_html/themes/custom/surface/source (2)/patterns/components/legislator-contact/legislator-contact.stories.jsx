import legislatorContact from './legislator-contact.twig';

const settings = {
  title: 'Components/Legislator Contact',
};

/**
 * Three-step contact flow:
 *   1. Enter address → Google Civic API looks up reps
 *   2. Compose a pre-populated letter, edit, and send
 *   3. Confirmation with next steps
 *
 * In Storybook (no API key), step 1 resolves with mock VT legislators
 * so the full flow can be previewed without a live API key.
 *
 * In production, set the API key via:
 *   - data-civic-api-key attribute (passed from paragraph--action-section.html.twig)
 *   - window.CIVIC_API_KEY global
 */
export const Default = {
  render: (args) => legislatorContact(args),
  args: {
    civic_api_key: '',
  },
};

/**
 * Standalone on a dark background — matches the action-section context.
 */
export const InActionSection = {
  render: (args) => `
    <div style="background: var(--forest); padding: 4rem 2.5rem; max-width: 900px; margin: 0 auto;">
      ${legislatorContact(args)}
    </div>
  `,
  args: {
    civic_api_key: '',
  },
};

export default settings;
