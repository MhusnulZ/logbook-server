const STORAGE_KEY = 'logbook_server_logs';

export const DataManager = {
    getLogs() {
        const data = localStorage.getItem(STORAGE_KEY);
        return data ? JSON.parse(data) : [];
    },

    saveLog(log) {
        const logs = this.getLogs();
        const newLog = {
            id: Date.now(),
            status: 'INSIDE',
            timestampIn: new Date().toISOString(),
            timestampOut: null,
            ...log
        };
        logs.unshift(newLog);
        localStorage.setItem(STORAGE_KEY, JSON.stringify(logs));
        return newLog;
    },

    checkout(id) {
        const logs = this.getLogs();
        const updatedLogs = logs.map(log => {
            if (log.id === id) {
                return { ...log, status: 'CHECKED_OUT', timestampOut: new Date().toISOString() };
            }
            return log;
        });
        localStorage.setItem(STORAGE_KEY, JSON.stringify(updatedLogs));
    },

    getStats() {
        const logs = this.getLogs();
        const today = new Date().toDateString();
        
        const totalToday = logs.filter(log => new Date(log.timestampIn).toDateString() === today).length;
        const personnelInside = logs
            .filter(log => log.status === 'INSIDE')
            .reduce((sum, log) => {
                const q = parseInt(log.quantity);
                return sum + (isNaN(q) ? 1 : q);
            }, 0);
        
        return {
            totalToday,
            personnelInside: personnelInside.toString().padStart(2, '0')
        };
    }
};
