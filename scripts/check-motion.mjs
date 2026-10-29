// Behavioral checks for lazy media and user accessibility preferences.
import assert from 'node:assert/strict';
import fs from 'node:fs';
import vm from 'node:vm';
const source = fs.readFileSync(new URL('../theme/techcloud/assets/site.js', import.meta.url), 'utf8');
function setup({ reduced = false, saveData = false } = {}) {
  const handlers = {}, buttonHandlers = {}, documentHandlers = {};
  let intersect, preferenceChange;
  const video = {
    paused: true, src: '', playCalls: 0, dataset: { src: 'clip.mp4' },
    getAttribute: () => 'Illustrative Managed IT video',
    addEventListener: (name, fn) => { handlers[name] = fn; },
    play() { this.playCalls++; this.paused = false; handlers.play?.(); return Promise.resolve(); },
    pause() { this.paused = true; handlers.pause?.(); },
  };
  const button = { hidden: true, setAttribute() {}, addEventListener: (name, fn) => { buttonHandlers[name] = fn; } };
  const container = { querySelector: name => name === 'video' ? video : button };
  const media = { matches: reduced, addEventListener: (_, fn) => { preferenceChange = fn; } };
  const document = { hidden: false, querySelector: () => null, querySelectorAll: selector => selector === '.motion-media' ? [container] : [], addEventListener: (name, fn) => { documentHandlers[name] = fn; } };
  vm.runInNewContext(source, { document, navigator: { connection: { saveData } }, matchMedia: () => media,
    IntersectionObserver: class { constructor(fn) { intersect = fn; } observe(target) { assert.equal(target, video, 'Observe the actual video so a mobile hero does not start before its media is visible'); } },
  });
  return { video, button, document, enter: () => intersect([{ isIntersecting: true }]), leave: () => intersect([{ isIntersecting: false }]), click: () => buttonHandlers.click(),
    reduce: () => { media.matches = true; preferenceChange(); }, visibility: () => documentHandlers.visibilitychange() };
}
const normal = setup();
assert.equal(normal.video.src, '', 'Off-screen video must not download');
normal.enter(); assert.equal(normal.video.paused, false);
normal.enter(); normal.visibility();
assert.equal(normal.video.playCalls, 1, 'Repeated visibility events must not restart playing media');
normal.click(); normal.leave(); normal.enter(); assert.equal(normal.video.paused, true, 'Manual pause survives scrolling');
normal.click(); assert.equal(normal.video.paused, false);
normal.document.hidden = true; normal.visibility(); assert.equal(normal.video.paused, true);
normal.document.hidden = false; normal.visibility(); assert.equal(normal.video.paused, false);
normal.reduce(); assert.equal(normal.video.paused, true, 'New reduced-motion preference pauses playback');
for (const preference of [{ reduced: true }, { saveData: true }]) {
  const state = setup(preference); state.enter();
  assert.equal(state.video.src, '', 'Preference prevents automatic download');
  state.click(); assert.equal(state.video.paused, false, 'Explicit play remains available');
  state.leave(); assert.equal(state.video.paused, true, 'Off-screen playback pauses');
}
console.log('PASS: lazy loading, manual pause persistence, visibility, reduced motion, data saver and explicit playback.');
