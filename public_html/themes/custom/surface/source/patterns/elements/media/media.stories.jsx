import media from './media.twig';
import data from './media.yml';

const settings = {
  title: 'Components/Media',
  parameters: {
    controls: {
      disable: true,
    },
  },
};

export const Media = {
  name: 'Square',
  render: (args) => media(args),
  args: {
    ...data,
    image: '<img src="/images/1-1.svg" alt="placeholder text" />',
  },
};

export const Portrait = {
  ...Media,
  name: '2:3',
  args: {
    ...data,
    image: '<img src="/images/2-3.svg" alt="placeholder text" />',
  },
};

export const Rectangular32 = {
  ...Media,
  name: '3:2',
  args: {
    ...data,
    image: '<img src="/images/3-2.svg" alt="placeholder text" />',
  },
};

export const Rectangular43 = {
  ...Media,
  name: '4:3',
  args: {
    ...data,
    image: '<img src="/images/4-3.svg" alt="placeholder text" />',
  },
};

export const Rectangular169 = {
  ...Media,
  name: '16:9',
  args: {
    ...data,
    image: '<img src="/images/16-9.svg" alt="placeholder text" />',
  },
};

export default settings;
