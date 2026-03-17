import eventsSection from './events-section.twig';

const settings = {
  title: 'Collections/Events Section',
};

export const Default = {
  render: (args) => eventsSection(args),
  args: {
    label: 'Upcoming Events',
    items: [
      {
        month: 'MAR',
        day: '4',
        title: 'SJA Family Forum — Zoom',
        body: 'Dr. Sharon Howell, Headmaster of St. Johnsbury Academy, hosts a family briefing on the current legislative threats. Open to SJA families.',
        meta: '4:00 PM Eastern · Zoom · Meeting ID: 813 4230 1017',
      },
      {
        month: 'MAR',
        day: '4',
        title: 'NEK Choice District Meeting — Maidstone',
        body: 'Deputy Director of Education Jill Campbell-Briggs meets with NEK Choice district community members to discuss education reform proposals.',
        meta: 'Evening · Maidstone, VT · Open to NEK Choice town residents',
      },
      {
        month: 'TBD',
        day: '—',
        title: 'More events coming',
        body: 'Sign up to receive event announcements, action alerts, and legislative updates directly to your inbox.',
        signup: {
          placeholder: 'your@email.com',
          label: 'Notify Me',
        },
      },
    ],
  },
};

export default settings;
