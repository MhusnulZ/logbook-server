import { DataManager } from './data.js';

export const UIManager = {
    renderStats() {
        const stats = DataManager.getStats();
        document.getElementById('total-access').textContent = stats.totalToday;
        document.getElementById('personnel-inside').textContent = stats.personnelInside;
    },

    formatTime(isoString) {
        if (!isoString) return '-';
        const date = new Date(isoString);
        return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    },

    renderLogs() {
        const logs = DataManager.getLogs();
        const container = document.getElementById('activity-list');
        if (!container) return;
        container.innerHTML = '';

        if (logs.length === 0) {
            container.innerHTML = '<p style="text-align:center; color: var(--text-muted); padding: 2rem;">Belum ada aktivitas.</p>';
            return;
        }

        logs.forEach(log => {
            const item = document.createElement('div');
            item.className = 'activity-item';
            
            const isInside = log.status === 'INSIDE';
            
            item.innerHTML = `
                <div class="item-header">
                    <div class="item-main-info">
                        <span class="visitor-name">${log.visitorName}</span>
                        <div style="display:flex; align-items:center; gap:4px; font-size: 0.75rem; color: var(--primary-color); background: var(--primary-light); padding: 2px 8px; border-radius: 10px; font-weight: 600;">
                            <i data-lucide="users" style="width:12px"></i>
                            ${log.quantity || 1} Orang
                        </div>
                        <span class="vendor-tag">${log.vendorName}</span>
                        <span class="badge ${isInside ? 'inside' : 'out'}">${isInside ? 'INSIDE' : 'OUT'}</span>
                        <span class="vendor-tag" style="margin-left: auto;">${new Date(log.timestampIn).toLocaleDateString()}</span>
                    </div>
                </div>
                <div class="item-content">
                    <strong>${log.purpose}</strong><br>
                    <span style="font-style: italic; color: #94a3b8;">"${log.description}"</span>
                </div>
                <div class="item-footer">
                    <div class="time-slot" style="color: #64748b;">
                        <i data-lucide="clock" style="width:14px"></i>
                        MASUK: <span style="color: var(--text-main); font-weight: 600;">${this.formatTime(log.timestampIn)}</span>
                    </div>
                    <div class="time-slot" style="color: #64748b;">
                        <i data-lucide="clock" style="width:14px"></i>
                        KELUAR: <span style="color: var(--primary-color); font-weight: 600;">${this.formatTime(log.timestampOut)}</span>
                    </div>
                </div>
                ${isInside ? `<button class="checkout-btn" data-id="${log.id}">Checkout</button>` : ''}
            `;
            
            container.appendChild(item);
        });

        if (window.lucide) window.lucide.createIcons();

        document.querySelectorAll('.checkout-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = parseInt(e.target.dataset.id);
                DataManager.checkout(id);
                this.renderAll();
            });
        });
    },

    renderHistory(filters = {}) {
        let logs = DataManager.getLogs();
        const tbody = document.getElementById('history-table-body');
        if (!tbody) return;
        tbody.innerHTML = '';

        // Apply filters
        if (filters.search) {
            const query = filters.search.toLowerCase();
            logs = logs.filter(l => 
                l.visitorName.toLowerCase().includes(query) || 
                l.vendorName.toLowerCase().includes(query)
            );
        }
        if (filters.purpose) {
            logs = logs.filter(l => l.purpose === filters.purpose);
        }
        if (filters.date) {
            logs = logs.filter(l => new Date(l.timestampIn).toISOString().split('T')[0] === filters.date);
        }

        document.getElementById('history-count').textContent = `Total: ${logs.length} Data ditemukan`;

        if (logs.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; padding: 3rem; color: var(--text-muted);">Tidak ada data riwayat yang ditemukan.</td></tr>';
            return;
        }

        logs.forEach(log => {
            const tr = document.createElement('tr');
            const isInside = log.status === 'INSIDE';
            
            tr.innerHTML = `
                <td style="font-size: 0.875rem; color: var(--text-muted);">${new Date(log.timestampIn).toISOString().split('T')[0]}</td>
                <td>
                    <div style="font-weight:700;">${log.visitorName}</div>
                    <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase;">${log.vendorName}</div>
                </td>
                <td><span class="qty-pill">${log.quantity || 1}</span></td>
                <td>
                    <div style="font-weight:600; font-size: 0.9rem;">${log.purpose}</div>
                    <div style="font-size:0.8rem; color: #94a3b8; font-style: italic;">"${log.description}"</div>
                </td>
                <td style="font-weight: 600;">${this.formatTime(log.timestampIn)}</td>
                <td style="font-weight: 600; color: var(--primary-color);">${this.formatTime(log.timestampOut)}</td>
                <td><span class="badge ${isInside ? 'inside' : 'out'}">${isInside ? 'INSIDE' : 'OUT'}</span></td>
            `;
            tbody.appendChild(tr);
        });
    },

    switchView(viewId) {
        document.querySelectorAll('.view-container').forEach(v => v.style.display = 'none');
        document.getElementById(`${viewId}-view`).style.display = 'block';

        // Update sidebar
        document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));
        if (viewId === 'dashboard') document.getElementById('nav-dashboard').classList.add('active');
        if (viewId === 'history') document.getElementById('nav-history').classList.add('active');

        // Update Header Titles
        if (viewId === 'dashboard') {
            document.getElementById('page-title').textContent = 'Log Book Digital Server Room';
            document.getElementById('page-subtitle').textContent = 'Pencatatan Akses Keluar/Masuk Ruang Server BBHIPMM';
        } else {
            document.getElementById('page-title').textContent = 'Data Riwayat Akses';
            document.getElementById('page-subtitle').textContent = 'Arsip lengkap log aktivitas server room';
        }

        this.renderAll();
    },

    renderAll() {
        this.renderStats();
        const dashboardVisible = document.getElementById('dashboard-view').style.display !== 'none';
        if (dashboardVisible) {
            this.renderLogs();
        } else {
            // Get current filters
            const filters = {
                search: document.getElementById('filter-search')?.value || '',
                purpose: document.getElementById('filter-purpose')?.value || '',
                date: document.getElementById('filter-date')?.value || ''
            };
            this.renderHistory(filters);
        }
    }
};
