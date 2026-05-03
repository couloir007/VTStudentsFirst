import tuitioningTownsSection from './tuitioning-towns-section.twig';

const meta = {
  title: 'Collections/Tuitioning Towns Section',
  tags: ['autodocs'],
  render: (args) => tuitioningTownsSection(args),
  argTypes: {
    section_label: { control: 'text' },
    headline:      { control: 'text' },
    body_text:     { control: 'text' },
    modifier:      { control: 'text' },
  },
};

export default meta;

// Stub table matching the block_2 View output structure
const stubTable = `
<table class="views-table">
  <thead>
    <tr>
      <th>Town</th>
      <th>Sending Schools</th>
    </tr>
  </thead>
  <tbody>
    <tr><td><a href="#">Alburgh</a></td><td>Missisquoi Valley Union High School</td></tr>
    <tr><td><a href="#">Barnet</a></td><td>St. Johnsbury Academy</td></tr>
    <tr><td><a href="#">Burke</a></td><td>St. Johnsbury Academy</td></tr>
    <tr><td><a href="#">Granville</a></td><td>Harwood Union High School</td></tr>
    <tr><td><a href="#">Hancock</a></td><td>Harwood Union High School</td></tr>
    <tr><td><a href="#">Waterford</a></td><td>St. Johnsbury Academy</td></tr>
    <tr><td><a href="#">Winhall</a></td><td>Burr and Burton Academy</td></tr>
  </tbody>
</table>
`;

export const Default = {
  args: {
    section_label: 'School Choice',
    headline:      'Vermont Tuitioning Communities',
    body_text:     '<p>Vermont has approximately 95 communities that tuition students to public or independent schools for some or all grade levels. These towns have no public school at that grade level — they rely on tuitioning as their public education system.</p>',
    towns_view:    stubTable,
  },
};

export const NoIntro = {
  args: {
    headline:   'Vermont Tuitioning Communities',
    towns_view: stubTable,
  },
};
