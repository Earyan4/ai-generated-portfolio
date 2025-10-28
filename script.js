(function() {
  const header = document.querySelector('[data-header]');
  const navToggle = document.querySelector('[data-nav-toggle]');
  const primaryNav = document.getElementById('primary-nav');
  const lightbox = document.querySelector('[data-lightbox]');
  const lightboxContent = document.querySelector('[data-lightbox-content]');
  const lightboxCloseEls = document.querySelectorAll('[data-lightbox-close]');
  const portfolioGrid = document.querySelector('[data-portfolio-grid]');
  const filterButtons = Array.from(document.querySelectorAll('.filter'));
  const contactForm = document.querySelector('[data-contact-form]');
  const yearEl = document.querySelector('[data-year]');

  if (yearEl) {
    yearEl.textContent = new Date().getFullYear().toString();
  }

  // Sticky header visual cue
  const onScroll = () => {
    if (!header) return;
    const scrolled = window.scrollY > 10;
    header.style.boxShadow = scrolled ? '0 6px 20px rgba(0,0,0,.25)' : 'none';
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  // Mobile nav toggle
  if (navToggle && primaryNav) {
    navToggle.addEventListener('click', () => {
      const expanded = navToggle.getAttribute('aria-expanded') === 'true';
      navToggle.setAttribute('aria-expanded', String(!expanded));
      primaryNav.classList.toggle('open');
    });
    primaryNav.querySelectorAll('a').forEach(a => a.addEventListener('click', () => {
      navToggle.setAttribute('aria-expanded', 'false');
      primaryNav.classList.remove('open');
    }));
  }

  // Smooth scroll for same-page links
  document.querySelectorAll('a[href^="#"]').forEach(link => {
    link.addEventListener('click', (e) => {
      const targetId = link.getAttribute('href');
      if (!targetId || targetId === '#' || targetId.length === 1) return;
      const target = document.querySelector(targetId);
      if (!target) return;
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      history.replaceState(null, '', targetId);
    });
  });

  // Active nav on scroll via IntersectionObserver
  const navLinks = Array.from(document.querySelectorAll('.primary-nav a'));
  const sections = navLinks
    .map(a => document.querySelector(a.getAttribute('href')))
    .filter(Boolean);
  const io = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      const id = '#' + entry.target.id;
      const link = navLinks.find(a => a.getAttribute('href') === id);
      if (!link) return;
      if (entry.isIntersecting) {
        navLinks.forEach(a => a.classList.remove('is-active'));
        link.classList.add('is-active');
      }
    });
  }, { rootMargin: '-45% 0px -45% 0px', threshold: 0.01 });
  sections.forEach(sec => io.observe(sec));

  // Portfolio filtering
  function applyFilter(filter) {
    if (!portfolioGrid) return;
    const tiles = Array.from(portfolioGrid.querySelectorAll('.tile'));
    tiles.forEach(tile => {
      const cat = tile.getAttribute('data-category');
      const show = filter === 'all' || cat === filter;
      tile.style.display = show ? '' : 'none';
    });
  }
  filterButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      filterButtons.forEach(b => b.classList.remove('is-active'));
      btn.classList.add('is-active');
      const filter = btn.getAttribute('data-filter') || 'all';
      applyFilter(filter);
    });
  });

  // Lightbox logic
  let lastFocused = null;
  function openLightbox(contentEl) {
    lastFocused = document.activeElement;
    lightboxContent.innerHTML = '';
    lightboxContent.appendChild(contentEl);
    lightbox.hidden = false;
    trapFocus(lightbox);
  }
  function closeLightbox() {
    if (lightboxContent) lightboxContent.innerHTML = '';
    lightbox.hidden = true;
    if (lastFocused && lastFocused.focus) lastFocused.focus();
  }
  function makeIframe(src) {
    const iframe = document.createElement('iframe');
    iframe.src = src;
    iframe.title = 'Embedded video';
    iframe.allow = 'autoplay; fullscreen; picture-in-picture';
    iframe.loading = 'lazy';
    return iframe;
  }

  // Openers (hero button and tiles)
  document.querySelectorAll('[data-lightbox-video]').forEach(btn => {
    btn.addEventListener('click', () => {
      const src = btn.getAttribute('data-lightbox-video');
      if (!src) return;
      openLightbox(makeIframe(src));
    });
  });

  // Close interactions
  lightboxCloseEls.forEach(el => el.addEventListener('click', closeLightbox));
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !lightbox.hidden) closeLightbox();
  });
  lightbox.addEventListener('click', (e) => {
    if (e.target && (e.target.closest('[data-lightbox-close]') || e.target === lightbox.querySelector('.lightbox-backdrop'))) {
      closeLightbox();
    }
  });

  // Focus trap for dialog
  function trapFocus(container) {
    const focusable = container.querySelectorAll('a, button, textarea, input, select, [tabindex]:not([tabindex="-1"])');
    const first = focusable[0];
    const last = focusable[focusable.length - 1];
    if (first) first.focus();
    function handle(e) {
      if (e.key !== 'Tab') return;
      if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    }
    container.addEventListener('keydown', handle);
  }

  // Contact form validation + mailto compose
  if (contactForm) {
    contactForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const status = contactForm.querySelector('.form-status');
      const name = contactForm.name.value.trim();
      const email = contactForm.email.value.trim();
      const budget = contactForm.budget.value;
      const message = contactForm.message.value.trim();
      if (!name || !email || !message || !budget) {
        if (status) status.textContent = 'Please fill in all fields.';
        return;
      }
      const emailOk = /.+@.+\..+/.test(email);
      if (!emailOk) {
        if (status) status.textContent = 'Please enter a valid email address.';
        return;
      }
      const body = encodeURIComponent(
        `Name: ${name}\nEmail: ${email}\nBudget: ${budget}\n\n${message}`
      );
      const subject = encodeURIComponent(`Project Inquiry — ${name}`);
      const mailto = `mailto:studio@yourdomain.com?subject=${subject}&body=${body}`;
      window.location.href = mailto;
      if (status) status.textContent = 'Opening your email client…';
      contactForm.reset();
    });
  }
})();
