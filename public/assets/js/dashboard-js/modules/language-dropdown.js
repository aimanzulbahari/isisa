/**
 * Header: dropdown bahasa (UI interaktif sahaja)
 */
export function initLanguageDropdown() {
    const langOptions = document.querySelectorAll('.lang-option');
    const currentLangCode = document.getElementById('currentLangCode');

    langOptions.forEach(option => {
        option.addEventListener('click', () => {
            const selectedLang = option.dataset.lang;
            if (currentLangCode) {
                currentLangCode.textContent = selectedLang;
            }

            langOptions.forEach(btn => {
                btn.classList.remove('active');
                const checkIcon = btn.querySelector('.lang-check');
                if (checkIcon) checkIcon.classList.add('d-none');
            });

            option.classList.add('active');
            const activeCheck = option.querySelector('.lang-check');
            if (activeCheck) activeCheck.classList.remove('d-none');
        });
    });
}
