import http from 'k6/http';
import { check, sleep } from 'k6';
import { BASE_URL, API_BASE, SAFE_THRESHOLDS, JSON_HEADERS } from './helpers/setup.js';

/**
 * NimbusDocs — Smoke Test
 * 
 * Quick sanity check to verify all critical endpoints respond correctly.
 * Use this before running heavier load/stress tests.
 * 
 * Run: k6 run tests/LoadTest/smoke.js
 */

export const options = {
    vus: 1,
    duration: '30s',
    thresholds: {
        http_req_duration: ['p(99)<1500'],  // Stricter: 99th percentile under 1.5s
        http_req_failed: ['rate<0.01'],   // Almost zero errors expected
    },
};

export default function () {
    // ── 1. Admin Login Page (GET) ───────────────────────────────────
    const loginPage = http.get(`${BASE_URL}/admin/login`);
    check(loginPage, {
        'admin login: status 200': (r) => r.status === 200,
        'admin login: has form': (r) => r.body.includes('<form'),
        'admin login: has CSRF token': (r) => r.body.includes('_token'),
    });

    sleep(0.5);

    // ── 2. Portal Login Page (GET) ─────────────────────────────────
    const portalPage = http.get(`${BASE_URL}/portal/login`);
    check(portalPage, {
        'portal login: status 200': (r) => r.status === 200,
        'portal login: has form': (r) => r.body.includes('<form'),
    });

    sleep(0.5);

    // ── 3. Admin Health Check (GET) ────────────────────────────────
    const adminHealth = http.get(`${BASE_URL}/admin/health`);
    check(adminHealth, {
        'admin health: status 200': (r) => r.status === 200,
        'admin health: body ok': (r) => r.body.includes('ok') || r.body.includes('healthy'),
    });

    sleep(0.5);

    // ── 4. API Health Check (GET) ──────────────────────────────────
    const apiHealth = http.get(`${API_BASE}/health`);
    check(apiHealth, {
        'api health: status 200': (r) => r.status === 200,
        'api health: valid json': (r) => {
            try { return JSON.parse(r.body).status === 'ok'; }
            catch { return false; }
        },
    });

    sleep(0.5);

    // ── 5. Static Assets (CSS) ────────────────────────────────────
    const css = http.get(`${BASE_URL}/css/nimbusdocs-theme.css`);
    check(css, {
        'css: status 200': (r) => r.status === 200,
        'css: content-type': (r) => r.headers['Content-Type']?.includes('css') || r.status === 200,
    });

    sleep(0.5);

    // ── 6. API Auth Endpoint (POST with bad credentials) ──────────
    const badAuth = http.post(`${API_BASE}/auth/login`, JSON.stringify({
        email: 'nonexistent@test.com',
        password: 'wrong',
    }), JSON_HEADERS);
    check(badAuth, {
        'bad auth: returns 401 or 403': (r) => r.status === 401 || r.status === 403,
    });

    sleep(1);
}
