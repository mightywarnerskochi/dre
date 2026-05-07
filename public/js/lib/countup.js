function debounce(func, wait, immediate) {
  let timeout;
  return function() {
    const context = this, args = arguments;
    const later = function() {
      timeout = null;
      if (!immediate) func.apply(context, args);
    };
    const callNow = immediate && !timeout;
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
    if (callNow) func.apply(context, args);
  };
}

function isInViewport(element) {
  if (!element || typeof element.getBoundingClientRect !== 'function') return false;
  const rect = element.getBoundingClientRect();
  const vh = window.innerHeight || document.documentElement.clientHeight;
  const vw = window.innerWidth  || document.documentElement.clientWidth;
  const verticallyVisible = rect.top < vh && rect.bottom > 0;
  const horizontallyVisible = rect.left < vw && rect.right > 0;
  return verticallyVisible && horizontallyVisible;
}

function startOneCounter($el){
  // Reset before starting again
  const options = $.extend({}, $el.data('countToOptions'), {
    from:            Number($el.data('from')) || 0,
    to:              Number($el.data('to'))   || 0,
    speed:           Number($el.data('speed'))|| 1000,
    refreshInterval: Number($el.data('refresh-interval')) || 100,
    decimals:        Number($el.data('decimals')) || 0,
  });
  $el.countTo(options);
}

const scanAndStart = debounce(function() {
  $('.timer').each(function(){
    const $t = $(this);
    const inView = isInViewport(this);
    const wasInView = $t.data('inView') || false;

    if (inView && !wasInView) {
      // just entered viewport — start counter
      startOneCounter($t);
    }

    // update visibility state
    $t.data('inView', inView);
  });
}, 200);

$(document).ready(function() {
  scanAndStart();
  $(window).on('scroll resize', scanAndStart);
});

/* ---- countTo plugin (unchanged) ---- */
(function ($) {
  $.fn.countTo = function (options) {
    options = options || {};
    return this.each(function () {
      const $self = $(this);
      const settings = $.extend({}, $.fn.countTo.defaults, {
        from:            $self.data('from'),
        to:              $self.data('to'),
        speed:           $self.data('speed'),
        refreshInterval: $self.data('refresh-interval'),
        decimals:        $self.data('decimals')
      }, options);

      let loops = Math.max(1, Math.ceil(settings.speed / settings.refreshInterval));
      const increment = (settings.to - settings.from) / loops;
      let loopCount = 0;
      let value = Number(settings.from) || 0;
      const data = $self.data('countTo') || {};

      if (data.interval) clearInterval(data.interval);
      data.interval = setInterval(updateTimer, settings.refreshInterval);
      $self.data('countTo', data);
      render(value);

      function updateTimer() {
        value += increment;
        loopCount++;
        render(value);
        if (typeof(settings.onUpdate) === 'function') settings.onUpdate.call($self[0], value);

        if (loopCount >= loops) {
          clearInterval(data.interval);
          $self.removeData('countTo');
          value = settings.to;
          render(value);
          if (typeof(settings.onComplete) === 'function') settings.onComplete.call($self[0], value);
        }
      }

      function render(v) {
        const formattedValue = settings.formatter.call($self[0], v, settings);
        $self.html(formattedValue);
      }
    });
  };

  $.fn.countTo.defaults = {
    from: 0,
    to: 0,
    speed: 1000,
    refreshInterval: 100,
    decimals: 0,
    formatter: function(value, settings) {
      return Number(value).toFixed(settings.decimals);
    },
    onUpdate: null,
    onComplete: null
  };
}(jQuery));
