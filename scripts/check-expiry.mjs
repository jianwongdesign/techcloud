import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import vm from 'node:vm';
const html = readFileSync(new URL('../preview/index.html', import.meta.url), 'utf8');
const code = html.match(/<script>([\s\S]*?)<\/script>/)[1];
const deadline = Date.parse(JSON.parse(readFileSync(new URL('../preview-policy.json', import.meta.url))).expires_at);
function harness(now, hasBody = true) {
  const handlers = {};
  let timer, paused = false;
  const body = {innerHTML: 'original content', removeAttribute() {}};
  const document = {
    body: hasBody ? body : null, documentElement: {hidden: false}, title: 'TechCloud',
    head: {querySelectorAll: () => []},
    querySelectorAll: () => [{pause() {paused = true;}}],
    addEventListener(name, fn) { handlers[name] = fn; },
  };
  const clock = {now};
  vm.runInNewContext(code, {document, window: {addEventListener(name, fn) {handlers[name] = fn;}},
    Date: {parse: Date.parse, now: () => clock.now}, Number,
    setTimeout(fn, ms) {timer = {fn, ms}; return 1;}, clearTimeout() {timer = null;}});
  return {document, body, handlers, clock, tick: () => timer.fn(), timer: () => timer, paused: () => paused};
}
const open = harness(deadline - 500);
assert.equal(open.body.innerHTML, 'original content');
assert.equal(open.timer().ms, 500);
open.clock.now = deadline;
open.tick();
assert.match(open.body.innerHTML, /This preview has expired/);
assert.ok(open.paused());
const fresh = harness(deadline, false);
assert.equal(fresh.document.documentElement.hidden, true);
fresh.document.body = fresh.body;
fresh.handlers.DOMContentLoaded();
assert.match(fresh.body.innerHTML, /This preview has expired/);
assert.equal(fresh.document.documentElement.hidden, false);
for (const event of ['focus', 'pageshow', 'visibilitychange']) {
 const tab = harness(deadline - 10000);
 tab.clock.now = deadline + 10000;
 tab.handlers[event]();
 assert.match(tab.body.innerHTML, /This preview has expired/);
}
console.log('PASS: open-tab deadline, expired first visit, paused video, and returning-tab expiry.');
