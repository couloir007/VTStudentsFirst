import nodePerson from './node-person.twig';
import nodePersonData from './node-person.yml';

const settings = {
  title: 'Components/Person Profile',
};

export const Default = {
  render: (args) => nodePerson(args),
  args: {
    ...nodePersonData,
  },
};

export const NoImage = {
  render: (args) => nodePerson(args),
  args: {
    ...nodePersonData,
    image: null,
  },
};

export const NoArticles = {
  render: (args) => nodePerson(args),
  args: {
    ...nodePersonData,
    articles: [],
  },
};

export const MultipleArticles = {
  render: (args) => nodePerson(args),
  args: {
    ...nodePersonData,
    articles: [
      {
        title:    'Our Independent Schools Are More Than Just Educational Institutions — They Are Social and Economic Cornerstones',
        url:      'https://vtdigger.org/2025/07/07/sean-montague-our-independent-schools-are-more-than-just-educational-institutions-they-are-social-and-economic-cornerstones/',
        date:     'July 7, 2025',
        date_iso: '2025-07-07',
        topic:    'Opinion',
      },
      {
        title:    'When Equal Is Not Fair',
        url:      'https://vtdigger.org/2026/03/22/opinion-carmenza-montague-when-equal-is-not-fair/',
        date:     'March 22, 2026',
        date_iso: '2026-03-22',
        topic:    'Opinion',
      },
    ],
  },
};

export default settings;
