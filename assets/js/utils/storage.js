/**
 * Generic browser storage helpers.
 */

export class StorageHelper {
    static get(key) {
        return JSON.parse(window.localStorage.getItem(key) || 'null');
    }

    static set(key, value) {
        window.localStorage.setItem(key, JSON.stringify(value));
    }

    static remove(key) {
        window.localStorage.removeItem(key);
    }
}
