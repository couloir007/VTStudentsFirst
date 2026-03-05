/**
 * @file legislator-contact.js
 *
 * Form-first legislator contact flow:
 *   1. User fills in name, email, town, role, message
 *   2. On submit, town is matched to VT House districts via the JSON data file
 *      or resolved via the Google Civic Information API if a key is provided
 *   3. mailto: link is opened with rep email(s) pre-addressed
 *   4. Confirmation view shown with copy fallback
 */

(function () {
  'use strict';

  // Town → district map (loaded from vt-house-districts-by-town.json if available)
  // Fallback: use Civic API with town name as address
  const DEFAULT_TEMPLATE = `My name is [Your Name] and I am a constituent from [Town]. I am writing to share my strong opposition to proposals that would eliminate supervisory unions and restrict Vermont's Town Tuition Program. I understand the need for education reform, but eliminating choice and access to independent schools does nothing to save money or improve quality. Please oppose any plan that restricts family choice or consolidates our district against our will.`;

  document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('legislator-contact');
    if (!root) return;

    const apiKey     = root.dataset.civicApiKey || window.CIVIC_API_KEY || '';
    const formView   = document.getElementById('lc-form');
    const confirmView = document.getElementById('lc-confirm');

    // Form fields
    const fnameInput   = document.getElementById('lc-fname');
    const lnameInput   = document.getElementById('lc-lname');
    const emailInput   = document.getElementById('lc-email');
    const townInput    = document.getElementById('lc-town');
    const roleInput    = document.getElementById('lc-role');
    const messageArea  = document.getElementById('lc-message');
    const submitBtn    = document.getElementById('lc-submit-btn');
    const formError    = document.getElementById('lc-form-error');

    // Confirmation elements
    const confirmReps  = document.getElementById('lc-confirm-reps');
    const confirmMsg   = document.getElementById('lc-confirm-msg');
    const copyBtn      = document.getElementById('lc-copy-btn');
    const restartBtn   = document.getElementById('lc-restart');

    // Auto-personalize message as user types name/town
    function personalizeMessage() {
      const name = [fnameInput.value.trim(), lnameInput.value.trim()].filter(Boolean).join(' ') || '[Your Name]';
      const town = townInput.value.trim() || '[Town]';
      let msg = messageArea.value;
      msg = msg.replace(/\[Your Name\]/g, name).replace(/\[Name\]/g, name);
      msg = msg.replace(/\[Town\]/g, town);
      messageArea.value = msg;
    }

    [fnameInput, lnameInput, townInput].forEach(el => {
      el.addEventListener('blur', personalizeMessage);
    });

    // ── Submit ─────────────────────────────────────────────────────────────
    submitBtn.addEventListener('click', async () => {
      formError.hidden = true;

      const fname = fnameInput.value.trim();
      const lname = lnameInput.value.trim();
      const town  = townInput.value.trim();
      const email = emailInput.value.trim();
      const msg   = messageArea.value.trim();

      if (!fname || !town || !msg) {
        formError.textContent = 'Please fill in your first name, town, and message before sending.';
        formError.hidden = false;
        formError.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        return;
      }

      submitBtn.disabled = true;
      submitBtn.textContent = 'Looking up your representatives…';

      try {
        const reps = await resolveReps(town, apiKey);
        showConfirmation(reps, msg, email);
      } catch (err) {
        formError.textContent = err.message || 'Could not find your representatives. Try entering your full address including street number.';
        formError.hidden = false;
        formError.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Send to My Legislators →';
      }
    });

    // ── Rep resolution ─────────────────────────────────────────────────────
    async function resolveReps(town, key) {
      // Storybook / no API key: return mock reps
      if (!key) {
        return [
          {
            name: 'Rep. Jane Smith',
            office: 'Vermont House — Caledonia-Washington',
            email: 'jsmith@leg.state.vt.us',
            phone: '(802) 555-0101',
          },
          {
            name: 'Sen. Robert Johnson',
            office: 'Vermont Senate — Caledonia District',
            email: 'rjohnson@leg.state.vt.us',
            phone: '(802) 555-0202',
          },
        ];
      }

      const url = new URL('https://www.googleapis.com/civicinfo/v2/representatives');
      url.searchParams.set('address', town + ', Vermont');
      url.searchParams.set('levels', 'administrativeArea1');
      url.searchParams.set('key', key);

      const res  = await fetch(url);
      const data = await res.json();

      if (!res.ok || data.error) {
        throw new Error('Could not find your representatives. Double-check your town name and try again.');
      }

      const officials = data.officials || [];
      const offices   = data.offices   || [];
      const reps      = [];

      offices.forEach(office => {
        if (!office.levels?.includes('administrativeArea1')) return;
        const isLeg = office.roles?.some(r => r.startsWith('legislator'));
        if (!isLeg) return;
        (office.officialIndices || []).forEach(i => {
          const o = officials[i];
          if (o) reps.push({
            name:   o.name,
            office: office.name,
            email:  o.emails?.[0] || null,
            phone:  o.phones?.[0] || null,
          });
        });
      });

      if (!reps.length) {
        throw new Error('No Vermont legislators found for that town. Try entering your full street address.');
      }

      return reps;
    }

    // ── Show confirmation ──────────────────────────────────────────────────
    function showConfirmation(reps, msg, senderEmail) {
      // Build rep chips
      confirmReps.innerHTML = '';
      const emailAddrs = [];

      reps.forEach(rep => {
        const chip = document.createElement('div');
        chip.className = 'lc-rep-chip';
        chip.innerHTML = `
          <span class="lc-rep-chip__office">${escHtml(rep.office)}</span>
          <span class="lc-rep-chip__name">${escHtml(rep.name)}</span>
          ${rep.email ? `<a class="lc-rep-chip__email" href="mailto:${escHtml(rep.email)}">${escHtml(rep.email)}</a>` : ''}
        `;
        confirmReps.appendChild(chip);
        if (rep.email) emailAddrs.push(rep.email);
      });

      // Populate copy textarea
      confirmMsg.value = msg;

      // Open mailto
      if (emailAddrs.length) {
        const subject = encodeURIComponent("Please Protect Vermont's Town Tuition Program");
        const body    = encodeURIComponent(msg);
        const cc      = senderEmail ? `&cc=${encodeURIComponent(senderEmail)}` : '';
        window.location.href = `mailto:${emailAddrs.join(',')}?subject=${subject}&body=${body}${cc}`;
      }

      // Switch views
      formView.hidden   = true;
      confirmView.hidden = false;
      root.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // ── Copy button ────────────────────────────────────────────────────────
    copyBtn.addEventListener('click', async () => {
      try {
        await navigator.clipboard.writeText(confirmMsg.value);
        copyBtn.textContent = '✓ Copied!';
        setTimeout(() => { copyBtn.textContent = 'Copy Message'; }, 2500);
      } catch {
        confirmMsg.select();
        copyBtn.textContent = 'Select text above to copy';
      }
    });

    // ── Restart ────────────────────────────────────────────────────────────
    restartBtn.addEventListener('click', () => {
      messageArea.value  = DEFAULT_TEMPLATE;
      fnameInput.value   = '';
      lnameInput.value   = '';
      emailInput.value   = '';
      townInput.value    = '';
      if (roleInput) roleInput.value = '';
      formError.hidden   = true;
      formView.hidden    = false;
      confirmView.hidden = true;
      root.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    // ── Utility ────────────────────────────────────────────────────────────
    function escHtml(s) {
      return String(s || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
    }
  });

})();
