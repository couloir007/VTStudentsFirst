((Drupal, once) => {
  Drupal.behaviors.primaryTabs = {
    attach: function attach(context) {
      Drupal.primaryTabs.init(context);
    },
  };

  Drupal.primaryTabs = {
    init: function (context) {
      once('surfaceTabsInit', '[data-drupal-nav-primary-tabs]', context).forEach((el) => {
        const tabs = el.querySelector('.tabs');
        const activeTab = tabs.querySelector('.is-active');

        if (this.isTabsMobileLayout() && activeTab && !activeTab.matches('.tabs__tab:first-child')) {
          const firstTab = tabs.querySelector('.tabs__tab:first-child');
          tabs.insertBefore(activeTab, firstTab);
        }
      });

      // Tabs click
      once('surfaceTabs', '.tabs__trigger', context).forEach((el) => {
        el.addEventListener('click', (e) => {
          e.preventDefault();
          this.toggleTabs();
        });
      });

      // Tabs keyboard navigation
      once('surfaceTabsKeyboard', '[role="tablist"]', context).forEach((tablist) => {
        const tabs = Array.from(tablist.querySelectorAll('[role="tab"]'));

        tablist.addEventListener('keydown', (e) => {
          let index = tabs.indexOf(document.activeElement);

          if (index < 0) return;

          let newIndex;
          switch (e.key) {
            case 'ArrowRight':
            case 'ArrowDown':
              newIndex = (index + 1) % tabs.length;
              break;
            case 'ArrowLeft':
            case 'ArrowUp':
              newIndex = (index - 1 + tabs.length) % tabs.length;
              break;
            case 'Home':
              newIndex = 0;
              break;
            case 'End':
              newIndex = tabs.length - 1;
              break;
            default:
              return;
          }

          if (newIndex !== undefined) {
            tabs[newIndex].focus();
            e.preventDefault();
          }
        });
      });
    },

    // Tabs in mobile layout.
    isTabsMobileLayout: () => {
      const tabs = document.querySelector('.tabs');
      return tabs.querySelector('.tabs__trigger').clientHeight > 0;
    },

    // Toggle tabs.
    toggleTabs: () => {
      const tabs = document.querySelector('.tabs');

      if (tabs.classList.contains('is-expanded')) {
        Drupal.primaryTabs.collapseTabs();
      } else {
        Drupal.primaryTabs.showTabs();
      }
    },

    // Collapse tabs.
    collapseTabs: () => {
      const tabs = document.querySelector('.tabs');
      const tabsTrigger = document.querySelector('.tabs__trigger');
      tabsTrigger.setAttribute('aria-expanded', 'false');
      tabs.classList.remove('is-expanded');
    },

    // Show tabs.
    showTabs: () => {
      const tabs = document.querySelector('.tabs');
      const tabsTrigger = document.querySelector('.tabs__trigger');
      tabsTrigger.setAttribute('aria-expanded', 'true');
      tabs.classList.add('is-expanded');
    },
  };
})(Drupal, once);
