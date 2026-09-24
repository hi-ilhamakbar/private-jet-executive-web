'use strict';

const header = document.querySelector('[data-header]');
const menuToggle = document.querySelector('[data-menu-toggle]');
const navigation = document.querySelector('[data-navigation]');

const setHeaderState = () => header?.classList.toggle('site-header--scrolled', window.scrollY > 24);
setHeaderState();
window.addEventListener('scroll', setHeaderState, { passive: true });

menuToggle?.addEventListener('click', () => {
  const isOpen = menuToggle.getAttribute('aria-expanded') === 'true';
  menuToggle.setAttribute('aria-expanded', String(!isOpen));
  navigation?.classList.toggle('site-nav--open', !isOpen);
  document.body.classList.toggle('menu-open', !isOpen);
});

document.querySelectorAll('input[inputmode="numeric"]').forEach((input) => {
  input.addEventListener('input', () => {
    input.value = input.value.replace(/\D/g, '');
  });
});

document.querySelectorAll('[data-count-target]').forEach((input) => {
  const counter = document.getElementById(input.dataset.countTarget);
  const updateCount = () => {
    if (counter) counter.textContent = `${input.value.length.toLocaleString()} / ${Number(input.maxLength).toLocaleString()}`;
  };
  input.addEventListener('input', updateCount);
  updateCount();
});

const returnFields = document.querySelectorAll('[data-return-field]');
const returnInputs = document.querySelectorAll('[data-return-field] input');
const departureDate = document.getElementById('departure_date');
const returnDate = document.getElementById('return_date');
const setJourneyType = () => {
  const isReturn = document.querySelector('[data-journey-type][value="return"]')?.checked;
  returnFields.forEach((field) => {
    field.classList.toggle('field--disabled', !isReturn);
    field.setAttribute('aria-disabled', String(!isReturn));
  });
  returnInputs.forEach((input) => {
    input.required = Boolean(isReturn);
    input.disabled = !isReturn;
  });
};
document.querySelectorAll('[data-journey-type]').forEach((input) => input.addEventListener('change', setJourneyType));
const syncReturnDate = () => {
  if (!departureDate || !returnDate) return;
  returnDate.min = departureDate.value || departureDate.min;
  if (returnDate.value && returnDate.value < returnDate.min) returnDate.value = returnDate.min;
};
departureDate?.addEventListener('change', syncReturnDate);
setJourneyType();
syncReturnDate();

document.querySelectorAll('[data-airport-search]').forEach((input) => {
  const results = document.getElementById(input.getAttribute('aria-controls'));
  const status = input.closest('.airport-field')?.querySelector('[data-airport-status]');
  let items = [];
  let activeIndex = -1;
  let controller;
  let timer;

  const resultLabel = (item) => {
    const city = item.city?.name || item.city_name || item.city || item.location?.city || '';
    const name = item.name || item.airport_name || item.title || '';
    const code = item.iata || item.iata_code || item.code || item.airport_code || '';
    const country = item.country?.name || item.country_name || item.country || '';
    return [city, name !== city ? name : '', code ? `(${code})` : '', country].filter(Boolean).join(' · ');
  };
  const closeResults = () => {
    items = [];
    activeIndex = -1;
    results.hidden = true;
    results.replaceChildren();
    input.setAttribute('aria-expanded', 'false');
  };
  const selectItem = (item) => {
    input.value = resultLabel(item);
    closeResults();
    input.focus();
  };
  const render = (list) => {
    items = list;
    activeIndex = -1;
    results.replaceChildren();
    if (!items.length) {
      results.hidden = true;
      status.textContent = 'No matching airport or city found.';
      input.setAttribute('aria-expanded', 'false');
      return;
    }
    items.forEach((item, index) => {
      const option = document.createElement('button');
      option.type = 'button';
      option.className = 'airport-option';
      option.setAttribute('role', 'option');
      option.id = `${input.id}-option-${index}`;
      option.textContent = resultLabel(item) || 'Airport result';
      option.addEventListener('mousedown', (event) => { event.preventDefault(); selectItem(item); });
      results.append(option);
    });
    results.hidden = false;
    status.textContent = `${items.length} matching locations available.`;
    input.setAttribute('aria-expanded', 'true');
  };
  const setActive = (index) => {
    activeIndex = index;
    [...results.children].forEach((element, itemIndex) => element.classList.toggle('airport-option--active', itemIndex === activeIndex));
    input.setAttribute('aria-activedescendant', activeIndex >= 0 ? `${input.id}-option-${activeIndex}` : '');
  };
  const search = async () => {
    const query = input.value.trim();
    if (query.length < 2) { closeResults(); status.textContent = query ? 'Enter at least two characters.' : ''; return; }
    controller?.abort();
    controller = new AbortController();
    status.textContent = 'Searching airports…';
    try {
      const response = await fetch(`/api/airports.php?q=${encodeURIComponent(query)}`, { signal: controller.signal, headers: { Accept: 'application/json' } });
      if (!response.ok) throw new Error('Search request failed');
      const payload = await response.json();
      const list = Array.isArray(payload) ? payload : payload.data || payload.regions || payload.items || payload.results || [];
      render(Array.isArray(list) ? list : []);
    } catch (error) {
      if (error.name !== 'AbortError') { closeResults(); status.textContent = 'Airport search is temporarily unavailable. Please enter the location manually.'; }
    }
  };
  input.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(search, 320); });
  input.addEventListener('keydown', (event) => {
    if (!items.length) { if (event.key === 'Escape') closeResults(); return; }
    if (event.key === 'ArrowDown') { event.preventDefault(); setActive(Math.min(activeIndex + 1, items.length - 1)); }
    if (event.key === 'ArrowUp') { event.preventDefault(); setActive(Math.max(activeIndex - 1, 0)); }
    if (event.key === 'Enter' && activeIndex >= 0) { event.preventDefault(); selectItem(items[activeIndex]); }
    if (event.key === 'Escape') { event.preventDefault(); closeResults(); }
  });
  input.addEventListener('blur', () => window.setTimeout(closeResults, 150));
});
