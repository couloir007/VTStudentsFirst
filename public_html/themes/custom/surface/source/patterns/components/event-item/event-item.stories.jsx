import eventItem from './event-item.twig';

const settings = {
  title: 'Components/Event Item',
};

export const Default = {
  render: (args) => eventItem(args),
  args: {
    month: 'MAR',
    day: '4',
    title: 'SJA Family Forum — Zoom',
    body: 'Dr. Sharon Howell, Headmaster of St. Johnsbury Academy, hosts a family briefing on the current legislative threats. Open to SJA families.',
    meta: '4:00 PM Eastern · Zoom · Meeting ID: 813 4230 1017',
  },
};

export const Signup = {
  render: (args) => eventItem(args),
  args: {
    month: 'TBD',
    day: '—',
    title: 'More events coming',
    body: 'Sign up to receive event announcements, action alerts, and legislative updates directly to your inbox.',
    signup: {
      placeholder: 'your@email.com',
      label: 'Notify Me',
    },
  },
};

export default settings;
