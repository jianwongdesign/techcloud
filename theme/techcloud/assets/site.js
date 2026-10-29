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
  const select = index => {
    choices.forEach((choice, i) => {
      choice.setAttribute('aria-selected', String(i === index));
      choice.tabIndex = i === index ? 0 : -1;
      panels[i].hidden = i !== index;
    });
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
});

// Load motion only near the viewport. User pause and accessibility preferences win.
const reducedMotion = matchMedia('(prefers-reduced-motion: reduce)');
const saveData = navigator.connection?.saveData;
document.querySelectorAll('.motion-media').forEach(container => {
  const video = container.querySelector('video');
  const button = container.querySelector('.motion-control');
  if (!video || !button) return;
  let visible = false;
  let userPaused = false;
  let userStarted = false;
  const label = video.getAttribute('aria-label').replace(/^Illustrative /, '').replace(/ video$/, '');
  button.hidden = false;
  const update = () => {
    const playing = !video.paused;
    button.textContent = playing ? 'Pause video' : 'Play video';
    button.setAttribute('aria-label', `${playing ? 'Pause' : 'Play'} ${label} video`);
  };
  const play = () => {
    if (!video.src) video.src = video.dataset.src;
    video.muted = true;
    video.play().catch(update);
  };
  const sync = () => {
    if (visible && !document.hidden && !userPaused && (userStarted || (!reducedMotion.matches && !saveData))) play();
    else video.pause();
  };
  button.addEventListener('click', () => {
    if (video.paused) { userStarted = true; userPaused = false; play(); }
    else { userPaused = true; userStarted = false; video.pause(); }
  });
  video.addEventListener('play', update);
  video.addEventListener('pause', update);
  video.addEventListener('error', () => { button.hidden = true; });
  new IntersectionObserver(entries => { visible = entries[0].isIntersecting; sync(); }, { threshold: 0.15 }).observe(video);
  reducedMotion.addEventListener('change', () => { userStarted = false; sync(); });
  document.addEventListener('visibilitychange', sync);
});
