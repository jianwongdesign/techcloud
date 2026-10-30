// Inlined before page content by the exporter. This is a review limit, not authentication.
(() => {
  const deadline = Date.parse(__PREVIEW_EXPIRES_AT__);
  const markup = __PREVIEW_LOCK_MARKUP__;
  let timer;
  let locked = false;
  function lock() {
    if (!document.body) {
      document.documentElement.hidden = true;
      document.addEventListener('DOMContentLoaded', lock, { once: true });
      return;
    }
    if (locked) return;
    locked = true;
    clearTimeout(timer);
    document.querySelectorAll('video').forEach(video => video.pause());
    document.head.querySelectorAll('style, link[rel="stylesheet"]').forEach(node => node.remove());
    document.body.innerHTML = markup;
    document.body.removeAttribute('class');
    document.documentElement.hidden = false;
    document.title = 'Preview expired · TechCloud';
  }
  function check() {
    clearTimeout(timer);
    const remaining = deadline - Date.now();
    if (!Number.isFinite(remaining) || remaining <= 0) lock();
    else timer = setTimeout(check, Math.min(remaining, 60000));
  }
  check();
  window.addEventListener('pageshow', check);
  window.addEventListener('focus', check);
  document.addEventListener('visibilitychange', check);
})();
