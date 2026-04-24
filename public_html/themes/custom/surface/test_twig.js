import twig from 'twig';
import fs from 'fs';
import path from 'path';
import { fileURLToPath, URL } from 'url';
import { createRequire } from 'module';

const require = createRequire(import.meta.url);
const twigDrupalFilters = require('twig-drupal-filters');

const __dirname = path.dirname(fileURLToPath(import.meta.url));

// Register Drupal filters/functions (without, clean_class, etc.)
twigDrupalFilters(twig);

// attach_library is Drupal-only; twig-drupal-filters doesn't include it
twig.extendFunction('attach_library', () => '');

const src = fs.readFileSync(
  path.resolve(__dirname, 'source/patterns/components/node-article-body/node-article-body.twig'),
  'utf8'
);

const render = (data) => twig.twig({ data: src }).render(data).trim();

let passed = 0;
let failed = 0;

function assert(label, condition) {
  if (condition) {
    console.log(`PASSED: ${label}`);
    passed++;
  } else {
    console.error(`FAILED: ${label}`);
    failed++;
  }
}

const full = render({
  title: "What Is Vermont's Town Tuition Program?",
  topic: 'Town Tuition',
  date: 'March 12, 2026',
  date_iso: '2026-03-12',
  lead: 'Vermont\'s Town Tuition Program is one of the oldest school choice systems.',
  body: '<p>For generations, families in Vermont towns have used this program.</p>',
});

assert('renders wrapper element', full.includes('node-article-body'));
assert('renders topic', full.includes('Town Tuition'));
assert('renders title', full.includes("What Is Vermont's Town Tuition Program?"));
assert('renders date with datetime attr', full.includes('datetime="2026-03-12"'));
assert('renders lead text', full.includes('Vermont\'s Town Tuition Program'));
assert('renders body HTML', full.includes('<p>For generations'));

const noTopic = render({
  title: 'Test Title',
  date: 'March 12, 2026',
  date_iso: '2026-03-12',
  body: '<p>Body content.</p>',
});

assert('omits topic span when not provided', !noTopic.includes('node-article-body__topic'));
assert('still renders title without topic', noTopic.includes('Test Title'));

const noLead = render({
  title: 'Test Title',
  topic: 'Test Topic',
  body: '<p>Body only.</p>',
});

assert('omits lead when not provided', !noLead.includes('node-article-body__lead'));
assert('still renders body without lead', noLead.includes('Body only.'));

const bodyOnly = render({
  title: 'Minimal',
  body: '<p>Just body.</p>',
});

assert('renders with only title and body', bodyOnly.includes('Minimal'));

console.log(`\n${passed} passed, ${failed} failed`);
if (failed > 0) process.exit(1);
