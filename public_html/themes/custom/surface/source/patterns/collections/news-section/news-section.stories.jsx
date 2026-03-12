import newsSection from './news-section.twig';

const settings = {
  title: 'Collections/News Section',
};

const sampleArticles = [
  {
    title: 'What is Vermont\'s Town Tuition Program — and why is it at risk?',
    description: 'Vermont\'s Town Tuition Program is one of the oldest school choice systems in the country. Here\'s how it works, who it serves, and why a 2025 law has put it at risk for NEK families.',
    date: 'March 12, 2026',
    date_iso: '2026-03-12',
    topic: 'Town Tuition',
    url: '/news/what-is-vermonts-town-tuition-program',
  },
  {
    title: 'Act 73 Explained: What It Means for NEK Families',
    description: 'Act 73 is Vermont\'s sweeping 2025 education reform law. Here\'s what it actually does, what it changes for Northeast Kingdom families, and what\'s still being fought over in 2026.',
    date: 'March 12, 2026',
    date_iso: '2026-03-12',
    topic: 'Act 73',
    url: '/news/act-73-vermont-explained-nek-families',
  },
  {
    title: 'St. Johnsbury Academy and Lyndon Institute: Why They Matter to the Whole NEK',
    description: 'St. Johnsbury Academy and Lyndon Institute aren\'t elite private schools. They\'re the educational infrastructure of Vermont\'s Northeast Kingdom.',
    date: 'March 12, 2026',
    date_iso: '2026-03-12',
    topic: 'Independent Schools',
    url: '/news/st-johnsbury-academy-lyndon-institute-nek',
  },
  {
    title: 'H.777 and H.813: The Next Threat to Vermont Independent Schools',
    description: 'Two bills moving through the Vermont Legislature in 2026 would add new restrictions and requirements on independent schools receiving public tuition.',
    date: 'March 12, 2026',
    date_iso: '2026-03-12',
    topic: 'Legislation',
    url: '/news/h777-h813-vermont-independent-schools',
  },
  {
    title: 'Vermont School Redistricting: What the New Maps Mean for Your Town',
    description: 'Vermont\'s legislature must vote on new school district maps in 2026 under Act 73. Here\'s where the redistricting debate stands and what\'s at stake for NEK communities.',
    date: 'March 12, 2026',
    date_iso: '2026-03-12',
    topic: 'Redistricting',
    url: '/news/vermont-school-redistricting-new-maps',
  },
  {
    title: 'What Happens to Family Choice Under Vermont\'s New School Districts?',
    description: 'Vermont\'s redistricting proposals would fundamentally reshape — or eliminate — school choice in communities that currently have it.',
    date: 'March 12, 2026',
    date_iso: '2026-03-12',
    topic: 'School Choice',
    url: '/news/vermont-school-choice-new-districts',
  },
];

export const Default = {
  render: (args) => newsSection(args),
  args: {
    section_label: 'News & Updates',
    section_headline: 'What\'s happening in Montpelier',
    body_text: 'The decisions being made this session will shape education in the Northeast Kingdom for decades. Stay informed.',
    articles: sampleArticles,
    cta: {
      url: '/news',
      title: 'View all articles',
    },
  },
};

export const ThreeUp = {
  render: (args) => newsSection(args),
  args: {
    section_label: 'Latest',
    section_headline: 'Recent updates',
    articles: sampleArticles.slice(0, 3),
  },
};

export const NoHeader = {
  render: (args) => newsSection(args),
  args: {
    articles: sampleArticles.slice(0, 3),
  },
};

export default settings;
