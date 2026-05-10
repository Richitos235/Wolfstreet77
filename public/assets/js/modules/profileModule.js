/**
 * Player profile module coordinates stats, wallet and inventory panels.
 */

export class ProfileModule {
    constructor(apiClient) {
        this.apiClient = apiClient;
    }

    async loadProfile() {
        return this.apiClient.get('/api/profile');
    }
}
