import voicesSection from './voices-section.twig';

const settings = {
  title: 'Components/Voices Section',
};

export const Default = {
  render: (args) => voicesSection(args),
  args: {
    section_label: 'Community Voices',
    section_headline: 'Vermont families,<br>in their own <em>words.</em>',
    body_text: '',
    voices: [
      {
        quote: 'Without regional school choice, we are confident we would be forced to relocate out of state — and we know we would not be alone. We chose to build our businesses and our lives here because of these opportunities.',
        name: 'Business Owner & SJA Parent — Waterford, Vermont',
        role: '<span style="color:var(--mist); font-style:italic;">(name withheld pending permission)</span>',
      },
      {
        quote: 'St. Johnsbury Academy is not simply an educational institution. It is a central economic engine for this town. Hundreds, and likely thousands, of individuals come to St. Johnsbury each day because of the Academy — fueling our gas stations, restaurants, shops, housing, and tax base.',
        name: 'Business Owner & Community Member — St. Johnsbury Region',
        role: '',
      },
      {
        quote: 'The most important thing right now is that we need more people to get involved, raise concerns, and share personal stories. Every voice that reaches Montpelier makes a difference.',
        name: 'NEK Community Advocate',
        role: '',
      },
      {
        quote: 'My daughter has an IEP and found her footing at the academy in ways I couldn\'t have predicted. The thought of a forced reassignment — to a school two towns over — is terrifying. We chose this community because of this school.',
        name: 'Parent — [Town], Vermont',
        role: '',
      },
    ],
  },
};

export default settings;
