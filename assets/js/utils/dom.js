/**
 * DOM helpers for component rendering and event wiring.
 */

export class DomHelper {
    static qs(selector, context = document) {
        return context.querySelector(selector);
    }

    static qsa(selector, context = document) {
        return Array.from(context.querySelectorAll(selector));
    }
}
