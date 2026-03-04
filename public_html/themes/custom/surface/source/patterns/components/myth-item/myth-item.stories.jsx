import mythItem from './myth-item.twig';

const settings = {
  title: 'Components/Myth Item',
};

export const Cost = {
  render: (args) => mythItem(args),
  args: {
    myth: 'Independent schools are draining the Education Fund.',
    fact: 'Publicly funded independent school students make up only <strong>4% of the student population</strong> and just <strong>3% of Education Fund spending</strong>. Districts with tuitioning spend <strong>$184 less per pupil</strong> than the state average.',
  },
};

export const Oversight = {
  render: (args) => mythItem(args),
  args: {
    myth: 'Independent schools have no oversight or accountability.',
    fact: 'Independent schools are governed by <strong>Rule Series 2200</strong> — nearly twice the length of public school standards — and must be re-approved by the Agency of Education <strong>every five years</strong>.',
  },
};

export const Equity = {
  render: (args) => mythItem(args),
  args: {
    myth: "Independent schools aren't equitable and don't serve all learners.",
    fact: 'Under <strong>Act 173</strong>, independent schools receiving public tuition dollars are required to accommodate students with special education needs. Discrimination based on any protected class is illegal under state and federal law.',
  },
};

export const Savings = {
  render: (args) => mythItem(args),
  args: {
    myth: 'Restructuring districts will save money and improve outcomes.',
    fact: 'Districts with tuitioning already spend <strong>less per pupil than the state average</strong>. The issue is not district structure — it is whether families retain the right to choose.',
  },
};

export default settings;
