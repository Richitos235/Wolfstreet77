/**
 * Game tick manager orchestrates client-side scheduling and event polling.
 */

export class TickManager {
    constructor(apiClient) {
        this.apiClient = apiClient;
        this.interval = 60000;
    }

    initialize() {
        this.pollGameTick();
        setInterval(() => this.pollGameTick(), this.interval);
    }

    async pollGameTick() {
        await this.apiClient.get('/api/health');
    }
}
