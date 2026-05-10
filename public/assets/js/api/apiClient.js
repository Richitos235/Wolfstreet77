/**
 * Lightweight API client for REST and future websocket integration.
 */

export class ApiClient {
    constructor(baseUrl = window.location.origin) {
        this.baseUrl = baseUrl;
    }

    async get(path) {
        const response = await fetch(`${this.baseUrl}${path}`, {
            credentials: 'include',
        });
        return response.json();
    }

    async post(path, body) {
        const response = await fetch(`${this.baseUrl}${path}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
            body: JSON.stringify(body),
        });
        return response.json();
    }
}
