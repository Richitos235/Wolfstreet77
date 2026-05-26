/**
 * Syndicate module is the entry point for guild system and faction UI.
 */

export class SyndicateModule {
    constructor(apiClient) {
        this.apiClient = apiClient;
    }

    async loadSyndicateDashboard() {
        return this.apiClient.get('/api/syndicate');
    }
}
