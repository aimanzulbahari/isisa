/**
 * Select2: pilihan berbilang (jQuery + Select2 dimuatkan dahulu)
 */
export function initSelect2() {
    // Select2: pilihan berbilang (jQuery + Select2 dimuatkan di atas)
    if (window.jQuery && jQuery.fn.select2) {
        const $multi = jQuery('#inMulti');
        $multi.select2({
            width: '100%',
            placeholder: $multi.data('placeholder'),
            closeOnSelect: false,
            language: {
                noResults: () => 'Tiada padanan dijumpai',
                searching: () => 'Mencari...'
            }
        });
        // Kemas kini paparan Select2 apabila borang disetkan semula
        const parentForm = document.getElementById('demoForm');
        if (parentForm) {
            parentForm.addEventListener('reset', () => setTimeout(() => $multi.trigger('change'), 0));
        }
    }
}
