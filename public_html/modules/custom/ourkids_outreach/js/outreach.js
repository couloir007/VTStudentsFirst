/**
 * @file
 * Legislator outreach admin UI.
 * Handles tab switching and localStorage progress tracking.
 */

(function () {
  'use strict';

  const STORAGE_KEY = 'ourkids_outreach_sent';

  function getSent() {
    try {
      return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
    } catch {
      return [];
    }
  }

  function saveSent(sent) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(sent));
  }

  function markSent(ref) {
    const sent = getSent();
    if (!sent.includes(ref)) {
      sent.push(ref);
      saveSent(sent);
    }
  }

  function updateUI() {
    const sent = getSent();

    // Update all rows.
    document.querySelectorAll('.ourkids-outreach__row').forEach((row) => {
      const ref = row.dataset.ref;
      const isSent = sent.includes(ref);
      const markBtn = row.querySelector('.ourkids-outreach__mark');
      const badge = row.querySelector('.ourkids-outreach__sent-badge');
      row.classList.toggle('is-sent', isSent);
      if (markBtn) markBtn.hidden = isSent;
      if (badge) badge.hidden = !isSent;
    });

    // Update sent count.
    const countEl = document.getElementById('outreach-sent-count');
    if (countEl) countEl.textContent = sent.length;
  }

  document.addEventListener('DOMContentLoaded', () => {
    // ── Tab switching ──────────────────────────────────────────────────────
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

    // ── Mark sent buttons ──────────────────────────────────────────────────
    document.querySelectorAll('.ourkids-outreach__mark').forEach((btn) => {
      btn.addEventListener('click', () => {
        markSent(btn.dataset.ref);
        updateUI();
      });
    });

    // ── Auto-mark on mailto click ──────────────────────────────────────────
    document.querySelectorAll('.ourkids-outreach__mailto').forEach((link) => {
      link.addEventListener('click', () => {
        setTimeout(() => {
          markSent(link.dataset.ref);
          updateUI();
        }, 1000);
      });
    });

    // ── Reset ──────────────────────────────────────────────────────────────
    const resetBtn = document.getElementById('outreach-reset');
    if (resetBtn) {
      resetBtn.addEventListener('click', () => {
        if (confirm('Reset all progress? This cannot be undone.')) {
          saveSent([]);
          updateUI();
        }
      });
    }

    // ── Init ───────────────────────────────────────────────────────────────
    updateUI();
  });

})();
