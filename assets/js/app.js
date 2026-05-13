(() => {
  const showToasts = () => {
    document.querySelectorAll('.toast').forEach((t, i) => {
      setTimeout(() => t.style.opacity = '.98', 10);
      setTimeout(() => t.remove(), 4500 + i * 500);
    });
  };

  const animateCounters = () => {
    document.querySelectorAll('[data-count]').forEach((el) => {
      const target = Number(el.dataset.count || 0);
      let value = 0;
      const step = Math.max(1, Math.ceil(target / 40));
      const timer = setInterval(() => {
        value = Math.min(target, value + step);
        el.textContent = Number.isInteger(target) ? value : value.toFixed(2);
        if (value >= target) clearInterval(timer);
      }, 25);
    });
  };

  const copyButtons = () => {
    document.querySelectorAll('[data-copy]').forEach((btn) => {
      btn.addEventListener('click', async () => {
        const text = btn.getAttribute('data-copy') || '';
        try {
          await navigator.clipboard.writeText(text);
          btn.textContent = 'Copied';
          setTimeout(() => btn.textContent = 'Copy', 1500);
        } catch (_) {
          alert('Copy failed');
        }
      });
    });
  };

  const searchFilter = () => {
    document.querySelectorAll('[data-filter-target]').forEach((input) => {
      const selector = input.getAttribute('data-filter-target');
      if (!selector) return;
      input.addEventListener('input', () => {
        const q = input.value.toLowerCase().trim();
        document.querySelectorAll(selector).forEach((item) => {
          item.style.display = item.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
      });
    });
  };

  const navActive = () => {
    const path = window.location.pathname;
    document.querySelectorAll('.bottom-nav a').forEach((a) => {
      if (path === a.getAttribute('href')) a.classList.add('active');
    });
  };

  showToasts();
  animateCounters();
  copyButtons();
  searchFilter();
  navActive();
})();
