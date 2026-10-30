const toggle = document.querySelector('.menu-toggle');
const nav = document.querySelector('.primary-nav');
if (toggle && nav) {
  const close = () => { toggle.setAttribute('aria-expanded', 'false'); nav.classList.remove('is-open'); };
  toggle.addEventListener('click', () => {
    const open = toggle.getAttribute('aria-expanded') !== 'true';
    toggle.setAttribute('aria-expanded', String(open));
    nav.classList.toggle('is-open', open);
  });
  nav.addEventListener('click', event => { if (event.target.closest('a')) close(); });
  document.addEventListener('keydown', event => { if (event.key === 'Escape' && nav.classList.contains('is-open')) { close(); toggle.focus(); } });
  document.addEventListener('click', event => { if (!event.target.closest('.site-header')) close(); });
  matchMedia('(min-width: 761px)').addEventListener('change', close);
}

// The public design preview has no backend. Never imply that it sent a message.
document.querySelectorAll('[data-preview-form]').forEach(form => {
  form.addEventListener('submit', event => {
    event.preventDefault();
    if (!form.reportValidity()) return;
    const status = form.querySelector('.form-status');
    status.classList.remove('is-error', 'is-success');
    status.textContent = 'Preview complete. No message was sent. You can contact TechCloud at ';
    const email = document.createElement('a');
    email.href = `mailto:${form.dataset.contactEmail}`;
    email.textContent = form.dataset.contactEmail;
    status.append(email, '.');
    status.hidden = false;
    status.focus();
  });
});

// Enhance real service links into keyboard-accessible tabs. Without JavaScript,
// the menu remains a direct route to every service's complete detail page.
document.querySelectorAll('[data-service-showcase]').forEach(showcase => {
  const menu = showcase.querySelector('.service-menu');
  const choices = [...showcase.querySelectorAll('[data-service-choice]')];
  const panels = [...showcase.querySelectorAll('[data-service-panel]')];
  if (!menu || !choices.length || choices.length !== panels.length) return;
  menu.setAttribute('role', 'tablist');
  menu.setAttribute('aria-orientation', 'vertical');
  // Overlapping grid cells reserve the tallest panel before selection changes.
  // Inactive panels stay out of the accessibility tree and keyboard order.
  showcase.classList.add('has-service-tabs');
  const select = index => {
    choices.forEach((choice, i) => {
      choice.setAttribute('aria-selected', String(i === index));
      choice.tabIndex = i === index ? 0 : -1;
      panels[i].hidden = i !== index;
      panels[i].inert = i !== index;
      panels[i].setAttribute('aria-hidden', String(i !== index));
    });
    const image = panels[index].querySelector('img');
    if (image) image.loading = 'eager';
  };
  choices.forEach((choice, index) => {
    choice.setAttribute('role', 'tab');
    choice.setAttribute('aria-controls', panels[index].id);
    panels[index].setAttribute('role', 'tabpanel');
    panels[index].setAttribute('aria-labelledby', choice.id);
    choice.addEventListener('click', event => {
      if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || event.button > 0) return;
      event.preventDefault();
      select(index);
    });
    choice.addEventListener('keydown', event => {
      let next;
      if (event.key === 'ArrowDown') next = (index + 1) % choices.length;
      else if (event.key === 'ArrowUp') next = (index - 1 + choices.length) % choices.length;
      else if (event.key === 'Home') next = 0;
      else if (event.key === 'End') next = choices.length - 1;
      else if (event.key === ' ') next = index;
      else return;
      event.preventDefault();
      select(next);
      choices[next].focus();
    });
  });
  select(0);
  // Warm nearby stills once, so switching services does not reveal an empty image.
  // Data saver users download only the service they select.
  if (!navigator.connection?.saveData) {
    const preload = new IntersectionObserver(entries => {
      if (!entries.some(entry => entry.isIntersecting)) return;
      panels.forEach(panel => {
        const image = panel.querySelector('img');
        if (image) image.loading = 'eager';
      });
      preload.disconnect();
    }, { rootMargin: '320px 0px' });
    preload.observe(showcase);
  }
});

// Play background motion only while visible and allowed by visitor preferences.
const reducedMotion = matchMedia('(prefers-reduced-motion: reduce)');
const saveData = navigator.connection?.saveData;
document.querySelectorAll('.motion-media').forEach(container => {
  const video = container.querySelector('video');
  if (!video) return;
  let visible = false;
  const play = () => {
    if (!video.paused) return;
    if (!video.src) video.src = video.dataset.src;
    video.muted = true;
    video.play().catch(() => {});
  };
  const sync = () => {
    if (visible && !document.hidden && !reducedMotion.matches && !saveData) play();
    else if (!video.paused) video.pause();
  };
  new IntersectionObserver(entries => { visible = entries[0].isIntersecting; sync(); }, { threshold: 0.15 }).observe(video);
  reducedMotion.addEventListener('change', sync);
  document.addEventListener('visibilitychange', sync);
});
