import assert from 'node:assert/strict';
import fs from 'node:fs';
import vm from 'node:vm';
const element = () => {
  const classes = new Set();
  return { handlers: {}, attrs: {}, hidden: true,
    classList: { toggle(c, on) { on ? classes.add(c) : classes.delete(c); }, contains: c => classes.has(c) },
    setAttribute(k,v) { this.attrs[k] = v; }, getAttribute(k) { return this.attrs[k]; },
    addEventListener(k, fn) { this.handlers[k] = fn; }, focus() { this.focused = true; }
  };
};
const toggle = element(), nav = element(), top = element(), marker = element();
const handlers = {};
const desktop = { matches: false, addEventListener(k, fn) { this.change = fn; } };
let observe;
vm.runInNewContext(fs.readFileSync(new URL('../theme/techcloud/assets/site.js', import.meta.url), 'utf8'), {
  document: { querySelector: s => ({ '.menu-toggle': toggle, '.primary-nav': nav, '.back-to-top': top, '.scroll-top-marker': marker })[s],
    querySelectorAll: () => [], addEventListener(k,fn) { handlers[k] = fn; } },
  matchMedia: () => desktop, navigator: {},
  IntersectionObserver: class { constructor(fn) { observe = fn; } observe() {} }
});
assert.equal(nav.inert, true);
for (let i = 0; i < 9; i++) toggle.handlers.click();
assert.equal(nav.inert, false);
handlers.keydown({ key: 'Escape' });
assert.equal(nav.inert, true); assert.equal(toggle.focused, true);
desktop.matches = true; desktop.change();
assert.equal(nav.inert, false, 'Desktop links remain usable after closing the mobile menu');
desktop.matches = false; desktop.change();
assert.equal(nav.inert, true);
assert.equal(top.tabIndex, -1);
observe([{ isIntersecting: false, boundingClientRect: { top: -10 } }]);
assert.equal(top.tabIndex, 0); assert.equal(top.attrs['aria-hidden'], 'false');
observe([{ isIntersecting: true, boundingClientRect: { top: 0 } }]);
assert.equal(top.tabIndex, -1); assert.equal(top.attrs['aria-hidden'], 'true');
assert.equal(top.classList.contains('is-visible'), false);
console.log('PASS: interrupted menu toggles, Escape focus, breakpoint navigation and return-control focusability.');
