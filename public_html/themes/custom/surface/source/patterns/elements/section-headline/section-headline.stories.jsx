import React from 'react';
import template from './section-headline.twig';
import './section-headline.css';

export default {
  title: 'Elements/Section Headline',
  parameters: {
    layout: 'centered',
  },
  argTypes: {
    text: { control: 'text' },
    modifier: {
      control: { type: 'select' },
      options: [null, 'light', 'white'],
    },
    class: { control: 'text' },
  },
};

const Template = (args) => {
  return <div dangerouslySetInnerHTML={{ __html: template(args) }} />;
};

export const Default = Template.bind({});
Default.args = {
  text: 'Our <em>Position</em>',
};

export const Light = Template.bind({});
Light.args = {
  text: 'The <em>Stakes</em> for Vermont Families',
  modifier: 'light',
};
Light.parameters = {
  backgrounds: { default: 'dark' },
};

export const White = Template.bind({});
White.args = {
  text: 'Take <em>Action</em> Today',
  modifier: 'white',
};
White.parameters = {
  backgrounds: { default: 'dark' },
};
