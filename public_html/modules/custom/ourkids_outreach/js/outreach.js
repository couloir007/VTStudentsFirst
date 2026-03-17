/**
 * @file
 * Legislator outreach admin UI.
 */

(function (drupalSettings) {
  'use strict';

  const STORAGE_KEY = 'ourkids_outreach_sent';
  const settings    = drupalSettings.ourkidsOutreach || {};

  function getSent() {
    try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'); }
    catch { return []; }
  }

  function saveSent(sent) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(sent));
  }

  function markSent(ref) {
    const sent = getSent();
    if (!sent.includes(ref)) { sent.push(ref); saveSent(sent); }
  }

  function updateUI() {
    const sent = getSent();
    document.querySelectorAll('.ourkids-outreach__row').forEach((row) => {
      const isSent  = sent.includes(row.dataset.ref);
      const btn     = row.querySelector('.ourkids-outreach__preview-btn');
      const badge   = row.querySelector('.ourkids-outreach__sent-badge');
      row.classList.toggle('is-sent', isSent);
      if (btn)   btn.hidden   = isSent;
      if (badge) badge.hidden = !isSent;
    });
    const countEl = document.getElementById('outreach-sent-count');
    if (countEl) countEl.textContent = sent.length;
  }

  // ── Modal state ────────────────────────────────────────────────────────────
  let currentRef = null;

  function openModal(ref) {
    currentRef = ref;
    const modal = document.getElementById('outreach-modal');

    document.getElementById('modal-to').textContent      = '…';
    document.getElementById('modal-subject').textContent = '…';
    document.getElementById('modal-preview').srcdoc      = '<p style="padding:1rem;font-family:sans-serif;">Loading…</p>';
    document.getElementById('outreach-send-success').hidden = true;
    document.getElementById('outreach-send-error').hidden   = true;
    document.getElementById('outreach-send-btn').hidden     = false;
    document.getElementById('outreach-modal-cancel').hidden = false;

    modal.hidden = false;
    document.body.style.overflow = 'hidden';

    fetch(`${settings.previewUrl}?ref=${encodeURIComponent(ref)}`)
      .then((r) => r.json())
      .then((data) => {
        if (data.error) throw new Error(data.error);
        document.getElementById('modal-to').textContent      = `${data.name} <${data.email}>`;
        document.getElementById('modal-subject').textContent = data.subject;
        document.getElementById('modal-preview').srcdoc      = data.body;
      })
      .catch((err) => {
        document.getElementById('modal-preview').srcdoc =
          `<p style="padding:1rem;color:red;">Error: ${err.message}</p>`;
      });
  }

  function closeModal() {
    document.getElementById('outreach-modal').hidden = true;
    document.body.style.overflow = '';
    currentRef = null;
  }

  function sendEmail() {
    if (!currentRef) return;

    const sendBtn    = document.getElementById('outreach-send-btn');
    const cancelBtn  = document.getElementById('outreach-modal-cancel');
    const sending    = document.getElementById('outreach-sending');
    const success    = document.getElementById('outreach-send-success');
    const errorEl    = document.getElementById('outreach-send-error');

    sendBtn.hidden   = true;
    cancelBtn.hidden = true;
    sending.hidden   = false;

    const token = drupalSettings.path?.currentPathIsAdmin
      ? document.querySelector('meta[name="csrf-token"]')?.content || ''
      : '';

    fetch(settings.sendUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-CSRF-Token': token,
      },
      body: `ref=${encodeURIComponent(currentRef)}`,
    })
      .then((r) => r.json())
      .then((data) => {
        sending.hidden = true;
        if (data.success) {
          success.hidden = false;
          markSent(currentRef);
          updateUI();
          setTimeout(closeModal, 1500);
        } else {
          errorEl.textContent = data.error || 'Send failed.';
          errorEl.hidden = false;
          sendBtn.hidden = false;
          cancelBtn.hidden = false;
        }
      })
      .catch((err) => {
        sending.hidden = true;
        errorEl.textContent = err.message;
        errorEl.hidden = false;
        sendBtn.hidden = false;
        cancelBtn.hidden = false;
      });
  }

  // ── Init ───────────────────────────────────────────────────────────────────
  document.addEventListener('DOMContentLoaded', () => {

    // Tabs.
    document.querySelectorAll('.ourkids-outreach__tab').forEach((tab) => {
      tab.addEventListener('click', () => {
        const target = tab.dataset.tab;
        document.querySelectorAll('.ourkids-outreach__tab').forEach((t) => {
          t.classList.toggle('is-active', t === tab);
        });
        document.querySelectorAll('.ourkids-outreach__panel').forEach((panel) => {
          panel.classList.toggle('is-active', panel.id === `outreach-panel-${target}`);
        });
      });
    });

    // Preview buttons.
    document.querySelectorAll('.ourkids-outreach__preview-btn').forEach((btn) => {
      btn.addEventListener('click', () => openModal(btn.dataset.ref));
    });

    // Modal close.
    document.getElementById('outreach-modal-close')?.addEventListener('click', closeModal);
    document.getElementById('outreach-modal-cancel')?.addEventListener('click', closeModal);
    document.querySelector('.ourkids-modal__backdrop')?.addEventListener('click', closeModal);

    // Send.
    document.getElementById('outreach-send-btn')?.addEventListener('click', sendEmail);

    // Escape key.
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeModal();
    });

    // Reset.
    document.getElementById('outreach-reset')?.addEventListener('click', () => {
      if (confirm('Reset all progress? This cannot be undone.')) {
        saveSent([]);
        updateUI();
      }
    });

    updateUI();
  });

})(drupalSettings);
