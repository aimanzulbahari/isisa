/**
 * AUTH JS - Halaman Log Masuk (ISISA)
 * Fail masuk (entry point): mengimport dan memulakan semua modul.
 *
 * PENTING: muatkan dengan <script type="module" src=".../auth-js/main.js"></script>
 */
import { initDisabledButtons } from './modules/disabled-buttons.js';
import { initIcInput } from './modules/ic-input.js';
import { initIcAutofill } from './modules/ic-autofill.js';
import { initPasswordToggle } from './modules/password-toggle.js';
import { initFormValidation } from './modules/form-validation.js';
import { initNoticePopup } from './modules/notice-popup.js';

initDisabledButtons();
initIcInput();
initIcAutofill();
initPasswordToggle();
initFormValidation();
initNoticePopup();
