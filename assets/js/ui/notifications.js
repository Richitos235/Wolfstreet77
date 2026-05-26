/**
 * Notification component manages user alerts and future realtime updates.
 */

export class Notifications {
    constructor(containerSelector = '#notification-area') {
        this.container = document.querySelector(containerSelector);
    }

    show(message, type = 'info') {
        if (!this.container) {
            console.warn('Notification container not found');
            return;
        }

        const element = document.createElement('div');
        element.className = `notification notification--${type}`;
        element.textContent = message;
        this.container.appendChild(element);

        setTimeout(() => element.remove(), 5000);
    }
}
