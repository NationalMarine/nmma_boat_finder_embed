(function ($, Drupal) {

  Drupal.behaviors.boatFinderEmbedStickyFilters = {
    attach: function (context, settings) {
      window.addEventListener('scroll', function() {
        const boatFinderApp = $('.boat-finder-app', context);
        if (boatFinderApp && boatFinderApp.length > 0) {
          const pageHeader = document.getElementById('header');
          const pageHeaderHeight = pageHeader.offsetHeight;
          const boatFinderOffset = boatFinderApp[0].getBoundingClientRect().top;

          if (boatFinderOffset < pageHeaderHeight) {
            pageHeader.classList.add('visually-hidden');
          }
          else {
            pageHeader.classList.remove('visually-hidden');
          }
        }
      });
    }
  }
})(jQuery, Drupal);
