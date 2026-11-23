/**
 * Notifikasi: tandakan dibaca, tapis & kiraan
 */
export function initNotifications() {
    const notifList = document.getElementById('notifList');
    if (notifList) {
        const notifCount = document.getElementById('notifUnreadCount');
        const notifEmpty = document.getElementById('notifEmpty');
        const btnAll = document.getElementById('notifFilterAll');
        const btnUnread = document.getElementById('notifFilterUnread');
        let unreadOnly = false;

        const refreshNotifs = () => {
            const items = notifList.querySelectorAll('.notif-item');
            const unread = notifList.querySelectorAll('.notif-item.unread').length;
            notifCount.textContent = unread;
            notifCount.classList.toggle('d-none', unread === 0);
            let shown = 0;
            items.forEach(li => {
                const show = !unreadOnly || li.classList.contains('unread');
                li.classList.toggle('d-none', !show);
                if (show) shown++;
            });
            notifEmpty.classList.toggle('d-none', shown > 0);
        };

        const markRead = (li) => {
            li.classList.remove('unread');
            const dot = li.querySelector('.notif-dot');
            if (dot) dot.remove();
            refreshNotifs();
        };

        notifList.querySelectorAll('.notif-item').forEach(li => {
            li.addEventListener('click', () => markRead(li));
            li.addEventListener('keydown', e => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    markRead(li);
                }
            });
        });

        document.getElementById('notifMarkAll').addEventListener('click', () => {
            notifList.querySelectorAll('.notif-item.unread').forEach(markRead);
        });

        const setFilter = (onlyUnread) => {
            unreadOnly = onlyUnread;
            btnAll.className = 'btn toolbar-ctl rounded-end-0 ' + (onlyUnread ? 'toolbar-ctl-outline' : 'btn-primary');
            btnUnread.className = 'btn toolbar-ctl rounded-start-0 ' + (onlyUnread ? 'btn-primary' : 'toolbar-ctl-outline');
            btnAll.setAttribute('aria-pressed', String(!onlyUnread));
            btnUnread.setAttribute('aria-pressed', String(onlyUnread));
            refreshNotifs();
        };
        btnAll.addEventListener('click', () => setFilter(false));
        btnUnread.addEventListener('click', () => setFilter(true));
        refreshNotifs();
    }
}
