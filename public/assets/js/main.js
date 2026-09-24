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
const setJourneyType = () => {
  const isReturn = document.querySelector('[data-journey-type][value="return"]')?.checked;
  returnFields.forEach((field) => field.hidden = !isReturn);
  returnInputs.forEach((input) => input.required = Boolean(isReturn));
};
document.querySelectorAll('[data-journey-type]').forEach((input) => input.addEventListener('change', setJourneyType));
setJourneyType();
