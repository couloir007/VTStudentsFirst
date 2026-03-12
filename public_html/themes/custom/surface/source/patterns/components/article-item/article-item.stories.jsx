import articleItem from './article-item.twig';

const settings = {
  title: 'Components/Article Item',
};

export const Default = {
  render: (args) => articleItem(args),
  args: {
    title: 'What is Vermont\'s Town Tuition Program — and why is it at risk?',
    description: 'Vermont\'s Town Tuition Program is one of the oldest school choice systems in the country. Here\'s how it works, who it serves, and why a 2025 law has put it at risk for NEK families.',
    date: 'March 12, 2026',
    date_iso: '2026-03-12',
    topic: 'Town Tuition',
    url: '/news/what-is-vermonts-town-tuition-program',
  },
};

export const NoDescription = {
  render: (args) => articleItem(args),
  args: {
    title: 'Act 73 Explained: What It Means for NEK Families',
    date: 'March 12, 2026',
    date_iso: '2026-03-12',
    topic: 'Act 73',
    url: '/news/act-73-vermont-explained-nek-families',
  },
};

export const NoTopic = {
  render: (args) => articleItem(args),
  args: {
    title: 'St. Johnsbury Academy and Lyndon Institute: Why They Matter to the Whole NEK',
    description: 'St. Johnsbury Academy and Lyndon Institute aren\'t elite private schools. They\'re the educational infrastructure of Vermont\'s Northeast Kingdom.',
    date: 'March 12, 2026',
    date_iso: '2026-03-12',
    url: '/news/st-johnsbury-academy-lyndon-institute-nek',
  },
};

export default settings;
