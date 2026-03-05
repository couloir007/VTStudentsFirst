import pageLayout from './page-layout.twig';
import headerData from '../site-header/site-header.yml';
import footerData from '../site-footer/site-footer.yml';

import { Default as HeroData } from '../../collections/hero-section/hero-section.stories.jsx';
import { Default as IssueData } from '../../collections/issue-section/issue-section.stories.jsx';
import { Default as StakesData } from '../../collections/stakes-section/stakes-section.stories.jsx';
import { Default as PositionsData } from '../../collections/positions-section/positions-section.stories.jsx';
import { Default as AccountabilityData } from '../../collections/accountability-section/accountability-section.stories.jsx';
import { Default as MythData } from '../../collections/myth-section/myth-section.stories.jsx';
import { Default as VoicesData } from '../../collections/voices-section/voices-section.stories.jsx';
import { Default as ActionData } from '../../collections/action-section/action-section.stories.jsx';

const settings = {
  title: 'Layouts/Campaign Page',
  parameters: { layout: 'fullscreen' },
};

export const CampaignPage = {
  render: (args) => pageLayout(args),
  args: {
    header_data: headerData,
    footer_data: footerData,
    hero_data: HeroData.args,
    issue_data: IssueData.args,
    stakes_data: StakesData.args,
    positions_data: PositionsData.args,
    accountability_data: AccountabilityData.args,
    myth_data: MythData.args,
    voices_data: VoicesData.args,
    action_data: ActionData.args,
  },
};

export default settings;
