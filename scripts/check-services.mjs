// Check the service selector's public interaction and keyboard contract.
import assert from 'node:assert/strict';
import fs from 'node:fs';
import vm from 'node:vm';

function element(id) {
  return { id, attrs: {}, handlers: {}, hidden: false,
    setAttribute(name, value) { this.attrs[name] = value; },
    addEventListener(name, callback) { this.handlers[name] = callback; },
    focus() { focused = this.id; },
  };
}
let focused;
const menu = element('menu');
const choices = Array.from({ length: 5 }, (_, i) => element(`choice-${i}`));
const panels = Array.from({ length: 5 }, (_, i) => element(`panel-${i}`));
const showcase = {
  querySelector: () => menu,
  querySelectorAll: selector => selector === '[data-service-choice]' ? choices : panels,
};
vm.runInNewContext(fs.readFileSync(new URL('../theme/techcloud/assets/site.js', import.meta.url), 'utf8'), {
  document: { querySelector: () => null, querySelectorAll: selector => selector === '[data-service-showcase]' ? [showcase] : [] },
  matchMedia: () => ({ matches: false }), navigator: {},
});
function selected(index) {
  assert.equal(panels.filter(panel => !panel.hidden).length, 1);
  assert.equal(panels[index].hidden, false);
  assert.equal(choices.filter(choice => choice.tabIndex === 0).length, 1);
  assert.equal(choices[index].attrs['aria-selected'], 'true');
  assert.equal(choices[index].attrs['aria-controls'], panels[index].id);
  assert.equal(panels[index].attrs['aria-labelledby'], choices[index].id);
}
function key(index, value) {
  let prevented = false;
  choices[index].handlers.keydown({ key: value, preventDefault: () => { prevented = true; } });
  return prevented;
}
selected(0);
assert.equal(menu.attrs.role, 'tablist');
let prevented = false;
choices[2].handlers.click({ button: 0, preventDefault: () => { prevented = true; } });
assert.equal(prevented, true); selected(2);
choices[1].handlers.click({ metaKey: true, preventDefault: () => assert.fail('Modified clicks retain the original service link') });
selected(2);
assert.equal(key(2, 'End'), true); selected(4); assert.equal(focused, 'choice-4');
key(4, 'ArrowDown'); selected(0);
key(0, 'ArrowUp'); selected(4);
key(4, 'Home'); selected(0);
key(3, ' '); selected(3);
assert.equal(key(3, 'Tab'), false, 'Tab must leave the selector normally');
console.log('PASS: one active service, accessible tab relationships, click selection, modified links, keyboard navigation and normal Tab behavior.');
