import actionSection from './action-section.twig';
import '../legislator-contact/legislator-contact.twig';

const settings = {
  title: 'Collections/Action Section',
};

export const Default = {
  render: (args) => actionSection(args),
  args: {
    section_label: 'Take Action',
    section_headline: 'Four ways to make<br>an <em>impact today.</em>',
    body_text: 'The most powerful advocacy happens when real constituents make direct, personal contact. As one NEK advocate put it: <em>"We need more people to get involved, raise concerns, and share personal stories."</em>',
    steps: [
      {
        headline: 'Contact Your Legislator',
        body: 'Fill in your info and we\'ll connect you with your Vermont House and Senate representatives. Personal messages carry far more weight than form letters.',
        cta: { url: '#tab-legislator', title: 'Legislator Letter' },
      },
      {
        headline: 'Engage Your School Board',
        body: 'Email your board or speak at the next public meeting. Ask them to go on record opposing designation and attendance zones.',
        cta: { url: '#tab-board', title: 'Get Board Template' },
      },
      {
        headline: 'Spread the Word',
        body: 'Share on Facebook, Front Porch Forum, or Instagram. Use the ready-made posts below — or write your own.',
        cta: { url: '#tab-social', title: 'Get Social Posts' },
      },
      {
        headline: 'Write to Your Paper',
        body: 'A letter to the editor in your local paper reaches legislators, school board members, and neighbors all at once.',
        cta: { url: '#tab-lte', title: 'Letter to Editor' },
      },
    ],
    toolkit: {
      headline: 'Advocacy Toolkit',
      subheadline: 'Ready-made templates for parents, employers, board meetings, and the press. Click any tab to copy.',
      tabs: [
        {
          id: 'legislator',
          title: 'Legislator Letter',
          items: [
            {
              id: 'leg-template',
              title: 'Sample Legislator Letter',
              content: 'My name is [Your Name] and I am a constituent from [Town]. I am writing to share my strong concern about proposals that would eliminate Vermont\'s Town Tuition Program and restrict family access to independent schools. I understand the need for education reform, but any restructuring that ends school choice for families in non-operating towns will reduce educational opportunity without saving money. Please ensure that any legislation explicitly protects tuitioning access and family choice for students in towns without a public high school.',
            },
          ],
        },
        {
          id: 'board',
          title: 'School Board Email',
          items: [
            {
              id: 'board-template',
              title: 'Board Email / Public Comment',
              content: 'I am asking the school board to publicly oppose legislative proposals that would eliminate or restrict school choice, or force our independent schools to conform to one-size-fits-all regulations. Proposals being considered — including Act 73 Phase 2, H.777, and H.813 — would strip our community of its ability to tuition students to the schools that best fit their needs. Will this board commit to drafting a public statement in unambiguous support of maintaining our system of school choice?',
            },
          ],
        },
        {
          id: 'social',
          title: 'Social Media',
          items: [
            {
              id: 'social-1',
              title: 'Post 1 — General',
              content: 'Rural Vermont doesn\'t deserve to have its education system targeted. Proposals in the House Education Committee would cut out the independent schools that have served us for generations. Contact your legislators today. #OurKidsOurSchoolsVT #VTEd',
            },
            {
              id: 'social-2',
              title: 'Post 2 — Data',
              content: 'Independent school students make up 4% of Vermont\'s student population and just 3% of Education Fund spending. Districts with school choice spend LESS per pupil than the state average. These schools aren\'t the problem. #OurKidsOurSchoolsVT',
            },
            {
              id: 'social-3',
              title: 'Post 3 — Family',
              content: 'My child is not a seat to be filled. Proposals in the House Education Committee seek to make public schools the "default," allowing tuition to independent schools only as a rare exception. Let\'s keep our choice. #OurKidsOurSchoolsVT',
            },
            {
              id: 'social-4',
              title: 'Post 4 — Academies',
              content: 'St. Johnsbury Academy, Lyndon Institute, Burr and Burton, Thetford Academy — these are not exceptions. They are the system. Let\'s keep our communities vibrant. #OurKidsOurSchoolsVT',
            },
          ],
        },
        {
          id: 'lte',
          title: 'Letter to Editor',
          items: [
            {
              id: 'lte-a',
              title: 'Template A — General',
              content: 'To the Editor,\n\nI am writing as a parent in [Town Name]. Like many families, we value the tight-knit community here and the ability to choose the best education for our kids through the Town Tuition Program.\n\nRecent proposals in the Legislature would replace family tuitioning with board-controlled "designation," making independent schools an option only by exception.\n\nThis is deeply concerning. My child thrives at [School Name] because of its [specific program]. I urge our representatives to protect the partnerships that have served our towns for generations.\n\nSincerely,\n[Your Name], [Your Town]',
            },
            {
              id: 'lte-b',
              title: 'Template B — Choose Your Angle',
              content: 'To the Editor,\n\nI am writing as a parent in [Town Name]. Recent proposals — specifically Act 73 Phase 2, H.777, and H.813 — threaten to dismantle Vermont\'s Town Tuition Program by replacing family choice with board-controlled designation.\n\n[THE COMMUTE: Policymakers seem to think a long bus ride out of town is better for our children than attending the local independent school that has served us well for so long.]\n\n[THE ECONOMY: Our local independent school is an economic and cultural anchor. If the state forces enrollment to drop, we risk losing the programs, jobs, and vitality that school brings to our region.]\n\nVermont\'s education funding problem is real, but we cannot solve it by punishing rural students.\n\nSincerely,\n[Your Name], [Your Town]',
            },
          ],
        },
      ],
    },
  },
};

export default settings;
