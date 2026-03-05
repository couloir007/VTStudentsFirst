import sectionLabel from './section-label.twig';

const settings = {
  title: 'Elements/Section Label',
};

export const Default = {
  render: (args) => sectionLabel(args),
  args: {
    text: "The Issue",
  },
};

export const Light = {
  render: (args) => sectionLabel(args),
  args: {
    text: "Take Action",
    modifier: "light",
  },
};

export const Sage = {
  render: (args) => sectionLabel(args),
  args: {
    text: "Upcoming Events",
    modifier: "sage",
  },
};

export default settings;
