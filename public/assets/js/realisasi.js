document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('rekapTable');
    if (!table) return;

    // --- BAGIAN 1: Fungsionalitas Tree View (Buka/Tutup Baris) ---
    function hideAllChildren(parentId) {
        const children = table.querySelectorAll(`tbody tr[data-parent-id="${parentId}"]`);
        children.forEach(child => {
            child.style.display = 'none';
            const childToggleIcon = child.querySelector('.tree-toggle i');
            if (childToggleIcon && childToggleIcon.classList.contains('bi-chevron-right')) {
                childToggleIcon.classList.remove('bi-chevron-right');
                childToggleIcon.classList.add('bi-chevron-down');
            }
            hideAllChildren(child.dataset.id);
        });
    }

    // Sembunyikan semua anak saat halaman dimuat
    table.querySelectorAll('tbody tr[data-parent-id]').forEach(row => {
        row.style.display = 'none';
    });

    table.addEventListener('click', function(e) {
        const toggleLink = e.target.closest('.tree-toggle');
        if (toggleLink) {
            e.preventDefault();
            const parentRow = toggleLink.closest('tr');
            const parentId = parentRow.dataset.id;
            const icon = toggleLink.querySelector('i');
            const childRows = table.querySelectorAll(`tbody tr[data-parent-id="${parentId}"]`);

            const isOpening = icon.classList.contains('bi-chevron-down');
            icon.classList.toggle('bi-chevron-down', !isOpening);
            icon.classList.toggle('bi-chevron-right', isOpening);

            childRows.forEach(child => {
                child.style.display = isOpening ? 'table-row' : 'none';
                if (!isOpening) {
                    hideAllChildren(child.dataset.id);
                }
            });
        }
    });


    // --- BAGIAN 2: Fungsionalitas Visibilitas Kolom (Bulan) ---
    const controlsContainer = document.getElementById('bulan-toggle-controls');
    const toggleAllBtn = document.getElementById('toggle-all-months');

    if (controlsContainer) {
        controlsContainer.addEventListener('click', function(e) {
            if (e.target.tagName === 'BUTTON') {
                const button = e.target;
                const bulan = button.dataset.bulan;
                button.classList.toggle('active');
                
                const isActive = button.classList.contains('active');
                const displayValue = isActive ? '' : 'none';

                const cellsToToggle = table.querySelectorAll(`th[data-bulan="${bulan}"], td[data-bulan="${bulan}"]`);
                cellsToToggle.forEach(cell => {
                    cell.style.display = displayValue;
                });
            }
        });
    }
    
    if (toggleAllBtn) {
        toggleAllBtn.addEventListener('click', function() {
            const allMonthButtons = controlsContainer.querySelectorAll('button');
            if (!allMonthButtons.length) return;

            const shouldBecomeActive = !allMonthButtons[0].classList.contains('active');
            allMonthButtons.forEach(button => {
                if (button.classList.contains('active') !== shouldBecomeActive) {
                    button.click();
                }
            });
        });
    }
});