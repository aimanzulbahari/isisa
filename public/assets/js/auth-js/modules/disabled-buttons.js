/**
 * Butang dilumpuhkan (MyDigital ID - akan datang)
 * Halang sebarang klik pada elemen bertanda aria-disabled="true".
 */
export function initDisabledButtons() {
    const disabledButtons = document.querySelectorAll('[aria-disabled="true"]');
    disabledButtons.forEach(function (btn) {
        btn.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
        });
    });
}
