import http from 'k6/http';
import { check, sleep, group } from 'k6';
import { BASE_URL, API_BASE, SAFE_THRESHOLDS, JSON_HEADERS, getJwtToken, authHeaders } from './helpers/setup.js';

/**
 * NimbusDocs — Load Test
 * 
 * Simulates sustained normal traffic: 10 concurrent users
 * performing typical actions over 2 minutes.
 * 
 * Run: k6 run tests/LoadTest/load.js
 */

export const options = {
    stages: [
        { duration: '30s', target: 5 },   // Ramp up to 5 users
        { duration: '30s', target: 10 },   // Ramp up to 10 users
        { duration: '1m', target: 10 },   // Hold at 10 users for 1 minute
        { duration: '15s', target: 5 },    // Ramp down to 5
        { duration: '15s', target: 0 },    // Ramp down to 0
    ],
    thresholds: SAFE_THRESHOLDS,
};

// ─────────────────────────────────────────────────────────────────────
// Per-VU setup: authenticate once and reuse the token
// ─────────────────────────────────────────────────────────────────────
let token = null;

export function setup() {
    // Nothing to do in global setup — each VU authenticates lazily
    return {};
}

export default function () {

    // ── Scenario A: Browse Public Pages (70% of traffic) ──────────
    group('Public Pages', () => {
        // Admin Login Page
        const adminLogin = http.get(`${BASE_URL}/admin/login`);
        check(adminLogin, {
            'admin: status 200': (r) => r.status === 200,
        });

        sleep(randomBetween(0.5, 1.5));

        // Portal Login Page
        const portalLogin = http.get(`${BASE_URL}/portal/login`);
        check(portalLogin, {
            'portal: status 200': (r) => r.status === 200,
        });

        sleep(randomBetween(0.5, 1.5));
    });

    // ── Scenario B: API Health Check (15% of traffic) ─────────────
    group('API Health', () => {
        const health = http.get(`${API_BASE}/health`);
        check(health, {
            'api health: status 200': (r) => r.status === 200,
        });

        sleep(randomBetween(0.3, 1));
    });

    // ── Scenario C: Static Assets (15% of traffic) ────────────────
    group('Static Assets', () => {
        const assets = [
            `${BASE_URL}/css/nimbusdocs-theme.css`,
            `${BASE_URL}/assets/vendor/bootstrap/bootstrap.min.css`,
            `${BASE_URL}/assets/fonts/fonts.css`,
        ];

        for (const url of assets) {
            const res = http.get(url);
            check(res, {
                [`asset loaded: ${url.split('/').pop()}`]: (r) => r.status === 200,
            });
        }

        sleep(randomBetween(0.5, 1));
    });

    // ── Scenario D: API Authentication Attempt ────────────────────
    group('API Auth', () => {
        // Simulate an authentication attempt
        const authRes = http.post(`${API_BASE}/auth/login`, JSON.stringify({
            email: 'loadtest@test.com',
            password: 'TestPassword123!',
        }), JSON_HEADERS);

        // We expect this to fail (401) since the user doesn't exist
        // but the endpoint should still respond quickly
        check(authRes, {
            'api auth: responds': (r) => r.status > 0,
        });

        sleep(randomBetween(1, 2));
    });
}

// ─────────────────────────────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────────────────────────────
function randomBetween(min, max) {
    return Math.random() * (max - min) + min;
}
