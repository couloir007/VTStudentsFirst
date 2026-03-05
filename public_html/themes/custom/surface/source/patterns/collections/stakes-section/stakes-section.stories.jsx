import stakesSection from './stakes-section.twig';

const settings = {
  title: 'Collections/Stakes Section',
};

export const Default = {
  render: (args) => stakesSection(args),
  args: {
    section_label: "What's at Stake",
    section_headline: 'This affects more than<br>one <em>school</em> or one <em>town.</em>',
    stakes_items: [
      {
        icon: '🎓',
        headline: 'Program Access',
        body: 'CTE pathways, AP courses, arts programs, dual enrollment, and world languages exist at regional academies that smaller consolidated schools could not replicate. Reassignment ends access overnight.',
      },
      {
        icon: '🚌',
        headline: 'Rural Commutes',
        body: 'For families in the Northeast Kingdom and other rural regions, alternative schools can mean 45–90 minute one-way commutes — a barrier that disproportionately harms working families.',
      },
      {
        icon: '🏗️',
        headline: 'Workforce Pipelines',
        body: 'Regional academies partner with local employers, hospitals, and trades. Disrupting enrollment disrupts workforce development pipelines that rural Vermont\'s economy depends on.',
      },
      {
        icon: '👨‍👩‍👧',
        headline: 'Family Stability',
        body: 'Families move to rural towns because they know their children can access these schools. Removing choice devalues property, destabilizes communities, and drives young families out of Vermont.',
      },
    ],
  },
};

export default settings;
