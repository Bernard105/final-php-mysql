/* Homepage UI helpers (Bootstrap 5)
   - Best sellers filter tabs
   - Responsive multi-item carousel (4/2/1)
*/

(function(){
  'use strict';

  // ------------------------------------------------------------
  // Toast helper (Bootstrap 5)
  // ------------------------------------------------------------
  function ensureToastContainer(){
    var c = document.getElementById('appToastContainer');
    if (c) return c;
    c = document.createElement('div');
    c.id = 'appToastContainer';
    c.className = 'toast-container position-fixed top-0 end-0 p-3';
    document.body.appendChild(c);
    return c;
  }

  function showToast(message, type){
    try{
      var container = ensureToastContainer();
      var t = document.createElement('div');
      t.className = 'toast align-items-center text-bg-' + (type || 'primary') + ' border-0';
      t.setAttribute('role', 'status');
      t.setAttribute('aria-live', 'polite');
      t.setAttribute('aria-atomic', 'true');
      t.innerHTML =
        '<div class="d-flex">' +
          '<div class="toast-body">' + String(message || '') + '</div>' +
          '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>' +
        '</div>';
      container.appendChild(t);
      var toast = bootstrap.Toast.getOrCreateInstance(t, { delay: 3200 });
      toast.show();
      t.addEventListener('hidden.bs.toast', function(){ t.remove(); });
    }catch(e){
      // ultra-safe fallback
      if (message) window.alert(message);
    }
  }

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

  // Navbar elevation on scroll + active link highlight
  function initNavbarBehaviors(){
    var nav = document.querySelector('.navbar');
    if (!nav) return;

    function toggleElev(){
      nav.classList.toggle('navbar-elevated', (window.scrollY || 0) > 8);
    }
    toggleElev();
    window.addEventListener('scroll', toggleElev, { passive: true });

    // active link (best effort)
    try{
      var path = (window.location.pathname || '').replace(/\/+$/,'');
      nav.querySelectorAll('a.nav-link').forEach(function(a){
        var href = a.getAttribute('href') || '';
        if (!href) return;
        // compare just path part
        var u = new URL(href, window.location.origin);
        var p = (u.pathname || '').replace(/\/+$/,'');
        if (p && p === path) a.classList.add('active');
      });
    }catch(e){}
  }

  // Back-to-top button
  function initBackToTop(){
    var btn = document.getElementById('backToTop');
    if (!btn) {
      btn = document.createElement('button');
      btn.type = 'button';
      btn.id = 'backToTop';
      btn.className = 'btn btn-primary btn-icon back-to-top';
      btn.setAttribute('aria-label', 'Back to top');
      btn.innerHTML = '<i class="fas fa-arrow-up" aria-hidden="true"></i>';
      document.body.appendChild(btn);
    }

    function toggle(){
      btn.classList.toggle('show', (window.scrollY || 0) > 500);
    }
    toggle();
    window.addEventListener('scroll', toggle, { passive: true });
    btn.addEventListener('click', function(){
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // ------------------------------------------------------------
  // Image skeletons (progressive enhancement)
  // ------------------------------------------------------------
  function initImageSkeletons(){
    var imgs = document.querySelectorAll('img.img-skeleton');
    if (!imgs.length) return;

    imgs.forEach(function(img){
      function done(){
        try{ img.classList.add('is-loaded'); }catch(e){}
      }
      if (img.complete && img.naturalWidth > 0) {
        done();
        return;
      }
      img.addEventListener('load', done, { once: true });
      img.addEventListener('error', done, { once: true });
    });
  }

  // ------------------------------------------------------------
  // Product quick view (Bootstrap modal)
  // Uses only data already rendered on the page.
  // ------------------------------------------------------------
  function initQuickView(){
    var modalEl = document.getElementById('quickViewModal');
    if (!modalEl) return;

    var imgEl = modalEl.querySelector('[data-qv-img]');
    var imgSkel = modalEl.querySelector('[data-qv-img-skel]');
    var titleEl = modalEl.querySelector('[data-qv-title]');
    var priceEl = modalEl.querySelector('[data-qv-price]');
    var detailsEl = modalEl.querySelector('[data-qv-details]');
    var addBtn = modalEl.querySelector('[data-qv-add]');

    var current = { id: null, url: '#', img: '', title: '', price: '' };

    function setContent(data){
      current = data || current;
      if (titleEl) titleEl.textContent = current.title || '';
      if (priceEl) priceEl.textContent = (current.price || '').toString();
      if (detailsEl) detailsEl.setAttribute('href', current.url || '#');

      if (imgEl) {
        if (imgSkel) imgSkel.classList.remove('is-loaded');
        imgEl.setAttribute('src', current.img || '');
        imgEl.addEventListener('load', function(){ if (imgSkel) imgSkel.classList.add('is-loaded'); }, { once: true });
        imgEl.addEventListener('error', function(){ if (imgSkel) imgSkel.classList.add('is-loaded'); }, { once: true });
      }
    }

    // Delegate clicks from cards
    document.addEventListener('click', function(e){
      var trigger = e.target && e.target.closest ? e.target.closest('[data-quick-view]') : null;
      if (!trigger) return;

      var card = trigger.closest('[data-product-card]');
      if (!card) return;

      e.preventDefault();

      var data = {
        id: card.getAttribute('data-product-id') || null,
        title: card.getAttribute('data-product-title') || '',
        price: card.getAttribute('data-product-price') || '',
        img: card.getAttribute('data-product-img') || '',
        url: card.getAttribute('data-product-url') || (trigger.getAttribute('href') || '#')
      };
      setContent(data);

      try{
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
      }catch(err){
        // fallback: go to details
        window.location.href = data.url;
      }
    });

    if (addBtn) {
      addBtn.addEventListener('click', function(){
        if (!current.id) return;
        try{ window.addToCart && window.addToCart(current.id, 1); }catch(e){}
      });
    }
  }

  // ------------------------------------------------------------
  // Product grid: instant filter/sort/price range (current page only)
  // ------------------------------------------------------------
  function initProductsToolbar(){
    var toolbar = document.querySelector('[data-products-toolbar]');
    if (!toolbar) return;

    var cards = Array.prototype.slice.call(document.querySelectorAll('[data-product-card]'));
    if (!cards.length) return;

    var totalEl = toolbar.querySelector('[data-products-total]');
    var visibleEl = toolbar.querySelector('[data-products-visible]');
    var qEl = toolbar.querySelector('[data-products-q]');
    var sortEl = toolbar.querySelector('[data-products-sort]');
    var resetEl = toolbar.querySelector('[data-products-reset]');
    var rMin = toolbar.querySelector('[data-price-min]');
    var rMax = toolbar.querySelector('[data-price-max]');
    var nMin = toolbar.querySelector('[data-price-min-input]');
    var nMax = toolbar.querySelector('[data-price-max-input]');

    var cols = cards.map(function(card){ return card.closest('.col-12, [class*="col-"]') || card.parentElement; });
    var row = cols[0] ? cols[0].parentElement : null;
    var original = cols.slice();

    function parsePrice(card){
      var p = parseFloat(card.getAttribute('data-product-price') || '0');
      return isNaN(p) ? 0 : p;
    }

    var prices = cards.map(parsePrice).filter(function(x){ return typeof x === 'number'; });
    var minP = Math.min.apply(Math, prices);
    var maxP = Math.max.apply(Math, prices);
    if (!isFinite(minP)) minP = 0;
    if (!isFinite(maxP)) maxP = 0;

    function clamp(v, lo, hi){ return Math.min(hi, Math.max(lo, v)); }

    function initRanges(){
      if (!rMin || !rMax || !nMin || !nMax) return;
      var step = 0.01;

      [rMin, rMax].forEach(function(r){
        r.setAttribute('min', String(minP));
        r.setAttribute('max', String(maxP));
        r.setAttribute('step', String(step));
      });

      rMin.value = String(minP);
      rMax.value = String(maxP);
      nMin.value = String(minP.toFixed(2));
      nMax.value = String(maxP.toFixed(2));
    }

    function getFilters(){
      var q = (qEl && qEl.value ? qEl.value : '').trim().toLowerCase();
      var a = nMin ? parseFloat(nMin.value || String(minP)) : minP;
      var b = nMax ? parseFloat(nMax.value || String(maxP)) : maxP;
      if (isNaN(a)) a = minP;
      if (isNaN(b)) b = maxP;
      if (a > b) { var t = a; a = b; b = t; }
      return { q: q, min: a, max: b, sort: (sortEl && sortEl.value) ? sortEl.value : 'relevance' };
    }

    function apply(){
      var f = getFilters();
      var visible = 0;

      // filter
      cards.forEach(function(card){
        var title = (card.getAttribute('data-product-title') || '').toLowerCase();
        var price = parsePrice(card);
        var ok = true;
        if (f.q && title.indexOf(f.q) === -1) ok = false;
        if (price < f.min || price > f.max) ok = false;

        var col = card.closest('.col-12, [class*="col-"]') || card.parentElement;
        if (col) col.classList.toggle('d-none', !ok);
        if (ok) visible++;
      });

      // sort (only for visible items)
      if (row && f.sort && f.sort !== 'relevance') {
        var pairs = original
          .map(function(col){
            var card = col ? col.querySelector('[data-product-card]') : null;
            if (!card) return null;
            var hidden = col.classList.contains('d-none');
            return { col: col, card: card, hidden: hidden };
          })
          .filter(Boolean);

        var visiblePairs = pairs.filter(function(p){ return !p.hidden; });
        var hiddenPairs = pairs.filter(function(p){ return p.hidden; });

        visiblePairs.sort(function(a,b){
          var pa = parsePrice(a.card);
          var pb = parsePrice(b.card);
          var ta = (a.card.getAttribute('data-product-title') || '').toLowerCase();
          var tb = (b.card.getAttribute('data-product-title') || '').toLowerCase();
          if (f.sort === 'price_asc') return pa - pb;
          if (f.sort === 'price_desc') return pb - pa;
          if (f.sort === 'title_asc') return ta.localeCompare(tb);
          if (f.sort === 'title_desc') return tb.localeCompare(ta);
          return 0;
        });

        // re-append in order: visible first, then hidden (keeps DOM stable)
        visiblePairs.concat(hiddenPairs).forEach(function(p){ row.appendChild(p.col); });
      }

      if (totalEl) totalEl.textContent = String(cards.length);
      if (visibleEl) visibleEl.textContent = String(visible);
    }

    function syncRangesFromNumbers(){
      if (!rMin || !rMax || !nMin || !nMax) return;
      var a = clamp(parseFloat(nMin.value || String(minP)), minP, maxP);
      var b = clamp(parseFloat(nMax.value || String(maxP)), minP, maxP);
      if (isNaN(a)) a = minP;
      if (isNaN(b)) b = maxP;
      if (a > b) { var t=a; a=b; b=t; }
      rMin.value = String(a);
      rMax.value = String(b);
      nMin.value = a.toFixed(2);
      nMax.value = b.toFixed(2);
    }

    function syncNumbersFromRanges(){
      if (!rMin || !rMax || !nMin || !nMax) return;
      var a = clamp(parseFloat(rMin.value || String(minP)), minP, maxP);
      var b = clamp(parseFloat(rMax.value || String(maxP)), minP, maxP);
      if (a > b) { var t=a; a=b; b=t; }
      // keep handles from crossing
      rMin.value = String(a);
      rMax.value = String(b);
      nMin.value = a.toFixed(2);
      nMax.value = b.toFixed(2);
    }

    // listeners
    if (qEl) qEl.addEventListener('input', function(){ apply(); });
    if (sortEl) sortEl.addEventListener('change', function(){ apply(); });

    if (rMin && rMax) {
      rMin.addEventListener('input', function(){ syncNumbersFromRanges(); apply(); });
      rMax.addEventListener('input', function(){ syncNumbersFromRanges(); apply(); });
    }
    if (nMin && nMax) {
      nMin.addEventListener('input', function(){ syncRangesFromNumbers(); apply(); });
      nMax.addEventListener('input', function(){ syncRangesFromNumbers(); apply(); });
    }

    if (resetEl) {
      resetEl.addEventListener('click', function(){
        if (qEl) qEl.value = '';
        if (sortEl) sortEl.value = 'relevance';
        if (nMin) nMin.value = minP.toFixed(2);
        if (nMax) nMax.value = maxP.toFixed(2);
        syncRangesFromNumbers();
        // restore original order
        if (row) original.forEach(function(col){ row.appendChild(col); });
        apply();
      });
    }

    initRanges();
    apply();
  }

  // ------------------------------------------------------------
  // Add-to-cart buttons on product cards (non-inline)
  // ------------------------------------------------------------
  function initAddToCartButtons(){
    document.addEventListener('click', function(e){
      var btn = e.target && e.target.closest ? e.target.closest('[data-add-to-cart]') : null;
      if (!btn) return;
      var id = btn.getAttribute('data-product-id');
      if (!id) return;
      try{ window.addToCart && window.addToCart(id, 1); }catch(err){}
    });
  }

  // ------------------------------------------------------------
  // Mini-cart offcanvas (safe: if anything fails, link behaves normally)
  // ------------------------------------------------------------
  function initMiniCart(){
    var link = document.querySelector('.js-cart-link');
    var offEl = document.getElementById('miniCart');
    if (!link || !offEl) return;

    var body = offEl.querySelector('[data-mini-cart-body]');
    var skeleton = offEl.querySelector('[data-mini-cart-skeleton]');
    var content = offEl.querySelector('[data-mini-cart-content]');
    var totalEl = offEl.querySelector('[data-mini-cart-total]');

    var last = { at: 0, html: '' };

    function setLoading(on){
      if (skeleton) skeleton.classList.toggle('d-none', !on);
      if (content) content.classList.toggle('d-none', on);
    }

    function escapeHtml(str){
      return String(str || '').replace(/[&<>"']/g, function(m){
        return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]);
      });
    }

    function renderFromCartPage(html){
      var parser = new DOMParser();
      var doc = parser.parseFromString(html, 'text/html');
      var rows = doc.querySelectorAll('table tbody tr');
      var items = [];

      rows.forEach(function(tr){
        try{
          var img = tr.querySelector('img');
          var title = tr.querySelector('.fw-semibold');
          var unit = tr.querySelector('.js-unit-price');
          var qty = tr.querySelector('input.js-qty');
          var remove = tr.querySelector('a.btn-outline-danger');
          items.push({
            img: img ? img.getAttribute('src') : '',
            title: title ? title.textContent.trim() : 'Item',
            unit: unit ? unit.textContent.trim() : '',
            qty: qty ? qty.getAttribute('value') : '',
            remove: remove ? remove.getAttribute('href') : ''
          });
        }catch(e){}
      });

      var total = 0;
      try{
        var t = doc.querySelector('.js-cart-total');
        if (t) total = parseFloat(t.textContent || '0') || 0;
      }catch(e){}

      if (totalEl) totalEl.textContent = total.toFixed(2);

      if (!items.length) {
        if (content) content.innerHTML = '<div class="alert alert-info mb-0">Your cart is empty.</div>';
        return;
      }

      var out = '';
      items.slice(0, 6).forEach(function(it){
        out +=
          '<div class="mini-cart-item">' +
            '<img src="' + escapeHtml(it.img) + '" alt="">' +
            '<div class="flex-grow-1">' +
              '<div class="mini-cart-item__title">' + escapeHtml(it.title) + '</div>' +
              '<div class="mini-cart-item__meta">$' + escapeHtml(it.unit) + ' · Qty: ' + escapeHtml(it.qty) + '</div>' +
            '</div>' +
            (it.remove ? ('<a class="btn btn-outline-danger btn-sm" href="' + escapeHtml(it.remove) + '" aria-label="Remove">&times;</a>') : '') +
          '</div>';
      });

      if (items.length > 6) {
        out += '<div class="text-muted small mt-2">+' + (items.length - 6) + ' more item(s)…</div>';
      }
      if (content) content.innerHTML = out;
    }

    async function load(){
      var now = Date.now();
      // simple cache to avoid hammering server
      if (last.html && (now - last.at) < 15000) {
        renderFromCartPage(last.html);
        return;
      }

      setLoading(true);
      try{
        var resp = await fetch(link.getAttribute('href'), { credentials: 'same-origin' });
        if (!resp.ok) throw new Error('HTTP ' + resp.status);
        var html = await resp.text();
        last = { at: now, html: html };
        renderFromCartPage(html);
        setLoading(false);
      }catch(e){
        setLoading(false);
        // fallback: navigate
        try{ showToast('Could not load cart preview. Opening cart…', 'warning'); }catch(_e){}
        window.location.href = link.getAttribute('href');
      }
    }

    link.addEventListener('click', function(e){
      // if bootstrap isn't there, let link work normally
      if (typeof bootstrap === 'undefined' || !bootstrap.Offcanvas) return;
      e.preventDefault();
      try{
        var off = bootstrap.Offcanvas.getOrCreateInstance(offEl);
        off.show();
        load();
      }catch(err){
        // fallback: link navigation
        window.location.href = link.getAttribute('href');
      }
    });

    // refresh when something likely changed (after AJAX addToCart)
    // We listen for a custom event fired by addToCart
    document.addEventListener('cart:changed', function(){ last = { at: 0, html: '' }; });
  }

  // Expose a safe global addToCart() for pages that call it inline.
  // Keeps the same signature as before, but upgrades UX to toasts.
  window.addToCart = function addToCart(productId, quantity){
    quantity = quantity || 1;
    if (typeof window.jQuery === 'undefined') {
      showToast('jQuery is missing, cannot add to cart.', 'danger');
      return;
    }
    try{
      window.jQuery.ajax({
        url: (window.SITE_URL ? window.SITE_URL : '') + '/cart/add/' + productId,
        method: 'POST',
        data: { quantity: quantity },
        success: function(resp){
          // Attempt to update badge if response contains count
          try{
            if (resp && typeof resp === 'string') resp = JSON.parse(resp);
          }catch(e){}
          if (resp && typeof resp.count !== 'undefined' && window.updateCartCount) {
            window.updateCartCount(resp.count);
          }
          showToast('Added to cart.', 'success');
          try{ document.dispatchEvent(new CustomEvent('cart:changed')); }catch(e){}
        },
        error: function(){
          showToast('Failed to add product to cart.', 'danger');
        }
      });
    }catch(e){
      showToast('Failed to add product to cart.', 'danger');
    }
  };

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
    initNavbarBehaviors();
    initBackToTop();
    initImageSkeletons();
    initQuickView();
    initProductsToolbar();
    initAddToCartButtons();
    initMiniCart();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
