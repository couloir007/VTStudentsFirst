import accountabilitySection from './accountability-section.twig';

const settings = {
  title: 'Components/Accountability Section',
};

export const Default = {
  render: (args) => accountabilitySection(args),
  args: {
    section_label: 'Accountability',
    section_headline: 'Choice works because it is <em>accountable.</em>',
    body_text: '<p>Opponents of tuitioning claim independent schools are unregulated. The fact is, they are already held to high standards and transparency. We support common-sense measures that ensure public dollars are being used for their intended purpose — educating our children.</p>',
    accountability_cards: [
      {
        headline: 'Transparent Financial Reporting',
        body: 'Schools receiving public tuition dollars must disclose how those funds are used, with annual public reporting to the State.',
        icon: '📊',
      },
      {
        headline: 'Uniform Anti-Discrimination',
        body: 'All schools receiving public funds must adhere to strict anti-discrimination policies to ensure equitable access.',
        icon: '⚖️',
      },
      {
        headline: 'Independent Audit Requirements',
        body: 'Independent schools undergo rigorous financial audits to ensure fiscal responsibility and transparency.',
        icon: '📑',
      },
      {
        headline: 'Standards & Accountability',
        body: 'Independent schools are already required to meet state educational quality standards to be eligible for tuition.',
        icon: '🏛️',
      },
    ],
  },
};

export default settings;
