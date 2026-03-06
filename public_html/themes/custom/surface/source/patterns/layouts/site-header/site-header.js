document.addEventListener('DOMContentLoaded', () => {
  const header = document.querySelector('.site-header');
  const siteBranding = document.querySelector('.site-header .site-branding');
  const sitePrimary = document.querySelector('.site-primary__content');

  function getToolbarOffset() {
    const adminToolbar = document.querySelector('#toolbar-administration');
    const ginSecondary = document.querySelector('.gin-secondary-toolbar--frontend');

    const adminHeight = adminToolbar ? adminToolbar.getBoundingClientRect().height : 0;
    const ginHeight = ginSecondary ? ginSecondary.getBoundingClientRect().height : 0;

    return { total: adminHeight + ginHeight, floor: adminHeight };
  }

  // Scroll-to-floor: header slides from full toolbar offset down to
  // #toolbar-administration height (the persistent bar), never below it.
  if (header) {
    const handleToolbarScroll = () => {
      const { total, floor } = getToolbarOffset();
      if (total <= 0) return;

      const top = Math.max(floor, total - window.scrollY);
      header.style.top = `${top}px`;
    };

    window.addEventListener('scroll', handleToolbarScroll, { passive: true });
    handleToolbarScroll();
  }

  // Smooth scroll for anchor links, offset by header height.
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener('click', (e) => {
      const id = anchor.getAttribute('href');
      if (id === '#') return;

      const target = document.querySelector(id);
      if (!target) return;

      e.preventDefault();

      const headerHeight = header ? header.getBoundingClientRect().height : 0;
      const { floor } = getToolbarOffset();
      const offset = headerHeight + floor;
      const top = target.getBoundingClientRect().top + window.scrollY - offset;

      window.scrollTo({ top, behavior: 'smooth' });
    });
  });

  // Transparent-over-hero — front page only.
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
