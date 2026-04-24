import nodeArticleBody from './node-article-body.twig';
import nodeArticleBodyData from './node-article-body.yml';

const settings = {
  title: 'Components/Node Article Body',
};

export const Default = {
  render: (args) => nodeArticleBody(args),
  args: { ...nodeArticleBodyData },
};

export const NoTopic = {
  render: (args) => nodeArticleBody(args),
  args: {
    ...nodeArticleBodyData,
    topic: null,
  },
};

export const NoLead = {
  render: (args) => nodeArticleBody(args),
  args: {
    ...nodeArticleBodyData,
    lead: null,
  },
};

export const BodyOnly = {
  render: (args) => nodeArticleBody(args),
  args: {
    ...nodeArticleBodyData,
    topic: null,
    lead: null,
  },
};

export default settings;
