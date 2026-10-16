document.addEventListener('DOMContentLoaded', () => {
  const header = document.querySelector('.site-header');
  const toggle = document.querySelector('[data-nav-toggle]');
  const nav = document.querySelector('[data-nav]');
  const themeButtons = document.querySelectorAll('[data-theme-toggle]');
  const langForms = document.querySelectorAll('.lang-switch');

  const setTheme = (theme) => {
    const nextTheme = theme === 'dark' ? 'dark' : 'light';
    document.documentElement.dataset.theme = nextTheme;
    localStorage.setItem('upskillsit-theme', nextTheme);
    themeButtons.forEach((button) => {
      button.setAttribute('aria-pressed', nextTheme === 'dark' ? 'true' : 'false');
    });
  };
  setTheme(localStorage.getItem('upskillsit-theme') || 'light');

  themeButtons.forEach((button) => {
    button.addEventListener('click', () => {
      setTheme(document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark');
    });
  });

  langForms.forEach((form) => {
    const select = form.querySelector('select[name="lang"]');
    if (select) select.addEventListener('change', () => form.submit());
  });

  const updateHeader = () => {
    if (header) header.classList.toggle('is-scrolled', window.scrollY > 8);
  };
  updateHeader();
  window.addEventListener('scroll', updateHeader, { passive: true });

  if (toggle && nav) {
    toggle.addEventListener('click', () => {
      const isOpen = nav.classList.toggle('open');
      toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
  }

  const revealItems = document.querySelectorAll('[data-reveal]');
  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12 });
    revealItems.forEach((item) => observer.observe(item));
  } else {
    revealItems.forEach((item) => item.classList.add('is-visible'));
  }

  document.querySelectorAll('[data-event-register]').forEach((button) => {
    button.addEventListener('click', async () => {
      const endpoint = button.getAttribute('data-event-register');
      const message = document.querySelector('[data-event-message]');
      let completed = false;
      button.disabled = true;
      button.textContent = button.dataset.labelRegistering || 'Inscription...';
      try {
        const response = await fetch(endpoint, {
          method: 'POST',
          headers: {
            'X-CSRF-Token': window.UPSKILLSIT_CSRF || '',
            'Accept': 'application/json'
          }
        });
        const data = await response.json();
        if (data.redirect) {
          window.location.href = data.redirect;
          return;
        }
        if (message) {
          message.textContent = data.message || message.dataset.labelResponse || 'Reponse recue.';
          message.style.color = data.ok ? '#3D7A28' : '#991B1B';
        }
        if (data.ok) {
          const doneLabel = data.status === 'waiting_list'
            ? (button.dataset.labelWaiting || "Liste d'attente")
            : (button.dataset.labelRegistered || 'Inscrit');
          button.textContent = `${doneLabel} - OK`;
          completed = true;
          return;
        }
      } catch (error) {
        if (message) message.textContent = message.dataset.labelError || "Impossible de finaliser l'inscription pour le moment.";
      } finally {
        if (!completed) {
          button.disabled = false;
          if (button.textContent === (button.dataset.labelRegistering || 'Inscription...')) {
            button.textContent = button.dataset.labelDefault || "S'inscrire";
          }
        }
      }
    });
  });
});
