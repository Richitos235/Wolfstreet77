/**
 * Core application bootstrap for UI initialization and service wiring.
 */

import { ApiClient } from '../api/apiClient.js';
import { TickManager } from '../game/tickManager.js';
import { GameTimeModule } from '../modules/gameTimeModule.js';

const apiClient = new ApiClient();
const tickManager = new TickManager(apiClient);
const gameTimeModule = new GameTimeModule(apiClient);

window.WolfstreetApp = {
    apiClient,
    tickManager,
    gameTimeModule
};

window.addEventListener('DOMContentLoaded', () => {
    tickManager.initialize();
    gameTimeModule.start();
});