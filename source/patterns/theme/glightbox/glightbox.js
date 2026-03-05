((Drupal, once) => {
  Drupal.behaviors.surfaceLightbox = {
    attach: function attach(context) {
      Drupal.surfaceLightbox.init(context);
    },
  };

  Drupal.surfaceLightbox = {
    init: (context) => {
      once('surfaceLightboxInit', '.glightbox', context).forEach(() => {
        GLightbox({});
      });
    },
  };
})(Drupal, once);
