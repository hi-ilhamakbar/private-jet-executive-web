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
const returnInputs = document.querySelectorAll('[data-return-field] input, [data-return-field] [data-time-trigger]');
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

document.querySelectorAll('[data-country-picker]').forEach((select) => {
  const wrapper = document.createElement('div');
  wrapper.className = 'country-picker';
  const button = document.createElement('button');
  button.type = 'button';
  button.className = 'country-picker__button';
  button.setAttribute('aria-label', 'Select country calling code');
  button.setAttribute('aria-expanded', 'false');
  const list = document.createElement('div');
  list.className = 'country-picker__list';
  list.hidden = true;
  select.classList.add('country-picker__native');
  select.parentNode.insertBefore(wrapper, select);
  wrapper.append(select, button, list);

  const selectedOption = () => select.options[select.selectedIndex];
  const renderButton = () => {
    const option = selectedOption();
    const flag = option?.dataset.flag;
    button.replaceChildren();
    if (flag) {
      const image = document.createElement('img');
      image.src = `https://flagcdn.com/w40/${flag}.png`;
      image.alt = '';
      image.width = 20;
      image.height = 15;
      button.append(image);
    }
    button.append(document.createTextNode(option?.text || 'Country code'));
  };
  [...select.options].forEach((option) => {
    if (!option.value) return;
    const choice = document.createElement('button');
    choice.type = 'button';
    choice.className = 'country-picker__option';
    const image = document.createElement('img');
    image.src = `https://flagcdn.com/w40/${option.dataset.flag}.png`;
    image.alt = '';
    image.width = 20;
    image.height = 15;
    choice.append(image, document.createTextNode(option.text));
    choice.addEventListener('click', () => {
      select.value = option.value;
      select.dispatchEvent(new Event('change', { bubbles: true }));
      renderButton();
      list.hidden = true;
      button.setAttribute('aria-expanded', 'false');
      button.focus();
    });
    list.append(choice);
  });
  button.addEventListener('click', () => {
    const opening = list.hidden;
    document.querySelectorAll('.country-picker__list').forEach((element) => element.hidden = true);
    list.hidden = !opening;
    button.setAttribute('aria-expanded', String(opening));
  });
  select.addEventListener('change', renderButton);
  renderButton();
  document.addEventListener('click', (event) => {
    if (!wrapper.contains(event.target)) { list.hidden = true; button.setAttribute('aria-expanded', 'false'); }
  });
});

document.querySelectorAll('[data-time-picker]').forEach((picker) => {
  const valueInput = picker.querySelector('[data-time-value]');
  const trigger = picker.querySelector('[data-time-trigger]');
  const panel = picker.querySelector('[data-time-panel]');
  const hours = picker.querySelector('[data-time-hours]');
  const minutes = picker.querySelector('[data-time-minutes]');
  let [selectedHour, selectedMinute] = valueInput.value ? valueInput.value.split(':').map(Number) : [null, null];

  const update = () => {
    trigger.textContent = selectedHour === null || selectedMinute === null
      ? 'Select a time'
      : `${String(selectedHour).padStart(2, '0')}:${String(selectedMinute).padStart(2, '0')}`;
    valueInput.value = selectedHour === null || selectedMinute === null
      ? ''
      : `${String(selectedHour).padStart(2, '0')}:${String(selectedMinute).padStart(2, '0')}`;
    [...hours.children].forEach((button) => button.classList.toggle('time-picker__choice--active', Number(button.value) === selectedHour));
    [...minutes.children].forEach((button) => button.classList.toggle('time-picker__choice--active', Number(button.value) === selectedMinute));
  };
  const choose = (kind, value) => {
    if (kind === 'hour') selectedHour = value;
    else selectedMinute = value;
    update();
    if (selectedHour !== null && selectedMinute !== null && kind === 'minute') {
      panel.hidden = true;
      trigger.setAttribute('aria-expanded', 'false');
      trigger.focus();
    }
  };
  for (let hour = 0; hour < 24; hour += 1) {
    const choice = document.createElement('button');
    choice.type = 'button';
    choice.value = String(hour);
    choice.className = 'time-picker__choice';
    choice.textContent = String(hour).padStart(2, '0');
    choice.addEventListener('click', () => choose('hour', hour));
    hours.append(choice);
  }
  for (let minute = 0; minute < 60; minute += 5) {
    const choice = document.createElement('button');
    choice.type = 'button';
    choice.value = String(minute);
    choice.className = 'time-picker__choice';
    choice.textContent = String(minute).padStart(2, '0');
    choice.addEventListener('click', () => choose('minute', minute));
    minutes.append(choice);
  }
  trigger.addEventListener('click', () => {
    const opening = panel.hidden;
    document.querySelectorAll('[data-time-panel]').forEach((element) => element.hidden = true);
    panel.hidden = !opening;
    trigger.setAttribute('aria-expanded', String(opening));
  });
  document.addEventListener('click', (event) => {
    if (!picker.contains(event.target)) { panel.hidden = true; trigger.setAttribute('aria-expanded', 'false'); }
  });
  trigger.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') { panel.hidden = true; trigger.setAttribute('aria-expanded', 'false'); }
  });
  update();
});

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
    const location = city || name;
    const airport = name && name !== location ? ` — ${name}` : '';
    const identifier = code ? ` (${code})` : '';
    return `${location}${identifier}${airport}${country ? ` — ${country}` : ''}`;
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
