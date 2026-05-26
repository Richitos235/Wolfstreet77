/**
 * Game Time Module - Handles real-time clock and day display updates
 */

export class GameTimeModule {
    constructor(apiClient) {
        this.apiClient = apiClient;
        this.timeElement = document.querySelector('#gameTime');
        this.dayElement = document.querySelector('#gameDay');
        this.interval = null;
    }

    start() {
        this.update();
        this.interval = setInterval(() => this.update(), 5000);
    }

    async update() {
        try {
            const data = await this.apiClient.get('/api/game-time.php');
            if (data.success) {
                if (this.timeElement) {
                    this.timeElement.textContent = `ČAS: ${data.time}`;
                }
                if (this.dayElement) {
                    this.dayElement.textContent = `DEN ${data.day} / ${data.max_days}`;
                }
            }
        } catch (error) {
            console.error('Failed to update game time:', error);
        }
    }

    stop() {
        if (this.interval) {
            clearInterval(this.interval);
        }
    }
}