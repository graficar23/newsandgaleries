(function($, Drupal) {
  Drupal.behaviors.home = {
    attach: function(context, settings) {




      $('.way-life .video-play .play', context).click(function() {

        $('.way-life  .embed-responsive').html('<iframe class="embed-responsive-item" src="" allowfullscreen="allowfullscreen"></iframe>');

        var video = $(this).attr('data-video');
        $('.way-life .w-l-video iframe').attr('src', '//www.youtube.com/embed/' + video );

        $('.way-life .w-l-video', context).css('z-index', '99');
        $('.way-life .w-l-video', context).animate({
          opacity: 1,
        }, 500, function() {});

      });


      $('.way-life .w-l-video span', context).click(function() {
        $('.way-life .w-l-video', context).animate({
          opacity: 0,
        }, 500, function() {
          $('.way-life .w-l-video', context).css('z-index', '-1');
          $('.way-life  .embed-responsive').html('');
        });
      });


      /* Vanilla JS */
/* If you know how to improve the code, here is my email: alexandr.kazakov1@gmail.com */
function setupFBframe(frame) {
  var container = frame.parentNode;

  var containerWidth = container.offsetWidth;
  var containerHeight = container.offsetHeight;

  var src =
    "https://www.facebook.com/plugins/page.php" +
    "?href=https%3A%2F%2Fwww.facebook.com%2FSuzukiColombia" +
    "&tabs=timeline" +
    "&width=" +
    containerWidth +
    "&height=" +
    containerHeight +
    "&small_header=false" +
    "&adapt_container_width=false" +
    "&hide_cover=true" +
    "&hide_cta=true" +
    "&show_facepile=true" +
    "&appId";

  frame.width = containerWidth;
  frame.height = containerHeight;
  frame.src = src;
}

/* begin Document Ready
############################################ */


  var facebookIframe = document.querySelector('#facebook_iframe', context);
  setupFBframe(facebookIframe);

  /* begin Window Resize
  ############################################ */

  // Why resizeThrottler? See more : https://developer.mozilla.org/ru/docs/Web/Events/resize
  (function() {
    window.addEventListener("resize", resizeThrottler, false);

    var resizeTimeout;

    function resizeThrottler() {
      if (!resizeTimeout) {
        resizeTimeout = setTimeout(function() {
          resizeTimeout = null;
          actualResizeHandler();
        }, 66);
      }
    }

    function actualResizeHandler() {
      document.querySelector('#facebook_iframe', context).removeAttribute('src');
      setupFBframe(facebookIframe);
    }
  })();
  /* end Window Resize
  ############################################ */






    }
  };
})(jQuery, Drupal);
