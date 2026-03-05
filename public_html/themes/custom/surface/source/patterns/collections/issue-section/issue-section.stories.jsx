import issueSection from './issue-section.twig';

const settings = {
  title: 'Collections/Issue Section',
};

export const Default = {
  render: (args) => issueSection(args),
  args: {
    section_label: 'The Issue',
    section_headline: 'Tuitioning is how rural Vermont delivers <em>public education.</em>',
    lead_text: 'In Vermont, over 90 towns have no public high school of their own. Tuitioning — the right of families to choose an approved school and have tuition paid by their town — is how these communities provide public education. For generations, students in Danville, Sutton, Newark, and dozens of other rural towns have attended St. Johnsbury Academy, Lyndon Institute, Burr and Burton Academy, Thetford Academy, and other regional schools through this system.',
    body_text: '<p>Now, three legislative proposals threaten to dismantle this system — not by debating their merits, but by redesigning how districts are structured so that family choice disappears by default.</p><p>The Chair of the House Education Committee has explicitly stated that his goal is to eliminate school choice. Members of the committee have even referred to independent schools as <strong>"vendors"</strong> — a term that reveals a fundamental misunderstanding of how rural Vermont\'s education system actually works. We are taking them at their word — and organizing to stop it.</p><p>This is not about ideology. It is about equity. Tuitioning is Vermont\'s rural education delivery mechanism. Protecting it means protecting the same standard of access that every Vermont student deserves.</p>',
    issue_cards: [
      {
        title: 'Act 73 Phase 2 — Forced Consolidation',
        body: 'Would eliminate Supervisory Union structures and replace them with Supervisory Districts that assign students to schools through attendance zones. Family choice disappears. Only four historic academies — Burr and Burton, Thetford, St. Johnsbury, and Lyndon Institute — could be designated, and even they face forced enrollment reductions that would cut programs and staff.',
        variant: 'urgent',
      },
      {
        title: 'H.777 — Elimination of Tuitioning',
        body: 'Would eliminate public tuition funding for any independent school that does not follow public school rules (EQS). Because most independent schools operate under the rigorous Independent School Rules (Series 2200), this bill would effectively ban students from attending them with public tuition dollars.',
        variant: 'urgent',
      },
      {
        title: 'H.813 — Imposition of Public Rules',
        body: 'Would mandate that all independent schools follow public school regulations — removing the operational flexibility that makes these schools effective. This is a thinly-veiled attempt to homogenize Vermont\'s educational ecosystem and strip the independence that generations of families have valued.',
        variant: 'urgent',
      },
    ],
  },
};

export default settings;
