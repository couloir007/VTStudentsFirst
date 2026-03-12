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

      // Logged out: no toolbar — reset any inline top and let CSS take over.
      if (total <= 0) {
        header.style.top = '';
        return;
      }

      const top = Math.max(floor, total - window.scrollY);
      header.style.top = `${top}px`;
    };

    window.addEventListener('scroll', handleToolbarScroll, { passive: true });
    handleToolbarScroll();
  }

  // Smooth scroll — delegated to document so it catches all anchors
  // regardless of when they were added to the DOM or JS aggregation order.
  document.addEventListener('click', (e) => {
    const anchor = e.target.closest('a[href^="#"]');
    if (!anchor) return;

    const id = anchor.getAttribute('href');
    if (!id || id === '#') return;

    let target;
    try {
      target = document.querySelector(id);
    } catch (_) {
      // Malformed selector — bail out gracefully.
      return;
    }
    if (!target) return;

    e.preventDefault();

    // rAF ensures header height is measured after any layout triggered
    // by the click (e.g. menus closing, class toggles).
    requestAnimationFrame(() => {
      const headerHeight = header ? header.getBoundingClientRect().height : 0;
      const { floor } = getToolbarOffset();
      const offset = headerHeight + floor;
      const top = target.getBoundingClientRect().top + window.scrollY - offset;

      if ('scrollBehavior' in document.documentElement.style) {
        window.scrollTo({ top, behavior: 'smooth' });
      } else {
        // Fallback for older browsers.
        window.scrollTo(0, top);
      }
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
