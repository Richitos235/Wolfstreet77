/**
 * Core application bootstrap for UI initialization and service wiring.
 */

import { ApiClient } from '../api/apiClient.js';
import { TickManager } from '../game/tickManager.js';

const apiClient = new ApiClient();
const tickManager = new TickManager(apiClient);

window.WolfstreetApp = {
    apiClient,
    tickManager,
};

window.addEventListener('DOMContentLoaded', () => {
    tickManager.initialize();
});
