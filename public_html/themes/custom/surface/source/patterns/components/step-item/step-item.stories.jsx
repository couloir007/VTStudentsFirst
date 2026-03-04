import stepItem from './step-item.twig';

const settings = {
  title: 'Components/Step Item',
};

export const Legislator = {
  render: (args) => stepItem(args),
  args: {
    number: 1,
    headline: 'Contact Your Legislator',
    body: 'Send a personal message to your Vermont House and Senate members. Find yours at legislature.vermont.gov. Personal messages carry far more weight than form letters.',
    cta: { url: '#act', title: 'Write to My Legislators' },
  },
};

export const Board = {
  render: (args) => stepItem(args),
  args: {
    number: 2,
    headline: 'Engage Your School Board',
    body: 'Email your board or speak at the next public meeting. Ask them to go on record opposing designation rules that would eliminate family choice.',
    cta: { url: '#act', title: 'Get Board Template' },
  },
};

export const Spread = {
  render: (args) => stepItem(args),
  args: {
    number: 3,
    headline: 'Spread the Word',
    body: 'Share on Facebook, Front Porch Forum, or Instagram. Use the ready-made posts — or write your own in your own words.',
    cta: { url: '#act', title: 'Get Social Posts' },
  },
};

export const LTE = {
  render: (args) => stepItem(args),
  args: {
    number: 4,
    headline: 'Write to Your Paper',
    body: 'A letter to the editor in your local paper reaches legislators, school board members, and neighbors all at once.',
    cta: { url: '#act', title: 'Get LTE Templates' },
  },
};

export default settings;
