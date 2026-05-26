/**
 * Lightweight API client for REST and future websocket integration.
 */

export class ApiClient {
    constructor(baseUrl = window.location.origin) {
        this.baseUrl = baseUrl;
    }

    async get(path) {
        // Ensure path starts with / if it doesn't
        const cleanPath = path.startsWith('/') ? path : `/${path}`;
        const response = await fetch(`${this.baseUrl}${cleanPath}`, {
            credentials: 'include',
        });
        return response.json();
    }

    async post(path, body) {
        const cleanPath = path.startsWith('/') ? path : `/${path}`;
        const response = await fetch(`${this.baseUrl}${cleanPath}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
            body: JSON.stringify(body),
        });
        return response.json();
    }
}