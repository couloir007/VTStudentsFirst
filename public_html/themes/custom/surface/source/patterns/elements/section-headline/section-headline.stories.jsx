import template from './section-headline.twig';
import './section-headline.css';

export default {
  title: 'Elements/Section Headline',
};

export const Default = {
  render: (args) => template(args),
  args: {
    text: 'Our <em>Position</em>',
  },
};

export const Light = {
  render: (args) => template(args),
  args: {
    text: 'The <em>Stakes</em> for Vermont Families',
    modifier: 'light',
  },
  parameters: {
    backgrounds: { default: 'dark' },
  },
};

export const White = {
  render: (args) => template(args),
  args: {
    text: 'Take <em>Action</em> Today',
    modifier: 'white',
  },
  parameters: {
    backgrounds: { default: 'dark' },
  },
};
