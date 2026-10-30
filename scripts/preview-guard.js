// Casual copy deterrents for the shared review export only.
// Form editing, navigation, keyboard access and assistive reading remain available.
(() => {
  const editable = target => target instanceof Element && target.closest('input, textarea, select, [contenteditable="true"]');
  for (const type of ['copy', 'cut', 'contextmenu', 'dragstart', 'selectstart']) {
    document.addEventListener(type, event => {
      if (!editable(event.target)) event.preventDefault();
    });
  }
})();
