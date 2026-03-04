import testimonial from './testimonial.twig';

const settings = {
  title: 'Components/Testimonial',
};

export const BusinessOwner = {
  render: (args) => testimonial(args),
  args: {
    quote: 'Without regional school choice, we are confident we would be forced to relocate out of state — and we know we would not be alone. We chose to build our businesses and our lives here because of these opportunities.',
    attribution: 'Business Owner & SJA Parent — Waterford, Vermont (name withheld pending permission)',
  },
};

export const EconomicAnchor = {
  render: (args) => testimonial(args),
  args: {
    quote: 'St. Johnsbury Academy is not simply an educational institution. It is a central economic engine for this town. Hundreds, and likely thousands, of individuals come to St. Johnsbury each day because of the Academy — fueling our gas stations, restaurants, shops, housing, and tax base.',
    attribution: 'Business Owner & Community Member — St. Johnsbury Region',
  },
};

export const CommunityAdvocate = {
  render: (args) => testimonial(args),
  args: {
    quote: 'The most important thing right now is that we need more people to get involved, raise concerns, and share personal stories. Every voice that reaches Montpelier makes a difference.',
    attribution: 'NEK Community Advocate',
  },
};

export const ParentAnonymous = {
  render: (args) => testimonial(args),
  args: {
    quote: 'My daughter has an IEP and found her footing at the academy in ways I could not have predicted. The thought of a forced reassignment — to a school two towns over — is terrifying. We chose this community because of this school.',
    attribution: 'Parent — [Town], Vermont',
  },
};

export default settings;
