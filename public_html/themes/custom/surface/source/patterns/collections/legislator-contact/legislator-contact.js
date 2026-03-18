/**
 * @file legislator-contact.js
 *
 * Form-first legislator contact flow:
 *   1. User fills in name, email, town, role, message
 *   2. Town is matched to VT district via vt-districts-by-town.json
 *      then cross-referenced with vt-legislators.json for email addresses.
 *      Falls back to Google Civic Information API if a key is provided.
 *   3. mailto: link is opened with rep email(s) pre-addressed
 *   4. Confirmation view shown with copy fallback
 */

(function () {
  'use strict';

/**
 * @file legislator-contact.js
 *
 * Form-first legislator contact flow:
 *   1. User fills in name, email, town, role, message
 *   2. Town is matched to VT House district via vt-districts-by-town.json
 *      then cross-referenced with vt-legislators.json for email addresses.
 *      Falls back to Google Civic Information API if a key is provided.
 *   3. mailto: link is opened with rep email(s) pre-addressed
 *   4. Confirmation view shown with copy fallback
 */
  function init() {
    const root = document.getElementById('legislator-contact');
    if (!root) { return; }
    if (root.dataset.lcInitialized) { return; }
    root.dataset.lcInitialized = 'true';

    const apiKey      = root.dataset.civicApiKey || window.CIVIC_API_KEY || '';
    const formView    = document.getElementById('lc-form');
    const confirmView = document.getElementById('lc-confirm');

    const fnameInput  = document.getElementById('lc-fname');
    const lnameInput  = document.getElementById('lc-lname');
    const emailInput  = document.getElementById('lc-email');
    const townInput   = document.getElementById('lc-town');
    const districtPickerWrap       = document.getElementById('lc-district-picker');
    const districtSelect            = document.getElementById('lc-district');
    const senateDistrictPickerWrap  = document.getElementById('lc-senate-district-picker');
    const senateDistrictSelect      = document.getElementById('lc-senate-district');
    const roleInput   = document.getElementById('lc-role');
    const messageArea = document.getElementById('lc-message');
    const submitBtn   = document.getElementById('lc-submit-btn');
    const formError   = document.getElementById('lc-form-error');

    const confirmReps = document.getElementById('lc-confirm-reps');
    const confirmMsg  = document.getElementById('lc-confirm-msg');
    const copyBtn     = document.getElementById('lc-copy-btn');
    const mailtoBtn   = document.getElementById('lc-mailto-btn');
    const restartBtn  = document.getElementById('lc-restart');

    // ── Analytics ──────────────────────────────────────────────────────────
    function pushEvent(eventName, data = {}) {
      window.dataLayer = window.dataLayer || [];
      window.dataLayer.push({ event: eventName, ...data });
    }

    // Track first interaction with any form field (once per session)
    let formStarted = false;
    [fnameInput, lnameInput, emailInput, townInput, roleInput].forEach((el) => {
      el?.addEventListener('focus', () => {
        if (formStarted) return;
        formStarted = true;
        pushEvent('lc_form_start');
      }, { once: true });
    });

    // ── District picker ────────────────────────────────────────────────────
    let cachedDistrictData = null;

    async function maybeShowDistrictPicker() {
      const town = townInput.value.trim();
      if (!town) { hideDistrictPicker(); return; }

      try {
        if (!cachedDistrictData) {
          cachedDistrictData = await fetchJson('/themes/custom/surface/data/vt-districts-by-town.json');
        }
        const normalizedTown = town.toLowerCase();
        const ambiguous = ['barre', 'newport', 'st. albans', 'rutland', 'essex'];
        if (ambiguous.includes(normalizedTown)) { hideDistrictPicker(); return; }

        const townKey = Object.keys(cachedDistrictData).find((k) => k.toLowerCase() === normalizedTown);
        if (!townKey) { hideDistrictPicker(); return; }

        const townData       = cachedDistrictData[townKey];
        const houseDistricts = townData.houseDistricts  || [];
        const senateDistricts = townData.senateDistricts || [];

        // House picker.
        if (houseDistricts.length > 1) {
          if (districtPickerWrap.hidden) {
            districtSelect.innerHTML = '<option value="">Not sure — choose one for me</option>';
            houseDistricts.forEach((d) => {
              const opt = document.createElement('option');
              opt.value = d;
              opt.textContent = d;
              districtSelect.appendChild(opt);
            });
            districtPickerWrap.hidden = false;
          }
        } else {
          districtPickerWrap.hidden = true;
          districtSelect.innerHTML  = '<option value="">Not sure — choose one for me</option>';
        }

        // Senate picker.
        if (senateDistricts.length > 1) {
          if (senateDistrictPickerWrap.hidden) {
            senateDistrictSelect.innerHTML = '<option value="">Not sure — choose one for me</option>';
            senateDistricts.forEach((d) => {
              const opt = document.createElement('option');
              opt.value = d;
              opt.textContent = d;
              senateDistrictSelect.appendChild(opt);
            });
            senateDistrictPickerWrap.hidden = false;
          }
        } else {
          senateDistrictPickerWrap.hidden = true;
          senateDistrictSelect.innerHTML  = '<option value="">Not sure — choose one for me</option>';
        }

      } catch (e) {
        hideDistrictPicker();
      }
    }

    function hideDistrictPicker() {
      districtPickerWrap.hidden      = true;
      districtSelect.innerHTML       = '<option value="">Not sure — choose one for me</option>';
      senateDistrictPickerWrap.hidden = true;
      senateDistrictSelect.innerHTML  = '<option value="">Not sure — choose one for me</option>';
    }

    let townInputTimer = null;
    townInput.addEventListener('input', () => {
      clearTimeout(townInputTimer);
      townInputTimer = setTimeout(maybeShowDistrictPicker, 300);
    });

    // ── Personalization ────────────────────────────────────────────────────
    // Tracks the last-substituted values so we can restore placeholders on change
    let currentName = '[Your Name]';
    let currentTown = '[Town]';
    let currentRole = '[Role]';

    function escRegex(s) {
      return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    function personalizeMessage() {
      const name = [fnameInput.value.trim(), lnameInput.value.trim()].filter(Boolean).join(' ') || '[Your Name]';
      const town = townInput.value.trim() || '[Town]';
      const role = (roleInput && roleInput.value.trim()) || '[Role]';

      // Restore previous substituted values back to placeholders before re-substituting
      let msg = messageArea.value;
      if (currentName !== '[Your Name]') {
        msg = msg.replace(new RegExp(escRegex(currentName), 'g'), '[Your Name]');
      }
      if (currentTown !== '[Town]') {
        msg = msg.replace(new RegExp(escRegex(currentTown), 'g'), '[Town]');
      }
      if (currentRole !== '[Role]') {
        msg = msg.replace(new RegExp(escRegex(currentRole), 'g'), '[Role]');
      }

      msg = msg.replace(/\[Your Name\]/g, name).replace(/\[Name\]/g, name);
      msg = msg.replace(/\[Town\]/g, town);
      msg = msg.replace(/\[Role\]/g, role);

      currentName = name;
      currentTown = town;
      currentRole = role;
      messageArea.value = msg;
    }

    [fnameInput, lnameInput, townInput].forEach((el) => {
      el.addEventListener('input', personalizeMessage);
    });
    if (roleInput) { roleInput.addEventListener('change', personalizeMessage); }

    // ── Submit ─────────────────────────────────────────────────────────────
    submitBtn.addEventListener('click', async () => {
      formError.hidden = true;

      const fname = fnameInput.value.trim();
      const lname = lnameInput.value.trim();
      const town  = townInput.value.trim();
      const email = emailInput.value.trim();
      const msg   = messageArea.value.trim();
      const role  = roleInput ? roleInput.value.trim() : '';

      pushEvent('lc_form_submit_attempt', { role });

      if (!fname || !email || !town || !role || !msg) {
        formError.textContent = 'Please fill in all required fields before sending.';
        formError.hidden = false;
        formError.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        return;
      }

      // Final substitution pass — guarantees all placeholders are replaced
      // even if the user filled fields out of order or edited the message manually
      const fullName = [fname, lname].filter(Boolean).join(' ');
      const finalMsg = msg
        .replace(/\[Your Name\]/g, fullName).replace(/\[Name\]/g, fullName)
        .replace(/\[Town\]/g, town)
        .replace(/\[Role\]/g, role);

      submitBtn.disabled = true;
      submitBtn.textContent = 'Looking up your representatives…';

      // If district picker is not yet shown, check now in case user
      // submitted without blurring the town field first.
      const houseWasHidden  = districtPickerWrap.hidden;
      const senateWasHidden = senateDistrictPickerWrap.hidden;

      await maybeShowDistrictPicker();

      const houseJustAppeared  = houseWasHidden  && !districtPickerWrap.hidden;
      const senateJustAppeared = senateWasHidden && !senateDistrictPickerWrap.hidden;

      if (houseJustAppeared || senateJustAppeared) {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Find My Representatives →';
        const firstPicker = houseJustAppeared ? districtPickerWrap : senateDistrictPickerWrap;
        firstPicker.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        (houseJustAppeared ? districtSelect : senateDistrictSelect).focus();
        return;
      }

      try {
        const chosenDistrict       = districtSelect && !districtPickerWrap.hidden ? districtSelect.value : '';
        const chosenSenateDistrict = senateDistrictSelect && !senateDistrictPickerWrap.hidden ? senateDistrictSelect.value : '';
        const reps = await resolveReps(town, apiKey, chosenDistrict, chosenSenateDistrict);
        showConfirmation(reps, finalMsg, email);
      } catch (err) {
        formError.textContent = err.message || 'Could not find your representatives. Try entering your full address.';
        formError.hidden = false;
        formError.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Send to My Legislators →';
      }
    });

    // ── Rep resolution ─────────────────────────────────────────────────────
    async function resolveReps(town, key, chosenDistrict = '', chosenSenateDistrict = '') {
      if (!town) {
        throw new Error('Please enter your town name.');
      }

      let districtData, legislatorData;
      try {
        [districtData, legislatorData] = await Promise.all([
          fetchJson('/themes/custom/surface/data/vt-districts-by-town.json'),
          fetchJson('/themes/custom/surface/data/vt-legislators.json'),
        ]);
      } catch (fetchErr) {
        if (key) { return resolveViaApi(town, key); }
        throw fetchErr;
      }

      const normalizedTown = town.trim().toLowerCase();

      // Prompt for clarification when town name is ambiguous.
      const ambiguous = {
        'barre':      ['Barre City', 'Barre Town'],
        'newport':    ['Newport City', 'Newport Town'],
        'st. albans': ['St. Albans City', 'St. Albans Town'],
        'rutland':    ['Rutland City', 'Rutland Town'],
        'essex':      ['Essex', 'Essex Junction'],
      };

      if (ambiguous[normalizedTown]) {
        const options = ambiguous[normalizedTown].join(' or ');
        throw new Error(`Did you mean ${options}? Please be more specific.`);
      }

      let townKey = Object.keys(districtData).find(
        (k) => k.toLowerCase() === normalizedTown
      );

      if (!townKey) {
        throw new Error(`"${town}" was not found in our Vermont town list. Check the spelling and try again.`);
      }

      const townData = districtData[townKey];
      let houseDistricts   = extractDistricts(townData, 'house');
      const senateDistricts = extractDistricts(townData, 'senate');

      // Use chosen district if provided, otherwise pick randomly.
      if (houseDistricts.length > 1) {
        const pick = chosenDistrict && houseDistricts.includes(chosenDistrict)
          ? chosenDistrict
          : houseDistricts[Math.floor(Math.random() * houseDistricts.length)];
        houseDistricts = [pick];
      }

      let selectedSenateDistricts = senateDistricts;
      if (senateDistricts.length > 1) {
        const pick = chosenSenateDistrict && senateDistricts.includes(chosenSenateDistrict)
          ? chosenSenateDistrict
          : senateDistricts[Math.floor(Math.random() * senateDistricts.length)];
        selectedSenateDistricts = [pick];
      }

      const reps = [];

      houseDistricts.forEach((district) => {
        legislatorData.representatives
          .filter((r) => normalizeDistrict(r.district) === normalizeDistrict(district))
          .forEach((r) => reps.push({ name: r.name, office: `Vermont House — ${r.district}`, email: r.email }));
      });

      selectedSenateDistricts.forEach((district) => {
        legislatorData.senators
          .filter((s) => normalizeDistrict(s.district) === normalizeDistrict(district))
          .forEach((s) => reps.push({ name: s.name, office: `Vermont Senate — ${s.district} District`, email: s.email }));
      });

      if (reps.length) { return reps; }
      throw new Error('No legislators found for that town in our records.');
    }

    function extractDistricts(townData, chamber) {
      if (!townData) { return []; }
      if (typeof townData === 'object' && !Array.isArray(townData)) {
        const key = chamber === 'house'
          ? (townData.houseDistricts || townData.houseDistrict || townData.house_district || townData.house)
          : (townData.senateDistricts || townData.senateDistrict || townData.senate_district || townData.senate);
        if (!key) { return []; }
        return Array.isArray(key) ? key : [key];
      }
      if (Array.isArray(townData))    { return chamber === 'house' ? townData : []; }
      if (typeof townData === 'string') { return chamber === 'house' ? [townData] : []; }
      return [];
    }

    function normalizeDistrict(d) {
      return String(d || '').toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
    }

    async function fetchJson(url) {
      const res = await fetch(url);
      if (!res.ok) { throw new Error(`Failed to load ${url}`); }
      return res.json();
    }

    async function resolveViaApi(town, key) {
      const url = new URL('https://www.googleapis.com/civicinfo/v2/representatives');
      url.searchParams.set('address', `${town}, Vermont`);
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

      offices.forEach((office) => {
        if (!office.levels?.includes('administrativeArea1')) { return; }
        const isLeg = office.roles?.some((r) => r.startsWith('legislator'));
        if (!isLeg) { return; }
        (office.officialIndices || []).forEach((i) => {
          const o = officials[i];
          if (o) {
            reps.push({
              name:   o.name,
              office: office.name,
              email:  o.emails?.[0] || null,
              phone:  o.phones?.[0] || null,
            });
          }
        });
      });

      if (!reps.length) {
        throw new Error('No Vermont legislators found for that town. Try entering your full street address.');
      }

      return reps;
    }

    // ── Show confirmation ──────────────────────────────────────────────────
    function showConfirmation(reps, msg, senderEmail) {
      pushEvent('lc_confirmation_shown', { rep_count: reps.length });
      const emailAddrs = [];

      confirmReps.innerHTML = '';
      reps.forEach((rep) => {
        const chip = document.createElement('div');
        chip.className = 'lc-rep-chip';
        chip.innerHTML = `
          <span class="lc-rep-chip__name">${escHtml(rep.name)}</span>
          <span class="lc-rep-chip__office">${escHtml(rep.office)}</span>
          ${rep.email ? `<a class="lc-rep-chip__email" href="mailto:${escHtml(rep.email)}">${escHtml(rep.email)}</a>` : ''}
        `;
        confirmReps.appendChild(chip);
        if (rep.email) { emailAddrs.push(rep.email); }
      });

      confirmMsg.value = msg;

      if (emailAddrs.length) {
        const subject    = encodeURIComponent("Please Protect Vermont's Town Tuition Program");
        const body       = encodeURIComponent(msg);
        const cc         = senderEmail ? `&cc=${encodeURIComponent(senderEmail)}` : '';
        const mailtoHref = `mailto:${emailAddrs.join(',')}?subject=${subject}&body=${body}${cc}`;
        if (mailtoBtn) {
          mailtoBtn.href = mailtoHref;
          mailtoBtn.hidden = false;
          mailtoBtn.addEventListener('click', () => {
            pushEvent('lc_email_opened', { rep_count: reps.length });
          }, { once: true });
        }
      } else {
        if (mailtoBtn) { mailtoBtn.hidden = true; }
      }

      formView.hidden    = true;
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
      messageArea.value  = '';
      fnameInput.value   = '';
      lnameInput.value   = '';
      emailInput.value   = '';
      townInput.value    = '';
      if (roleInput) { roleInput.value = ''; }
      hideDistrictPicker();
      senateDistrictPickerWrap.hidden = true;
      formStarted        = false;
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
  }
  // With JS aggregation, DOMContentLoaded may have already fired by the
  // time this bundle executes — run immediately in that case.
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

}());
