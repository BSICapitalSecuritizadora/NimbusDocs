import http from 'k6/http';
import { check } from 'k6';

/**
 * NimbusDocs — k6 Load Test Helpers
 * 
 * Shared constants, authentication helpers, and utility functions
 * used across all load test scenarios.
 */

// ─────────────────────────────────────────────────────────────────────
// Configuration
// ─────────────────────────────────────────────────────────────────────
export const BASE_URL = __ENV.BASE_URL || 'http://nimbusdocs.local';
export const API_BASE = `${BASE_URL}/api/v1`;
export const ADMIN_EMAIL = __ENV.ADMIN_EMAIL || 'e2e@test.com';
export const ADMIN_PASS = __ENV.ADMIN_PASS || 'Pass123!';

// ─────────────────────────────────────────────────────────────────────
// Default Thresholds (safe limits for local XAMPP)
// ─────────────────────────────────────────────────────────────────────
export const SAFE_THRESHOLDS = {
    http_req_duration: ['p(95)<2000'],  // 95% of requests under 2s
    http_req_failed: ['rate<0.05'],   // Less than 5% errors
};

// ─────────────────────────────────────────────────────────────────────
// Common HTTP params
// ─────────────────────────────────────────────────────────────────────
export const JSON_HEADERS = {
    headers: { 'Content-Type': 'application/json' },
};

/**
 * Authenticates via the API and returns a JWT token.
 * @returns {string|null} JWT token or null on failure
 */
export function getJwtToken() {
    const res = http.post(`${API_BASE}/auth/login`, JSON.stringify({
        email: ADMIN_EMAIL,
        password: ADMIN_PASS,
    }), JSON_HEADERS);

    const ok = check(res, {
        'auth: status 200': (r) => r.status === 200,
        'auth: has token': (r) => {
            try { return JSON.parse(r.body).token !== undefined; }
            catch { return false; }
        },
    });

    if (ok) {
        return JSON.parse(res.body).token;
    }
    return null;
}

/**
 * Returns headers with Bearer authorization.
 * @param {string} token JWT token
 * @returns {object} k6 params object with Authorization header
 */
export function authHeaders(token) {
    return {
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
        },
    };
}
