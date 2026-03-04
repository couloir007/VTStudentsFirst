import stakesSection from './stakes-section.twig';

const settings = {
  title: 'Components/Stakes Section',
};

export const Default = {
  render: (args) => stakesSection(args),
  args: {
    section_label: 'The Stakes',
    section_headline: 'What happens if rural school choice <em>disappears?</em>',
    body_text: 'Dismantling the tuitioning system wouldn\'t just limit choices — it would create an educational and economic crisis for rural Vermont.',
    stakes_items: [
      {
        headline: 'Financial Stability',
        body: 'Many rural towns avoid the massive capital costs of building and maintaining high schools through tuitioning. Forced consolidation would spike local property taxes.',
      },
      {
        headline: 'Educational Variety',
        body: 'Students currently access specialized programs in arts, tech, and sciences that single districts can\'t provide. That variety would be lost in a one-size-fits-all model.',
      },
      {
        headline: 'Community Identity',
        body: 'Historic academies are the heart of their regions. Stripping them of public tuition funding threatens their very existence and the vitality of the towns that surround them.',
      },
      {
        headline: 'Rural Access',
        body: 'For families in remote areas, choice isn\'t a luxury — it\'s the only way to ensure their children don\'t face two-hour daily commutes to distant assigned schools.',
      },
    ],
  },
};

export default settings;
