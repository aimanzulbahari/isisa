/**
 * Jadual: carian, tapis, set semula, pagination & tambah data (UI sahaja)
 */
export function initDataTable() {
    const tbody = document.querySelector('.table-isisa tbody');
    // Halaman tanpa jadual (cth: Form) atau dengan jadual statik tanpa bar alat (cth: Accounts):
    // berhenti di sini supaya modul lain dalam main.js tetap berjalan
    if (!tbody || !document.getElementById('tableSearch')) return;
    const searchInput = document.getElementById('tableSearch');
    const statusLabels = {
        success: 'Diluluskan',
        warning: 'Semakan',
        danger: 'Perlu Tindakan'
    };

    const PAGE_SIZE = 10;
    let currentPage = 1;
    const paginationEl = document.getElementById('tablePagination');
    const tableInfoEl = document.getElementById('tableInfo');

    const buildRow = (rujukan, jabatan, tarikh, statusKey) => {
        const tr = document.createElement('tr');
        const addCell = (text, cls) => {
            const td = document.createElement('td');
            if (cls) td.className = cls;
            td.textContent = text;
            tr.appendChild(td);
            return td;
        };
        addCell('', 'text-muted');
        addCell(rujukan, 'fw-semibold');
        addCell(jabatan);
        addCell(tarikh, 'text-muted');
        const statusTd = addCell('');
        statusTd.innerHTML = '<span class="badge-status badge-status-' + statusKey + '"><i class="bi bi-circle-fill" style="font-size: 6px;" aria-hidden="true"></i></span>';
        statusTd.firstChild.append(statusLabels[statusKey]);
        const actionTd = addCell('', 'text-center');
        actionTd.innerHTML = '<div class="d-inline-flex gap-1"><a href="#" class="btn-action" aria-label="Lihat rekod" title="Lihat"><i class="bi bi-eye" aria-hidden="true"></i></a><a href="#" class="btn-action" aria-label="Sunting rekod" title="Sunting"><i class="bi bi-pencil" aria-hidden="true"></i></a></div>';
        return tr;
    };

    const renderPagination = (totalPages) => {
        paginationEl.innerHTML = '';
        const addItem = (label, page, opts = {}) => {
            const li = document.createElement('li');
            li.className = 'page-item' + (opts.active ? ' active' : '') + (opts.disabled ? ' disabled' : '');
            const a = document.createElement('a');
            a.className = 'page-link';
            a.href = '#';
            a.setAttribute('aria-label', opts.aria || ('Halaman ' + label));
            if (opts.active) a.setAttribute('aria-current', 'page');
            if (opts.icon) a.innerHTML = '<i class="bi ' + opts.icon + '" aria-hidden="true"></i>';
            else a.textContent = label;
            a.addEventListener('click', e => {
                e.preventDefault();
                if (opts.disabled || opts.active) return;
                currentPage = page;
                refreshTable(false);
            });
            li.appendChild(a);
            paginationEl.appendChild(li);
        };
        addItem('', currentPage - 1, {
            icon: 'bi-chevron-left',
            aria: 'Halaman sebelumnya',
            disabled: currentPage === 1
        });
        for (let p = 1; p <= totalPages; p++) addItem(String(p), p, {
            active: p === currentPage
        });
        addItem('', currentPage + 1, {
            icon: 'bi-chevron-right',
            aria: 'Halaman seterusnya',
            disabled: currentPage === totalPages
        });
    };

    const refreshTable = (resetPage = true) => {
        if (resetPage) currentPage = 1;
        const q = (searchInput?.value || '').trim().toLowerCase();
        const statusFilter = document.getElementById('filterStatus')?.value || '';
        const deptFilter = document.getElementById('filterDept')?.value || '';
        const rows = Array.from(tbody.querySelectorAll('tr:not(.empty-row)'));
        const matched = rows.filter(tr => {
            if (q && !tr.textContent.toLowerCase().includes(q)) return false;
            if (statusFilter && !tr.querySelector('.badge-status-' + statusFilter)) return false;
            if (deptFilter && tr.cells[2].textContent.trim() !== deptFilter) return false;
            return true;
        });
        const total = matched.length;
        const totalPages = Math.max(1, Math.ceil(total / PAGE_SIZE));
        if (currentPage > totalPages) currentPage = totalPages;
        const start = (currentPage - 1) * PAGE_SIZE;

        rows.forEach(tr => tr.classList.add('d-none'));
        matched.forEach((tr, i) => {
            tr.cells[0].textContent = i + 1;
            tr.classList.toggle('d-none', i < start || i >= start + PAGE_SIZE);
        });

        let empty = tbody.querySelector('.empty-row');
        if (total === 0 && !empty) {
            empty = document.createElement('tr');
            empty.className = 'empty-row';
            empty.innerHTML = '<td colspan="6" class="text-center text-muted py-4">Tiada rekod dijumpai.</td>';
            tbody.appendChild(empty);
        } else if (total > 0 && empty) {
            empty.remove();
        }

        tableInfoEl.textContent = total === 0 ?
            'Tiada rekod' :
            'Menunjukkan ' + (start + 1) + '–' + Math.min(start + PAGE_SIZE, total) + ' daripada ' + total + ' rekod';
        renderPagination(totalPages);
    };

    document.getElementById('btnFilter')?.addEventListener('click', () => refreshTable());
    searchInput?.addEventListener('keydown', e => {
        if (e.key === 'Enter') {
            e.preventDefault();
            refreshTable();
        }
    });
    document.getElementById('btnReset')?.addEventListener('click', () => {
        if (searchInput) searchInput.value = '';
        const statusSelect = document.getElementById('filterStatus');
        const deptSelect = document.getElementById('filterDept');
        if (statusSelect) statusSelect.value = '';
        if (deptSelect) deptSelect.value = '';
        refreshTable();
    });
    refreshTable();

    const addForm = document.getElementById('addDataForm');
    const addModalEl = document.getElementById('addDataModal');
    if (!addForm || !addModalEl) return; // Halaman tanpa modal Tambah Data
    addForm.addEventListener('submit', e => {
        e.preventDefault();
        if (!addForm.checkValidity()) {
            addForm.classList.add('was-validated');
            return;
        }
        const statusKey = document.getElementById('fieldStatus').value;
        const date = new Date(document.getElementById('fieldTarikh').value + 'T00:00:00')
            .toLocaleDateString('ms-MY', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });

        const tr = buildRow(
            document.getElementById('fieldRujukan').value.trim(),
            document.getElementById('fieldJabatan').value,
            date,
            statusKey
        );

        tbody.insertBefore(tr, tbody.firstChild);
        refreshTable();
        bootstrap.Modal.getInstance(addModalEl).hide();
    });

    addModalEl.addEventListener('hidden.bs.modal', () => {
        addForm.reset();
        addForm.classList.remove('was-validated');
    });
}
