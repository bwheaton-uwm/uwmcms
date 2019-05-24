/**
 * @file
 * Homepage parallax for UW Medicine.
 *
 * Library: uwmbase/homepage.
 */

(function () {
  // Media query event handler:
  if (matchMedia) {
    const mq = window.matchMedia("(min-width: 1200px)");
    mq.addListener(WidthChange);
    WidthChange(mq);
  }

  // Detect media query change and turn scrollmagic controller on/off.
  function WidthChange(mq) {
    if (mq.matches) {
      // Screen size is 1200px:
      if (window.controller && window.controller.enabled() === false) {
        // There's already a controller but it's disabled.
        window.controller.enabled(true);
      }
      else {
        // There's no controller so let's create one and add scenes.
        window.controller = new ScrollMagic.Controller({
          globalSceneOptions: {
            triggerHook: 'onLeave'
          }
        });

        // Get all slides.
        var slides = document.querySelectorAll("section.homepage-section");

        // Create scene for every slide.
        for (var i = 0; i < slides.length; i++) {
          new ScrollMagic.Scene({
            triggerElement: slides[i],
            reverse: false,
            offset: -350
          })
              .addTo(controller)
              .on("start", function (e) {
                e.currentTarget.triggerElement().className += " animation-applied";
              })
        }
      }
    }
    else {
      // Screen size is less than 1200px:
      if (window.controller && window.controller.enabled() === true) {
        // There's a controller, let's disable it.
        window.controller = controller.enabled(false);
      }
    }
  }
})();
