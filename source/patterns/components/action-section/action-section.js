/**
 * @file action-section.js
 *
 * Representative lookup via Google Civic Information API.
 * Finds the user's VT House rep from a street address, then
 * pre-populates the legislator letter template with their name.
 *
 * Also handles tab switching and copy-to-clipboard for toolkit.
 *
 * Requires: data-civic-api-key attribute on .action-section element,
 * or window.CIVIC_API_KEY global set by the theme.
 */

(function () {
  'use strict';

  // ── Tab switching ──────────────────────────────────────────────────────────
  window.showTab = function (id) {
    document.querySelectorAll('.action-section__tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.action-section__tab-btn').forEach(b => b.classList.remove('active'));
    const panel = document.getElementById('tab-' + id);
    if (panel) panel.classList.add('active');
    const btn = document.querySelector(`[data-tab="${id}"]`);
    if (btn) btn.classList.add('active');
  };

  // ── Copy to clipboard ──────────────────────────────────────────────────────
  window.copyText = function (id) {
    const el = document.getElementById(id);
    if (!el) return;
    navigator.clipboard.writeText(el.innerText).then(() => {
      const btn = el.parentElement.querySelector('.action-section__copy-btn');
      if (!btn) return;
      btn.textContent = 'Copied!';
      btn.classList.add('copied');
      setTimeout(() => {
        btn.textContent = 'Copy Text';
        btn.classList.remove('copied');
      }, 2000);
    });
  };

  // ── Rep lookup ─────────────────────────────────────────────────────────────
  document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('rep-lookup-form');
    if (!form) return;

    const section    = form.closest('.action-section');
    const apiKey     = (section && section.dataset.civicApiKey) || window.CIVIC_API_KEY || '';
    const input      = document.getElementById('rep-lookup-address');
    const btn        = document.getElementById('rep-lookup-btn');
    const resultEl   = document.getElementById('rep-lookup-result');
    const errorEl    = document.getElementById('rep-lookup-error');

    // Tab buttons — wire up after DOM ready so showTab works
    document.querySelectorAll('.action-section__tab-btn').forEach(btn => {
      btn.addEventListener('click', () => showTab(btn.dataset.tab));
    });

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const address = input.value.trim();
      if (!address) return;

      // Reset state
      resultEl.hidden = true;
      errorEl.hidden  = true;
      btn.disabled    = true;
      btn.textContent = 'Looking up…';

      if (!apiKey) {
        showError('API key not configured. Please add your Google Civic API key.');
        btn.disabled    = false;
        btn.textContent = 'Find My Rep';
        return;
      }

      try {
        const url = new URL('https://www.googleapis.com/civicinfo/v2/representatives');
        url.searchParams.set('address', address + ', Vermont');
        url.searchParams.set('levels', 'administrativeArea1');
        url.searchParams.set('roles', 'legislatorLowerBody');
        url.searchParams.set('key', apiKey);

        const res  = await fetch(url);
        const data = await res.json();

        if (!res.ok || data.error) {
          throw new Error(data.error?.message || 'Address not found. Try including your street number and town.');
        }

        const officials = data.officials || [];
        const offices   = data.offices   || [];

        // Find VT House reps
        const reps = [];
        offices.forEach(office => {
          if (!office.levels?.includes('administrativeArea1')) return;
          if (!office.roles?.includes('legislatorLowerBody')) return;
          (office.officialIndices || []).forEach(i => {
            const o = officials[i];
            if (o) reps.push({ office: office.name, ...o });
          });
        });

        if (!reps.length) {
          throw new Error('No Vermont House representative found for that address. Try a more complete address including your town.');
        }

        renderResults(reps);
        updateLetterTemplates(reps);

      } catch (err) {
        showError(err.message || 'Something went wrong. Please try again.');
      } finally {
        btn.disabled    = false;
        btn.textContent = 'Find My Rep';
      }
    });

    function renderResults(reps) {
      const container = document.getElementById('rep-lookup-cards');
      container.innerHTML = '';

      reps.forEach(rep => {
        const phone   = rep.phones?.[0]   || null;
        const email   = rep.emails?.[0]   || null;
        const website = rep.urls?.[0]      || null;
        const photo   = rep.photoUrl       || null;

        const card = document.createElement('div');
        card.className = 'rep-card';
        card.innerHTML = `
          ${photo ? `<img class="rep-card__photo" src="${escHtml(photo)}" alt="${escHtml(rep.name)}" loading="lazy">` : '<div class="rep-card__photo rep-card__photo--placeholder">👤</div>'}
          <div class="rep-card__info">
            <p class="rep-card__office">${escHtml(rep.office)}</p>
            <h4 class="rep-card__name">${escHtml(rep.name)}</h4>
            ${rep.party ? `<p class="rep-card__party">${escHtml(rep.party)}</p>` : ''}
            <div class="rep-card__contacts">
              ${phone   ? `<a class="rep-card__contact" href="tel:${escHtml(phone)}">📞 ${escHtml(phone)}</a>` : ''}
              ${email   ? `<a class="rep-card__contact" href="mailto:${escHtml(email)}">✉️ ${escHtml(email)}</a>` : ''}
              ${website ? `<a class="rep-card__contact" href="${escHtml(website)}" target="_blank" rel="noopener">🔗 Official page</a>` : ''}
            </div>
          </div>
        `;
        container.appendChild(card);
      });

      resultEl.hidden = false;
      resultEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function updateLetterTemplates(reps) {
      // Pre-populate [Legislator Name] placeholder in the letter template
      const rep         = reps[0];
      const repName     = rep?.name || 'My Representative';
      const addressVal  = input.value.trim();
      // Extract town from address (last segment before VT/zipcode)
      const townMatch   = addressVal.match(/,?\s*([A-Za-z\s]+?)(?:,?\s*VT|\s*\d{5}|$)/i);
      const town        = townMatch?.[1]?.trim() || 'my town';

      // Update the legislator letter template text
      const legTemplate = document.getElementById('text-leg-template');
      if (legTemplate) {
        legTemplate.innerHTML = legTemplate.innerHTML
          .replace(/Dear\s+\[Legislator\]/gi, `Dear ${escHtml(repName)}`)
          .replace(/\[Your Town\]/gi, escHtml(capitalizeWords(town)))
          .replace(/\[Town\]/gi, escHtml(capitalizeWords(town)));
      }

      // Show a "Jump to toolkit" prompt
      const prompt = document.getElementById('rep-lookup-toolkit-prompt');
      if (prompt) {
        prompt.hidden = false;
        prompt.querySelector('.rep-lookup__rep-name').textContent = repName;
      }
    }

    function showError(msg) {
      errorEl.textContent = msg;
      errorEl.hidden = false;
    }

    function escHtml(str) {
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
    }

    function capitalizeWords(str) {
      return str.replace(/\b\w/g, c => c.toUpperCase());
    }
  });

})();
