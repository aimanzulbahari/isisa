/**
 * Tab halaman 1-8: tukar tab aktif & gulung ke dalam pandangan
 */
export function initPageTabs() {
    const pageTabs = document.querySelectorAll('#pageTabs .nav-link');
    const tabMenuItems = document.querySelectorAll('#pageTabsMenu .dropdown-item');
    const tabCurrentLabel = document.getElementById('pageTabsCurrent');

    // Selaraskan tab biasa & dropdown (skrin kecil)
    const setActiveTab = (index) => {
        pageTabs.forEach((t, i) => {
            const on = i === index;
            t.classList.toggle('active', on);
            t.setAttribute('aria-selected', String(on));
            t.tabIndex = on ? 0 : -1;
        });
        tabMenuItems.forEach((item, i) => item.classList.toggle('active', i === index));
        if (tabCurrentLabel) tabCurrentLabel.textContent = pageTabs[index].textContent;
    };

    tabMenuItems.forEach((item, i) => item.addEventListener('click', () => setActiveTab(i)));

    pageTabs.forEach((tab, idx) => {
        tab.addEventListener('click', () => {
            setActiveTab(idx);
            tab.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest',
                inline: 'center'
            });
        });
        tab.addEventListener('keydown', e => {
            if (e.key !== 'ArrowRight' && e.key !== 'ArrowLeft') return;
            const next = pageTabs[(idx + (e.key === 'ArrowRight' ? 1 : pageTabs.length - 1)) % pageTabs.length];
            next.focus();
            next.click();
        });
    });
}
