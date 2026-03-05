import statItem from './stat-item.twig';

const settings = {
  title: 'Elements/Stat Item',
};

export const StudentsShare = {
  render: (args) => statItem(args),
  args: {
    number: '4%',
    label: 'Of students — independent schools',
  },
};

export const FundShare = {
  render: (args) => statItem(args),
  args: {
    number: '3%',
    label: 'Of Education Fund spending',
  },
};

export const PerPupilDelta = {
  render: (args) => statItem(args),
  args: {
    number: '$184',
    label: 'Less per pupil than state average',
  },
};

export const ConstructionAid = {
  render: (args) => statItem(args),
  args: {
    number: '$0',
    label: 'State construction aid received',
  },
};

export default settings;
