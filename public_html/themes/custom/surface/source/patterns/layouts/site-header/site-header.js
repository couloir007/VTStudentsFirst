document.addEventListener('DOMContentLoaded', () => {
  const header = document.querySelector('.site-header');
  const siteBranding = document.querySelector('.site-header .site-branding');
  const sitePrimary = document.querySelector('.site-primary__content');

  // Transparent-over-hero — front page only.
  // body.path-frontpage is added by Drupal on the homepage.
  // CSS makes the header transparent by default on that page.
  // JS adds --scrolled once the user passes 60% of the hero height,
  // which restores the opaque background.
  if (header && document.body.classList.contains('path-frontpage')) {
    const hero = document.querySelector('.hero-section');
    const threshold = hero ? hero.offsetHeight * 0.6 : 80;

    const handleScroll = () => {
      if (window.scrollY > threshold) {
        header.classList.add('site-header--scrolled');
      } else {
        header.classList.remove('site-header--scrolled');
      }
    };

    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();
  }

  // Branding hide-on-scroll (non-front pages).
  if (siteBranding && sitePrimary) {
    window.addEventListener(
      'scroll',
      () => {
        const hidden = window.scrollY > 0;
        siteBranding.classList.toggle('primary-hidden', hidden);
        sitePrimary.classList.toggle('primary-hidden', hidden);
      },
      { passive: true }
    );
  }
});
