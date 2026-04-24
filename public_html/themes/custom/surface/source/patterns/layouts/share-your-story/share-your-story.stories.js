import shareYourStoryTemplate from './share-your-story.twig';
import './share-your-story.css';

const REQUIRED_FIELDS = ['mock-name', 'mock-email', 'mock-town', 'mock-role', 'mock-testimonial'];

const CONFIRMATION_HTML = `
  <div class="share-story section-theme--warm">
    <div class="share-story__inner">
      <div class="webform-confirmation">
        <h2>Thank you for sharing your story.</h2>
        <p>Your submission has been received. If you gave permission to publish, your story may appear on this site. We will be in touch if we have any questions.</p>
        <p><a href="/">Return to the homepage</a></p>
      </div>
    </div>
  </div>
`;

function attachInteractivity(container) {
  const form = container.querySelector('.share-story__form');
  const submitBtn = container.querySelector('.button--primary');
  if (!submitBtn || !form) return;

  // Inline error helper.
  function setError(id, msg) {
    const input = container.querySelector(`#${id}`);
    if (!input) return;
    let err = input.parentElement.querySelector('.form-error');
    if (msg) {
      if (!err) {
        err = document.createElement('div');
        err.className = 'form-error';
        err.style.cssText = 'color:#c0392b;font-size:0.82rem;margin-top:0.25rem;';
        input.parentElement.appendChild(err);
      }
      err.textContent = msg;
      input.style.borderColor = '#c0392b';
    } else {
      if (err) err.remove();
      input.style.borderColor = '';
    }
  }

  function validate() {
    let valid = true;
    REQUIRED_FIELDS.forEach((id) => {
      const el = container.querySelector(`#${id}`);
      if (!el || !el.value.trim()) {
        setError(id, 'This field is required.');
        valid = false;
      } else {
        setError(id, null);
      }
    });

    // Basic email format check.
    const email = container.querySelector('#mock-email');
    if (email && email.value.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
      setError('mock-email', 'Please enter a valid email address.');
      valid = false;
    }

    return valid;
  }

  // Clear errors on input.
  REQUIRED_FIELDS.forEach((id) => {
    container.querySelector(`#${id}`)?.addEventListener('input', () => setError(id, null));
  });

  submitBtn.addEventListener('click', () => {
    if (!validate()) return;

    // Swap to confirmation view.
    container.innerHTML = CONFIRMATION_HTML;
  });
}

export default {
  title: 'Layouts/Share Your Story',
  tags: ['autodocs'],
  decorators: [
    (story) => {
      const el = story();
      setTimeout(() => attachInteractivity(document.querySelector('.share-story')), 0);
      return el;
    },
  ],
};

export const Default = {
  render: () =>
    shareYourStoryTemplate({
      section_label: 'Your Voice Matters',
      title: 'Share Your Story',
      lead: 'Legislators respond to real people with real stories. Tell us what independent schools mean to your family and community — your words may be the ones that make a difference.',
    }),
};

export const Confirmation = {
  render: () => CONFIRMATION_HTML,
};
