import personCardTemplate from './person-card.twig';
import './person-card.css';

export default {
  title: 'Components/Person Card',
  tags: ['autodocs'],
};

const sean = {
  name: 'Sean Montague',
  job_title: 'Web Developer, Volunteer',
  town: 'Burke',
  school: 'St. Johnsbury Academy',
  quote: 'School choice is part of the package deal that makes it worth living here.',
};

const placeholder1 = {
  name: 'Jane Farmer',
  job_title: 'Small Business Owner',
  town: 'Thetford',
  school: 'Thetford Academy',
  quote: 'These schools are the reason people stay.',
};

const placeholder2 = {
  name: 'Mark Rivers',
  job_title: 'Parent',
  town: 'Manchester',
  school: 'Burr and Burton Academy',
  quote: 'My kids got opportunities here I never imagined.',
};

const placeholder3 = {
  name: 'Carol Bishop',
  job_title: 'Alumni, Business Owner',
  town: 'Lyndonville',
  school: 'Lyndon Institute',
  quote: "Lyndon Institute shaped who I am. I can't stand by while that's taken from the next generation.",
};

export const Default = {
  render: () => personCardTemplate(sean),
};

export const Grid = {
  render: () => `
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; padding: 2rem; background: var(--gray-50, #f9f9f7);">
      ${[sean, placeholder1, placeholder2, placeholder3].map(p => personCardTemplate(p)).join('')}
    </div>
  `,
};

export const NoImage = {
  render: () => personCardTemplate({ ...sean, image: null }),
};

export const NoQuote = {
  render: () => personCardTemplate({ ...placeholder1, quote: null }),
};
