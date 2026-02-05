/* Homepage UI helpers (Bootstrap 5)
   - Best sellers filter tabs
   - Responsive multi-item carousel (4/2/1)
*/

(function(){
  'use strict';

  // Best sellers: filter tabs
  function initBestSellerFilters(){
    var wrap = document.querySelector('[data-best-sellers]');
    if (!wrap) return;

    var buttons = wrap.querySelectorAll('[data-filter]');
    var items = document.querySelectorAll('.best-seller-item[data-cat]');

    function applyFilter(cat){
      items.forEach(function(item){
        var itemCat = item.getAttribute('data-cat');
        var show = (cat === 'all') || (String(itemCat) === String(cat));
        item.classList.toggle('d-none', !show);
      });
    }

    buttons.forEach(function(btn){
      btn.addEventListener('click', function(){
        buttons.forEach(function(b){ b.classList.remove('active'); });
        btn.classList.add('active');
        applyFilter(btn.getAttribute('data-filter') || 'all');
      });
    });

    applyFilter('all');
  }

  // Responsive carousel grouping: 4/2/1 cards per slide
  function getItemsPerSlide(){
    var w = window.innerWidth || document.documentElement.clientWidth;
    if (w >= 992) return 4;      // lg+
    if (w >= 768) return 2;      // md
    return 1;                    // mobile
  }

  function buildMultiCarousel(carouselEl){
    if (!carouselEl) return;

    var inner = carouselEl.querySelector('[data-carousel-items]');
    if (!inner) return;

    // Pull original items from a cache so we can rebuild on resize
    if (!carouselEl.__allItems) {
      carouselEl.__allItems = Array.prototype.slice.call(inner.querySelectorAll('.carousel-item'));
    }

    var allItems = carouselEl.__allItems;
    var per = getItemsPerSlide();

    // If already built with same per-slide, do nothing
    if (carouselEl.__builtPer === per) return;
    carouselEl.__builtPer = per;

    // Reset inner
    inner.innerHTML = '';

    // Build grouped slides
    var groups = [];
    for (var i = 0; i < allItems.length; i += per) {
      groups.push(allItems.slice(i, i + per));
    }

    groups.forEach(function(group, gi){
      var slide = document.createElement('div');
      slide.className = 'carousel-item' + (gi === 0 ? ' active' : '');

      var row = document.createElement('div');
      row.className = 'row g-3';

      group.forEach(function(item){
        // item contains a .card; move it into a responsive column
        var card = item.firstElementChild;
        if (!card) return;

        var col = document.createElement('div');
        // Responsive 4/2/1: 12 (1), 6 (2), 3 (4)
        col.className = 'col-12 col-md-6 col-lg-3';
        col.appendChild(card);
        row.appendChild(col);
      });

      slide.appendChild(row);
      inner.appendChild(slide);
    });

    // Rebuild indicators
    var indicators = carouselEl.querySelector('[data-carousel-indicators]');
    if (indicators) {
      indicators.innerHTML = '';
      groups.forEach(function(_, gi){
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.setAttribute('data-bs-target', '#' + carouselEl.id);
        btn.setAttribute('data-bs-slide-to', String(gi));
        btn.setAttribute('aria-label', 'Slide ' + (gi + 1));
        if (gi === 0) btn.className = 'active';
        indicators.appendChild(btn);
      });
    }

    // Reset bootstrap carousel instance to avoid stale state
    try {
      var inst = bootstrap.Carousel.getInstance(carouselEl);
      if (inst) inst.to(0);
    } catch(e) {}
  }

  function initMultiCarousels(){
    document.querySelectorAll('[data-multi-carousel]').forEach(function(carouselEl){
      buildMultiCarousel(carouselEl);
    });
  }

  // Cart: live line totals + grand total
  function initCartLiveTotals(){
    var form = document.getElementById('cartForm');
    if (!form) return;

    var totalEl = form.querySelector('.js-cart-total');
    var qtyInputs = form.querySelectorAll('input.js-qty');

    function recalc(){
      var grand = 0;
      qtyInputs.forEach(function(inp){
        var tr = inp.closest('tr');
        if (!tr) return;
        var unit = tr.querySelector('.js-unit-price');
        var unitPrice = unit ? parseFloat(unit.getAttribute('data-unit-price') || '0') : 0;
        var qty = parseInt(inp.value || '0', 10);
        if (isNaN(qty) || qty < 0) qty = 0;

        var line = unitPrice * qty;
        grand += line;

        var lineEl = tr.querySelector('.js-line-total');
        if (lineEl) lineEl.textContent = line.toFixed(2);
      });

      if (totalEl) totalEl.textContent = grand.toFixed(2);
    }

    qtyInputs.forEach(function(inp){
      inp.addEventListener('input', recalc);
      inp.addEventListener('change', recalc);
    });

    recalc();
  }

  // Debounced resize rebuild
  var resizeTimer = null;
  window.addEventListener('resize', function(){
    window.clearTimeout(resizeTimer);
    resizeTimer = window.setTimeout(initMultiCarousels, 120);
  });

  function boot(){
    initBestSellerFilters();
    initMultiCarousels();
    initCartLiveTotals();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
