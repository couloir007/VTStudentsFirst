import heroSection from './hero-section.twig';

const settings = {
  title: 'Components/Hero Section',
};

export const Default = {
  render: (args) => heroSection(args),
  args: {
    eyebrow: 'Vermont Public Education',
    headline: 'Every Vermont student<br>deserves a <em>real</em> choice.',
    subheadline: 'Thousands of Vermont students in rural towns attend their local academy through tuitioning — the mechanism Vermont uses to deliver public education where no public high school exists. Proposed legislation threatens to eliminate that choice. We\'re fighting to keep it.',
    cta_primary: {
      url: '#act',
      title: 'Contact Your Legislator',
    },
    cta_secondary: {
      url: '#issue',
      title: 'Learn What\'s at Stake',
    },
    stats: [
      { number: '4%', label: 'Of students — independent schools' },
      { number: '3%', label: 'Of Education Fund spending' },
      { number: '$184', label: 'Less per pupil than state average' },
      { number: '$0', label: 'State construction aid received' },
    ],
  },
};

export default settings;
