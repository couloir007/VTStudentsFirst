import mythSection from './myth-section.twig';

const settings = {
  title: 'Components/Myth Section',
};

export const Default = {
  render: (args) => mythSection(args),
  args: {
    section_label: 'Setting the Record Straight',
    section_headline: 'Myth vs. <em>Fact.</em>',
    myths: [
      {
        myth: 'Independent schools are draining the Education Fund.',
        fact: 'Publicly funded independent school students make up only <strong>4% of the student population</strong> and just <strong>3% of Education Fund spending</strong>. Districts with tuitioning spend <strong>$184 less per pupil</strong> ($13,763 vs. $13,947 statewide). Independent schools receive <strong>no state construction aid</strong>, while public schools carry over $6 billion in deferred maintenance.',
      },
      {
        myth: 'Independent schools have no oversight or accountability.',
        fact: 'Independent schools are governed by <strong>Rule Series 2200</strong> — nearly twice the length of public school standards — and must be formally re-approved by the Agency of Education <strong>every five years</strong>. They are subject to the same background checks, mandated reporter laws, and statewide assessment requirements as public schools.',
      },
      {
        myth: "Independent schools aren't equitable and don't serve all learners.",
        fact: 'Under <strong>Act 173</strong>, independent schools receiving public tuition dollars are required to accommodate students with special education needs. Discrimination based on any protected class is illegal under state and federal law. Many provide specialized environments where neurodiverse students thrive.',
      },
      {
        myth: 'Restructuring districts will save money and improve outcomes.',
        fact: 'Districts with tuitioning and school choice already spend <strong>less per pupil than the state average</strong>. Any change that eliminates family-level choice would discard a cost-effective delivery model built over generations. The issue is not district structure — it is whether families retain the right to choose.',
      },
    ],
  },
};

export default settings;
