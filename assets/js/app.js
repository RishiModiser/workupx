(() => {
  const showToasts = () => {
    document.querySelectorAll('.toast').forEach((toast, index) => {
      setTimeout(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
      }, 20 + index * 40);

      setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-4px)';
        setTimeout(() => toast.remove(), 220);
      }, 4600 + index * 450);
    });
  };

  const animateCounters = () => {
    document.querySelectorAll('[data-count]').forEach((el) => {
      const target = Number(el.dataset.count || 0);
      const isDecimal = !Number.isInteger(target);
      const step = Math.max(1, Math.ceil((target || 1) / 42));
      let current = 0;

      const timer = setInterval(() => {
        current = Math.min(target, current + step);
        el.textContent = isDecimal ? current.toFixed(2) : String(Math.round(current));
        if (current >= target) {
          el.textContent = isDecimal ? target.toFixed(2) : String(target);
          clearInterval(timer);
        }
      }, 24);
    });
  };

  const copyButtons = () => {
    document.querySelectorAll('[data-copy]').forEach((btn) => {
      btn.addEventListener('click', async () => {
        const text = btn.getAttribute('data-copy') || '';
        const prev = btn.textContent;
        try {
          await navigator.clipboard.writeText(text);
          btn.textContent = 'Copied';
          setTimeout(() => {
            btn.textContent = prev || 'Copy';
          }, 1500);
        } catch (error) {
          alert('Copy failed');
        }
      });
    });
  };

  const searchFilter = () => {
    document.querySelectorAll('[data-filter-target]').forEach((input) => {
      const selector = input.getAttribute('data-filter-target');
      if (!selector) {
        return;
      }

      input.addEventListener('input', () => {
        const query = input.value.toLowerCase().trim();
        document.querySelectorAll(selector).forEach((item) => {
          item.style.display = item.textContent.toLowerCase().includes(query) ? '' : 'none';
        });
      });
    });
  };

  const navActive = () => {
    const path = window.location.pathname;
    document.querySelectorAll('.bottom-nav a').forEach((a) => {
      if (path === a.getAttribute('href')) {
        a.classList.add('active');
      }
    });
  };

  const mobileNav = () => {
    const toggle = document.querySelector('[data-nav-toggle]');
    const menu = document.querySelector('[data-nav-menu]');
    if (!toggle || !menu) {
      return;
    }

    toggle.addEventListener('click', () => {
      const isOpen = menu.classList.toggle('open');
      toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    menu.querySelectorAll('a').forEach((link) => {
      link.addEventListener('click', () => {
        menu.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
      });
    });
  };

  const revealOnScroll = () => {
    const revealEls = document.querySelectorAll('.reveal');
    if (!revealEls.length) {
      return;
    }

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, {
      threshold: 0.15,
    });

    revealEls.forEach((el) => observer.observe(el));
  };

  const buttonRipple = () => {
    document.querySelectorAll('.btn').forEach((btn) => {
      btn.addEventListener('click', (event) => {
        const ripple = document.createElement('span');
        const rect = btn.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        ripple.style.position = 'absolute';
        ripple.style.width = `${size}px`;
        ripple.style.height = `${size}px`;
        ripple.style.left = `${event.clientX - rect.left - size / 2}px`;
        ripple.style.top = `${event.clientY - rect.top - size / 2}px`;
        ripple.style.borderRadius = '50%';
        ripple.style.background = 'rgba(255,255,255,.32)';
        ripple.style.transform = 'scale(0)';
        ripple.style.transition = 'transform .45s ease, opacity .45s ease';
        ripple.style.pointerEvents = 'none';

        btn.appendChild(ripple);
        requestAnimationFrame(() => {
          ripple.style.transform = 'scale(1)';
          ripple.style.opacity = '0';
        });
        setTimeout(() => ripple.remove(), 500);
      });
    });
  };

  const passwordToggle = () => {
    document.querySelectorAll('input[type="password"]').forEach((input) => {
      if (input.closest('.password-field')) {
        return;
      }

      const wrapper = document.createElement('div');
      wrapper.className = 'password-field';
      input.parentNode?.insertBefore(wrapper, input);
      wrapper.appendChild(input);

      const toggle = document.createElement('button');
      toggle.type = 'button';
      toggle.className = 'password-toggle';
      toggle.textContent = 'Show';
      toggle.setAttribute('aria-label', 'Toggle password visibility');
      wrapper.appendChild(toggle);

      toggle.addEventListener('click', () => {
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        toggle.textContent = show ? 'Hide' : 'Show';
      });
    });
  };

  const timeframeTabs = () => {
    document.querySelectorAll('[data-timeframe]').forEach((button) => {
      button.addEventListener('click', () => {
        const group = button.parentElement;
        if (!group) {
          return;
        }

        group.querySelectorAll('[data-timeframe]').forEach((tab) => tab.classList.remove('active'));
        button.classList.add('active');
      });
    });
  };

  const progressBars = () => {
    document.querySelectorAll('[data-progress]').forEach((el) => {
      const value = Number(el.getAttribute('data-progress') || 0);
      const bar = el.querySelector('span');
      if (!bar) {
        return;
      }

      bar.style.width = `${Math.max(0, Math.min(100, value))}%`;
    });
  };

  showToasts();
  animateCounters();
  copyButtons();
  searchFilter();
  navActive();
  mobileNav();
  revealOnScroll();
  buttonRipple();
  passwordToggle();
  timeframeTabs();
  progressBars();
})();
