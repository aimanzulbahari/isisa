/**
 * Welcome header: salam dinamik & tarikh Bahasa Melayu (ms-MY)
 */
export function initWelcomeHeader() {
    const greetingEl = document.getElementById('welcomeGreeting');
    const dateDisplayEl = document.getElementById('currentDateDisplay');
    const now = new Date();

    if (greetingEl) {
        const hour = now.getHours();
        const userName = greetingEl.dataset.username || 'Pengguna';
        let greetingText = 'Selamat malam';

        if (hour >= 5 && hour < 12) {
            greetingText = 'Selamat pagi';
        } else if (hour >= 12 && hour < 14) {
            greetingText = 'Selamat tengah hari';
        } else if (hour >= 14 && hour < 19) {
            greetingText = 'Selamat petang';
        }

        greetingEl.textContent = `${greetingText}, ${userName}`;
    }

    if (dateDisplayEl) {
        const dateOptions = {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        };
        const formattedDate = now.toLocaleDateString('ms-MY', dateOptions);
        // Pastikan huruf pertama setiap perkataan utama kemas
        dateDisplayEl.textContent = formattedDate.charAt(0).toUpperCase() + formattedDate.slice(1);
    }
}
