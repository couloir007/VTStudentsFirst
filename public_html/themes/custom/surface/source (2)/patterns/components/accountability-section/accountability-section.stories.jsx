import accountabilitySection from './accountability-section.twig';

const settings = {
  title: 'Components/Accountability Section',
};

export const Default = {
  render: (args) => accountabilitySection(args),
  args: {
    section_label: 'Our Accountability Framework',
    section_headline: 'We support guardrails.<br><em>Real ones.</em>',
    body_text: '<p>Protecting tuitioning doesn\'t mean writing a blank check. We believe any school receiving Vermont public tuition dollars should meet clear, enforceable standards. This is our position — not a concession.</p>',
    accountability_cards: [
      {
        headline: 'Transparent Financial Reporting',
        body: 'Schools receiving public tuition dollars must disclose how those funds are used, with annual public reporting to the State.',
        icon: '📊',
      },
      {
        headline: 'Student Support Obligations',
        body: 'Receiving schools must provide or fully fund special education services for tuitioning students — no exceptions, no cost-shifting to sending towns.',
        icon: '♿',
      },
      {
        headline: 'Nondiscrimination Requirements',
        body: 'All tuition-receiving schools must comply with state and federal nondiscrimination law as a condition of continued eligibility.',
        icon: '⚖️',
      },
      {
        headline: 'Outcomes Reporting',
        body: 'Graduation rates, post-secondary outcomes, and student performance data must be reported publicly and annually for all tuitioning students.',
        icon: '📈',
      },
    ],
  },
};

export default settings;
