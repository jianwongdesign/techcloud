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

// Load motion only near the viewport. User pause and accessibility preferences win.
const reducedMotion = matchMedia('(prefers-reduced-motion: reduce)');
const saveData = navigator.connection?.saveData;
document.querySelectorAll('.service-media').forEach(container => {
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
  new IntersectionObserver(entries => { visible = entries[0].isIntersecting; sync(); }, { threshold: 0.15 }).observe(container);
  reducedMotion.addEventListener('change', () => { userStarted = false; sync(); });
  document.addEventListener('visibilitychange', sync);
});
