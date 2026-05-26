/**
 * Tick Countdown Module - Manages game tick countdown display and updates
 */

export class TickCountdown {
    constructor(elementSelector = '#tickCountdown', apiEndpoint = '/api/tick') {
        this.element = document.querySelector(elementSelector);
        this.apiEndpoint = apiEndpoint;
        this.isRunning = false;
    }

    start() {
        if (this.isRunning) return;
        
        this.isRunning = true;
        this.updateCountdown();
        setInterval(() => this.updateCountdown(), 1000);
    }

    async updateCountdown() {
        try {
            // For now, calculate locally based on server time
            // In the future, fetch from API
            const countdown = await this.getCountdown();
            this.display(countdown);
        } catch (error) {
            console.error('Error updating countdown:', error);
        }
    }

    async getCountdown() {
        // Placeholder - in production, fetch real countdown from API
        // For now, just update the display with stored time
        return 0;
    }

    display(seconds) {
        if (!this.element) return;

        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;

        const formatted = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        this.element.textContent = formatted;
    }
}
