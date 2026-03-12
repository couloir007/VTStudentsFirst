import newsPage from './news-page.twig';
import headerData from '../site-header/site-header.yml';
import footerData from '../site-footer/site-footer.yml';

import { Default as NewsData } from '../../collections/news-section/news-section.stories.jsx';

const settings = {
  title: 'Layouts/News Page',
  parameters: { layout: 'fullscreen' },
};

export const NewsUpdatesPage = {
  render: (args) => newsPage(args),
  args: {
    header_data: headerData,
    footer_data: footerData,
    page_title: 'News & Updates',
    page_intro: 'The decisions being made this session will shape education in the Northeast Kingdom for decades. Stay informed.',
    news_data: NewsData.args,
  },
};

export default settings;
