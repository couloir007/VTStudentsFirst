import scrollTopTwig from './scroll-top.twig';

export default {
  title: 'Components/Scroll Top',
  render: (args) => scrollTopTwig(args),
};

export const Default = {
  args: {
    label: 'Back to top',
  },
};
