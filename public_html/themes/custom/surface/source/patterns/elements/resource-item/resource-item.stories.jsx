import resourceItem from './resource-item.twig';
import resourceItemData from './resource-item.yml';

const settings = {
  title: 'Elements/Resource Item',
};

export const Default = {
  render: (args) => `<ul class="resource-list__items">${resourceItem(args)}</ul>`,
  args: {
    ...resourceItemData,
  },
};

export const Legislation = {
  render: (args) => `<ul class="resource-list__items">${resourceItem(args)}</ul>`,
  args: {
    title: 'H.777 — An Act Relating to Education Governance',
    url: 'https://legislature.vermont.gov/bill/status/2026/H.777',
    summary: 'Pending legislation that would allow school boards to designate a single receiving school, replacing family-level tuitioning access with board-controlled assignment.',
    type: 'legislation',
    publisher: 'Vermont Legislature',
    date: '2026-01-15',
  },
};

export const News = {
  render: (args) => `<ul class="resource-list__items">${resourceItem(args)}</ul>`,
  args: {
    title: 'Act 73 Explained: 10 Things to Know',
    url: 'https://vtdigger.org/2025/12/04/act-73-explained-10-things-to-know-about-vermonts-education-reform-law/',
    summary: "VTDigger's explainer on Act 73's key provisions, including what it means for tuitioning towns and independent schools.",
    type: 'news',
    publisher: 'VTDigger',
    date: '2025-12-04',
  },
};

export default settings;
