import cardItem from './card-item.twig';

const settings = {
  title: 'Components/Card Item',
};

export const Default = {
  render: (args) => cardItem(args),
  args: {
    title: 'Eliminating the tuitioning mechanism',
    body: 'The proposal would eliminate the tuitioning mechanism in non-operating towns.',
    variant: '',
  },
};

export const Urgent = {
  render: (args) => cardItem(args),
  args: {
    title: 'Impacting 5,000+ students',
    body: 'This would directly impact over 5,000 students statewide.',
    variant: 'urgent',
  },
};

export const Stake = {
  render: (args) => cardItem(args),
  args: {
    title: 'VT-TV-21: State-of-the-Art Media Lab',
    body: 'Vermont’s first student-led newsroom. A place where students gain hands-on experience in journalism, broadcasting, and digital media.',
    variant: 'stake',
  },
};

export default settings;
