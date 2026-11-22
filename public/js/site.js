/* Wear Plates Synergy VE India – site interactions */
(function () {
  'use strict';

  document.documentElement.classList.remove('no-js');

  // Sticky header shadow + back-to-top visibility
  var header = document.querySelector('.site-header');
  var toTop = document.querySelector('.back-to-top');
  function onScroll() {
    var y = window.scrollY || window.pageYOffset;
    if (header) header.classList.toggle('is-scrolled', y > 20);
    if (toTop) toTop.classList.toggle('show', y > 600);
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
  if (toTop) toTop.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });

  // Reveal-on-scroll
  var revealEls = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) { entry.target.classList.add('is-visible'); io.unobserve(entry.target); }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    revealEls.forEach(function (el) { io.observe(el); });
  } else {
    revealEls.forEach(function (el) { el.classList.add('is-visible'); });
  }

  // Count-up numbers (Indian digit grouping, e.g. 11,000)
  function fmt(n) { try { return n.toLocaleString('en-IN'); } catch (e) { return String(n); } }
  var counters = document.querySelectorAll('[data-count]');
  if ('IntersectionObserver' in window && counters.length) {
    var co = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        var el = entry.target, target = parseInt(el.getAttribute('data-count'), 10) || 0;
        var from = parseInt(el.getAttribute('data-from'), 10) || 0;
        var delay = parseInt(el.getAttribute('data-delay'), 10) || 0;
        var suffix = el.getAttribute('data-suffix') || '';
        var start = null, duration = 1600;
        co.unobserve(el);
        if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) { el.textContent = fmt(target) + suffix; return; }
        el.textContent = fmt(from) + suffix;
        function step(ts) {
          if (!start) start = ts;
          var p = Math.min((ts - start) / duration, 1);
          el.textContent = fmt(Math.round(from + (target - from) * (1 - Math.pow(1 - p, 4)))) + suffix;
          if (p < 1) requestAnimationFrame(step);
          else { el.classList.add('count-done'); }
        }
        setTimeout(function () { requestAnimationFrame(step); }, delay);
      });
    }, { threshold: 0.5 });
    counters.forEach(function (el) { co.observe(el); });
  }

  // Hero slider: keep the custom progress dots in sync with the active slide
  var hero = document.getElementById('heroCarousel');
  if (hero) {
    var dots = hero.querySelectorAll('.hero-dots [data-bs-slide-to]');
    hero.addEventListener('slide.bs.carousel', function (e) {
      dots.forEach(function (d, i) {
        var on = i === e.to;
        d.classList.toggle('active', on);
        if (on) d.setAttribute('aria-current', 'true'); else d.removeAttribute('aria-current');
      });
    });
  }

  // Close the mobile menu after choosing an in-page link
  document.querySelectorAll('#mainNav a[href*="#"]').forEach(function (a) {
    a.addEventListener('click', function () {
      var nav = document.getElementById('mainNav');
      if (nav && nav.classList.contains('show') && window.bootstrap) bootstrap.Collapse.getOrCreateInstance(nav).hide();
    });
  });

  // Bootstrap client-side validation
  document.querySelectorAll('form.needs-validation').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      if (!form.checkValidity()) { e.preventDefault(); e.stopPropagation(); }
      else {
        var btn = form.querySelector('[type="submit"]');
        if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending…'; }
      }
      form.classList.add('was-validated');
    });
  });

  // "Send via WhatsApp" – validate the enquiry form, then open WhatsApp (to the COMPANY number)
  // with a prefilled message the customer can review and send. Independent of "Submit Enquiry".
  document.querySelectorAll('[data-whatsapp-form]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var form = document.getElementById(btn.getAttribute('data-whatsapp-form'));
      var number = (btn.getAttribute('data-number') || '').replace(/\D+/g, '');
      var feedback = form ? form.querySelector('[data-whatsapp-feedback]') : null;
      if (!form || !number) return;

      // Trim values first so "   " does not pass the required check.
      ['name', 'company_name', 'mobile', 'email', 'subject', 'message'].forEach(function (n) {
        var f = form.elements[n];
        if (f && typeof f.value === 'string') f.value = f.value.trim();
      });

      // 1. Validate with the same rules as "Submit Enquiry" (required, pattern, email, min length).
      if (!form.checkValidity()) {
        form.classList.add('was-validated');
        var firstInvalid = form.querySelector(':invalid:not(fieldset)');
        if (firstInvalid) {
          firstInvalid.focus({ preventScroll: true });
          firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        if (feedback) {
          feedback.className = 'wa-feedback small is-error';
          feedback.innerHTML = '<i class="fa-solid fa-circle-exclamation me-1"></i>Please fill in Name, Mobile Number, Subject and Message correctly before sending on WhatsApp.';
        }
        return;
      }

      // 2. Build the message (WhatsApp shows *text* in bold).
      var val = function (n) { var f = form.elements[n]; return f ? f.value.trim() : ''; };
      var orNA = function (v) { return v || 'Not provided'; };
      var company = btn.getAttribute('data-company') || 'Wear Plates Synergy VE India';
      var product = btn.getAttribute('data-product');
      var lines = [
        '*New Enquiry – ' + company + '*',
        '',
        '*Name:* ' + val('name'),
        '*Company Name:* ' + orNA(val('company_name')),
        '*Email:* ' + orNA(val('email')),
        '*Phone Number:* ' + val('mobile')
      ];
      if (product) lines.push('*Product:* ' + product);
      lines.push('*Subject:* ' + val('subject'), '', '*Message:*', val('message'));

      // 3. Open WhatsApp Click to Chat (app on phones, WhatsApp Web on desktop) in a new tab.
      var url = 'https://wa.me/' + number + '?text=' + encodeURIComponent(lines.join('\n'));
      var win = window.open(url, '_blank');
      if (win) { try { win.opener = null; } catch (e) {} } // detach the new tab from this page
      else { window.location.href = url; }                  // popup blocked → open in the same tab

      if (feedback) {
        feedback.className = 'wa-feedback small is-ok';
        feedback.innerHTML = '<i class="fa-solid fa-circle-check me-1"></i>WhatsApp opened with your enquiry. Please review it and tap send.';
      }
    });
  });

  // India map: link pins and location cards
  document.querySelectorAll('[data-loc]').forEach(function (el) {
    var id = el.getAttribute('data-loc');
    var related = function () { return document.querySelectorAll('[data-loc="' + id + '"]'); };
    var on = function () { related().forEach(function (r) { r.classList.add('is-active'); }); };
    var off = function () { related().forEach(function (r) { r.classList.remove('is-active'); }); };
    el.addEventListener('mouseenter', on); el.addEventListener('mouseleave', off);
    el.addEventListener('focus', on); el.addEventListener('blur', off);
  });

  // Bootstrap tooltips (used on map pins)
  if (window.bootstrap) {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) { new bootstrap.Tooltip(el); });
  }

  // Auto-dismiss alerts
  document.querySelectorAll('.alert-dismissible[data-autohide]').forEach(function (a) {
    setTimeout(function () { if (window.bootstrap) bootstrap.Alert.getOrCreateInstance(a).close(); }, 8000);
  });
})();
