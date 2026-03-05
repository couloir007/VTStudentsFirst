import mythSection from './myth-section.twig';

const settings = {
  title: 'Components/Myth Section',
};

export const Default = {
  render: (args) => mythSection(args),
  args: {
    section_label: 'Setting the Record Straight',
    section_headline: 'Myth vs. <em>Fact.</em>',
    body_text: 'These talking points address the misinformation circulating in Montpelier and at school board meetings. Use them when speaking with neighbors, legislators, or the press.',
    myths: [
      {
        myth: 'Independent schools are draining the Education Fund.',
        fact: 'Publicly funded independent school students make up only <strong>4% of the student population</strong> and just <strong>3% of Education Fund spending</strong>. Districts that tuition students to independent schools consistently spend <strong>$184 less per pupil</strong> ($13,763) than the state average ($13,947). Independent schools receive <strong>no state construction aid</strong>, saving taxpayers millions while public schools face over $6 billion in deferred maintenance.',
      },
      {
        myth: 'Independent schools have no oversight or accountability.',
        fact: 'Independent schools are governed by <strong>Rule Series 2200</strong> — nearly twice the length of public school standards. Unlike public schools, independent schools must undergo formal review and re-approval by the Agency of Education <strong>every five years</strong>. They are subject to the same background checks, mandated reporter laws, and statewide assessment requirements as public schools.',
      },
      {
        myth: 'Independent schools aren\'t equitable and don\'t serve all students.',
        fact: 'Under <strong>Act 173</strong>, independent schools that receive public tuition dollars are required to accommodate students with special education needs. Discrimination based on any protected class is illegal under state and federal law. Many independent schools provide specialized social and emotional environments where neurodiverse students thrive — offering flexibility that large public systems often cannot match.',
      },
      {
        myth: 'Consolidation will save money and improve outcomes.',
        fact: 'Districts with tuitioning and school choice already spend <strong>less per pupil than the state average</strong> — not more. Forced consolidation into large Supervisory Districts would eliminate the cost-effective delivery model that rural Vermont has built over generations, while imposing new administrative costs and forcing long commutes on students in towns that never had a local public school to begin with.',
      },
    ],
  },
};

export default settings;
