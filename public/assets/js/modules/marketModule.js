/**
 * Market module manages stock market data and charts.
 */

export class MarketModule {
    constructor(apiClient) {
        this.apiClient = apiClient;
    }

    async loadMarketOverview() {
        return this.apiClient.get('/api/market');
    }
}
