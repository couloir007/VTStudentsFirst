import positionItem from './position-item.twig';

const settings = {
  title: 'Components/Position Item',
};

export const Default = {
  render: (args) => positionItem(args),
  args: {
    number: 1,
    headline: 'Preserve family-level tuitioning access',
    body: 'Non-operating towns must retain the right to provide students with genuine school choice — not board-only designation that eliminates options. Tuitioning is not a loophole. It is Vermont\'s rural education policy.',
  },
};

export const Accountability = {
  render: (args) => positionItem(args),
  args: {
    number: 3,
    headline: 'Require accountability from all receiving schools',
    body: 'We support enforceable guardrails: transparency in spending, student support obligations, nondiscrimination requirements, and regular outcomes reporting for any school receiving public tuition dollars.',
  },
};

export default settings;
