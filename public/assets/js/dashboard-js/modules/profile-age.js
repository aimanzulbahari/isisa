/**
 * Profil Saya: kira umur secara automatik daripada Tarikh Lahir (UI sahaja)
 * Medan: #profileBirthDate (input tarikh) -> #profileAge (baca sahaja)
 */
export function initProfileAge() {
    const birthInput = document.getElementById('profileBirthDate');
    const ageInput = document.getElementById('profileAge');
    if (!birthInput || !ageInput) return;

    // Tarikh lahir tidak boleh melebihi hari ini
    const today = new Date();
    birthInput.max = today.toISOString().slice(0, 10);

    const calculate = () => {
        if (!birthInput.value) {
            ageInput.value = '';
            return;
        }
        const birth = new Date(birthInput.value + 'T00:00:00');
        let age = today.getFullYear() - birth.getFullYear();
        const hadBirthday = today.getMonth() > birth.getMonth()
            || (today.getMonth() === birth.getMonth() && today.getDate() >= birth.getDate());
        if (!hadBirthday) age -= 1;
        ageInput.value = age >= 0 ? age + ' tahun' : '';
    };

    birthInput.addEventListener('input', calculate);
    birthInput.addEventListener('change', calculate);
    calculate();
}
