document.addEventListener('DOMContentLoaded', () => {
  const btn = document.querySelector('.scroll-top');
  if (!btn) return;

  const THRESHOLD = 400;

  const toggle = () => {
    const visible = window.scrollY > THRESHOLD;
    btn.classList.toggle('scroll-top--visible', visible);
    btn.hidden = !visible;
  };

  btn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  window.addEventListener('scroll', toggle, { passive: true });
  toggle();
});
