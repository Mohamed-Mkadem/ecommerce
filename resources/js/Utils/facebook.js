const DEFAULT_CURRENCY = "TND";

/**
 * Safe helper to emit Meta Pixel events.
 *
 * @param {string} eventName - Pixel event name (e.g. AddToCart).
 * @param {object} customData - Event payload passed to fbq.
 * @param {object} options - Extra options, currently supports eventID.
 */
export function trackFacebookEvent(eventName, customData = {}, options = {}) {
    if (typeof window === "undefined" || typeof window.fbq !== "function") {
        if (import.meta.env.DEV) {
            console.warn(
                `trackFacebookEvent: fbq is not available for ${eventName}`,
            );
        }
        return;
    }

    const payload = {
        currency: DEFAULT_CURRENCY,
        ...customData,
    };

    const eventID =
        options.eventID ??
        `${eventName.toLowerCase()}-${Date.now()}-${Math.floor(Math.random() * 1000)}`;

    try {
        window.fbq("track", eventName, payload, { eventID });
    } catch (error) {
        if (import.meta.env.DEV) {
            console.error(`trackFacebookEvent failed for ${eventName}`, error);
        }
    }
}
