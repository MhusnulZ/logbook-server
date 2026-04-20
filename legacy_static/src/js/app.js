import { DataManager } from './data.js';
import { UIManager } from './ui.js';

document.addEventListener('DOMContentLoaded', () => {
    // Initialize UI
    UIManager.renderAll();

    // Setup Manual Time Toggle
    const manualToggle = document.getElementById('manual-time-toggle');
    const manualFields = document.getElementById('manual-time-fields');
    
    manualToggle.addEventListener('change', () => {
        manualFields.style.display = manualToggle.checked ? 'block' : 'none';
        
        // Pre-fill with current date if checking
        if (manualToggle.checked) {
            const now = new Date();
            document.getElementById('manual-date').value = now.toISOString().split('T')[0];
            document.getElementById('manual-time-in').value = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
        }
    });

    // Setup Form Submission
    const logForm = document.getElementById('log-form');
    logForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const formData = {
            visitorName: document.getElementById('visitor-name').value,
            vendorName: document.getElementById('vendor-name').value,
            purpose: document.getElementById('purpose').value,
            quantity: parseInt(document.getElementById('quantity').value) || 1,
            description: document.getElementById('description').value
        };

        // Handle manual time
        if (manualToggle.checked) {
            const date = document.getElementById('manual-date').value;
            const timeIn = document.getElementById('manual-time-in').value;
            const timeOut = document.getElementById('manual-time-out').value;

            if (date && timeIn) {
                formData.timestampIn = `${date}T${timeIn}:00`;
            }
            if (date && timeOut) {
                formData.timestampOut = `${date}T${timeOut}:00`;
                formData.status = 'CHECKED_OUT';
            }
        }

        DataManager.saveLog(formData);
        logForm.reset();
        manualFields.style.display = 'none'; // Hide manual fields after reset
        UIManager.renderAll();
    });

    // View Switching
    document.getElementById('nav-dashboard').addEventListener('click', (e) => {
        e.preventDefault();
        UIManager.switchView('dashboard');
    });

    document.getElementById('nav-history').addEventListener('click', (e) => {
        e.preventDefault();
        UIManager.switchView('history');
    });

    // History Filters
    const filterSearch = document.getElementById('filter-search');
    const filterPurpose = document.getElementById('filter-purpose');
    const filterDate = document.getElementById('filter-date');
    const btnResetFilter = document.getElementById('btn-reset-filter');

    const handleFilter = () => {
        const filters = {
            search: filterSearch.value,
            purpose: filterPurpose.value,
            date: filterDate.value
        };
        UIManager.renderHistory(filters);
    };

    filterSearch.addEventListener('input', handleFilter);
    filterPurpose.addEventListener('change', handleFilter);
    filterDate.addEventListener('change', handleFilter);

    btnResetFilter.addEventListener('click', () => {
        filterSearch.value = '';
        filterPurpose.value = '';
        filterDate.value = '';
        handleFilter();
    });

    // CSV Export
    document.getElementById('btn-export-csv').addEventListener('click', () => {
        const logs = DataManager.getLogs();
        if (logs.length === 0) return alert('Tidak ada data untuk diekspor.');

        const headers = ['Tanggal', 'Pengunjung', 'Instansi', 'Qty', 'Tujuan', 'Masuk', 'Keluar', 'Status'];
        const csvRows = [headers.join(',')];

        logs.forEach(log => {
            const row = [
                new Date(log.timestampIn).toISOString().split('T')[0],
                `"${log.visitorName}"`,
                `"${log.vendorName}"`,
                log.quantity || 1,
                `"${log.purpose}"`,
                log.timestampIn ? new Date(log.timestampIn).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-',
                log.timestampOut ? new Date(log.timestampOut).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-',
                log.status
            ];
            csvRows.push(row.join(','));
        });

        const csvContent = "data:text/csv;charset=utf-8," + csvRows.join("\n");
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", `logbook_server_${new Date().toISOString().split('T')[0]}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    // Update time/date in header
    const updateHeaderMeta = () => {
        const now = new Date();
        const dateStr = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        const timeStr = now.toLocaleTimeString('id-ID', { hour12: false, hour: '2-digit', minute: '2-digit' });
        
        document.getElementById('current-date').textContent = dateStr;
        document.getElementById('current-time').textContent = timeStr;
    };

    updateHeaderMeta();
    setInterval(updateHeaderMeta, 60000); // Update every minute
});
