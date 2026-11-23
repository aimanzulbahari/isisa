/**
 * DASHBOARD JS - Panel Pentadbiran (ISISA)
 * Fail masuk (entry point): mengimport dan memulakan semua modul.
 *
 * PENTING: muatkan dengan <script type="module" src=".../dashboard-js/main.js"></script>
 * (selepas Bootstrap, Chart.js, jQuery, Select2, SweetAlert2 & Toastr).
 */
import { initWelcomeHeader } from './modules/welcome-header.js';
import { initLanguageDropdown } from './modules/language-dropdown.js';
import { initDataTable } from './modules/data-table.js';
import { initStatusPie } from './modules/charts/status-pie.js';
import { initDeptDonut } from './modules/charts/dept-donut.js';
import { initWeeklyLine } from './modules/charts/weekly-line.js';
import { initHorizontalBar } from './modules/charts/horizontal-bar.js';
import { initPolarArea } from './modules/charts/polar-area.js';
import { initRadar } from './modules/charts/radar.js';
import { initCombo } from './modules/charts/combo.js';
import { initSteppedLine } from './modules/charts/stepped-line.js';
import { initStackedBar } from './modules/charts/stacked-bar.js';
import { initStackedLine } from './modules/charts/stacked-line.js';
import { initToastrDemo } from './modules/toastr-demo.js';
import { initSelect2 } from './modules/select2.js';
import { initConfirmDialogs } from './modules/confirm-dialogs.js';
import { initNotifications } from './modules/notifications.js';
import { initBackToTop } from './modules/back-to-top.js';
import { initPageTabs } from './modules/page-tabs.js';
import { initDemoForm } from './modules/demo-form.js';
import { initProfileAge } from './modules/profile-age.js';
import { initPasswordToggle } from './modules/password-toggle.js';
import { initMonthlyBar } from './modules/charts/monthly-bar.js';
import { initAnnouncementForm } from './modules/announcement-form.js';

document.addEventListener('DOMContentLoaded', () => {
    initWelcomeHeader();
    initLanguageDropdown();
    initDataTable();
    initStatusPie();
    initDeptDonut();
    initWeeklyLine();
    initHorizontalBar();
    initPolarArea();
    initRadar();
    initCombo();
    initSteppedLine();
    initStackedBar();
    initStackedLine();
    initToastrDemo();
    initSelect2();
    initConfirmDialogs();
    initNotifications();
    initBackToTop();
    initPageTabs();
    initDemoForm();
    initProfileAge();
    initPasswordToggle();
    initMonthlyBar();
    initAnnouncementForm();
});
