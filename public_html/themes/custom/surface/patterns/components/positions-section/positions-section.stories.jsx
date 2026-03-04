import positionsSection from './positions-section.twig';

const settings = {
  title: 'Components/Positions Section',
};

export const Default = {
  render: (args) => positionsSection(args),
  args: {
    section_label: 'Our Position',
    section_headline: 'Local flexibility.<br><em>Statewide accountability.</em>',
    body_text: 'We are not asking for special treatment. We are asking Vermont to protect the mechanism it already uses to deliver public education in rural communities — while holding all schools receiving public dollars to clear, enforceable standards.',
    positions: [
      {
        headline: 'Preserve family-level tuitioning access',
        body: 'Non-operating towns must retain the right to provide students with genuine school choice — not board-only designation that eliminates options. Tuitioning is not a loophole. It is Vermont\'s rural education policy.',
      },
      {
        headline: 'Protect rural equity — not special treatment',
        body: 'Tuitioning is how Vermont ensures equity for students in towns without a high school. Defending it is defending the same standard of access that every Vermont student deserves.',
      },
      {
        headline: 'Require accountability from all receiving schools',
        body: 'We support enforceable guardrails: transparency in spending, student support obligations, nondiscrimination requirements, and regular outcomes reporting for any school receiving public tuition dollars.',
      },
      {
        headline: 'Prevent destabilizing disruption',
        body: 'Any governance changes must include safeguards against sudden reassignments, loss of established programs, or changes that undermine workforce pipelines and community economic stability.',
      },
      {
        headline: 'Governance cannot become a backdoor to end tuitioning',
        body: 'If restructuring proceeds, statute must explicitly prevent governance changes from being used to eliminate family choice in non-operating towns. The protection must be durable.',
      },
    ],
  },
};

export default settings;
