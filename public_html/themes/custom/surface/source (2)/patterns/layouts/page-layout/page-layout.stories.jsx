import pageLayout from './page-layout.twig';
import headerData from '../site-header/site-header.yml';
import footerData from '../site-footer/site-footer.yml';

// Import data from existing stories
import { Default as HeroData } from '../../components/hero-section/hero-section.stories.jsx';
import { Default as IssueData } from '../../components/issue-section/issue-section.stories.jsx';
import { Default as StakesData } from '../../components/stakes-section/stakes-section.stories.jsx';
import { Default as MythData } from '../../components/myth-section/myth-section.stories.jsx';
import { Default as PositionsData } from '../../components/positions-section/positions-section.stories.jsx';
import { Default as VoicesData } from '../../components/voices-section/voices-section.stories.jsx';
import { Default as ActionData } from '../../components/action-section/action-section.stories.jsx';
import { Default as AccountabilityData } from '../../components/accountability-section/accountability-section.stories.jsx';

const settings = {
  title: 'Layouts/Campaign Page',
  parameters: {
    layout: 'fullscreen',
  },
};

export const CampaignPage = {
  render: (args) => pageLayout(args),
  args: {
    header_data: headerData,
    footer_data: footerData,
    hero_data: HeroData.args,
    issue_data: IssueData.args,
    stakes_data: StakesData.args,
    myth_data: MythData.args,
    positions_data: PositionsData.args,
    voices_data: VoicesData.args,
    action_data: ActionData.args,
    accountability_data: AccountabilityData.args,
  },
};

export default settings;
