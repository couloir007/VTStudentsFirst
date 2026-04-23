import resourceList from './resource-list.twig';
import resourceListData from './resource-list.yml';

const settings = {
  title: 'Collections/Resource List',
};

export const Default = {
  render: (args) => resourceList(args),
  args: {
    ...resourceListData,
  },
};

export const WithLegislation = {
  render: (args) => resourceList(args),
  args: {
    section_label: 'Resources',
    resource_items: [
      {
        title: 'H.777 — An Act Relating to Education Governance',
        url: 'https://legislature.vermont.gov/bill/status/2026/H.777',
        summary: 'Pending legislation that would allow school boards to designate a single receiving school, replacing family-level tuitioning access with board-controlled assignment.',
        type: 'legislation',
        publisher: 'Vermont Legislature',
        date: '2026-01-15',
      },
      {
        title: 'Act 73 — Vermont Education Reform',
        url: 'https://legislature.vermont.gov/Documents/2026/Docs/ACTS/ACT073/ACT073%20As%20Enacted.pdf',
        summary: "The 2025 act that reduced eligible tuitioning schools from 46 to 18 and created the Redistricting Task Force to study district consolidation.",
        type: 'legislation',
        publisher: 'Vermont Legislature',
        date: '2025-06-01',
      },
      {
        title: 'Act 73 Explained: 10 Things to Know',
        url: 'https://vtdigger.org/2025/12/04/act-73-explained-10-things-to-know-about-vermonts-education-reform-law/',
        summary: "VTDigger's explainer on Act 73's key provisions, including what it means for tuitioning towns and independent schools.",
        type: 'news',
        publisher: 'VTDigger',
        date: '2025-12-04',
      },
    ],
  },
};

export default settings;
