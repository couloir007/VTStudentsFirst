/**
 * @file action-section.js
 *
 * Handles tab switching and copy-to-clipboard for the Advocacy Toolkit.
 * Rep lookup / legislator contact is handled by legislator-contact.js.
 */

(function () {
  'use strict';

  window.showTab = function (id) {
    document.querySelectorAll('.action-section__tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.action-section__tab-btn').forEach(b => b.classList.remove('active'));
    const panel = document.getElementById('tab-' + id);
    if (panel) panel.classList.add('active');
    const btn = document.querySelector(`.action-section__tab-btn[data-tab="${id}"]`);
    if (btn) btn.classList.add('active');
  };

  window.copyText = function (id) {
    const el = document.getElementById(id);
    if (!el) return;
    navigator.clipboard.writeText(el.innerText).then(() => {
      const btn = el.parentElement.querySelector('.action-section__copy-btn');
      if (!btn) return;
      btn.textContent = '✓ Copied!';
      btn.classList.add('copied');
      setTimeout(() => {
        btn.textContent = 'Copy Text';
        btn.classList.remove('copied');
      }, 2000);
    });
  };

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.action-section__tab-btn').forEach(btn => {
      btn.addEventListener('click', () => showTab(btn.dataset.tab));
    });
  });

})();
