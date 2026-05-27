/**
 * Format angka ribuan menjadi K (misal: 1500 -> 1.5K)
 */
export const formatNumber = (num) => {
    if (!num) return "0";
    if (num >= 1000) return (num / 1000).toFixed(1) + "K";
    return num;
};

/**
 * Validasi URL untuk mencegah serangan XSS
 */
export const isSafeUrl = (url) => {
    try {
        const u = new URL(url, window.location.origin);
        return ["http:", "https:"].includes(u.protocol);
    } catch {
        return false;
    }
};
