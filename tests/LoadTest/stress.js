import http from 'k6/http';
import { check, sleep, group } from 'k6';
import { BASE_URL, API_BASE, JSON_HEADERS } from './helpers/setup.js';

/**
 * NimbusDocs — Stress Test
 * 
 * Progressively increases load to find the application's breaking point.
 * Ramping from 1 → 30 virtual users, then holding, then spiking to 50.
 * 
 * ⚠️  WARNING: This test is designed to push limits.
 *     Run ONLY against local (XAMPP) or staging environments.
 *     NEVER run against production.
 * 
 * Run: k6 run tests/LoadTest/stress.js
 */

export const options = {
    stages: [
        // Warm-up
        { duration: '20s', target: 5 },
        // Normal load
        { duration: '30s', target: 10 },
        // Stress phase
        { duration: '30s', target: 20 },
        // Peak stress
        { duration: '30s', target: 30 },
        // Spike (brief burst)
        { duration: '10s', target: 50 },
        // Recovery (observe how the system recovers)
        { duration: '30s', target: 10 },
        // Cool-down
        { duration: '10s', target: 0 },
    ],
    thresholds: {
        // More lenient thresholds for stress testing
        http_req_duration: ['p(95)<5000'],  // 95% under 5s (we expect degradation)
        http_req_failed: ['rate<0.15'],   // Up to 15% errors acceptable under stress
    },
};

export default function () {
    // ── Mixed workload simulating diverse user behavior ────────────

    const scenario = Math.random();

    if (scenario < 0.4) {
        // 40%: Browse login pages
        group('Login Pages', () => {
            const target = Math.random() < 0.5
                ? `${BASE_URL}/admin/login`
                : `${BASE_URL}/portal/login`;

            const res = http.get(target);
            check(res, {
                'login page: status OK': (r) => r.status === 200,
            });
        });

    } else if (scenario < 0.65) {
        // 25%: API health check
        group('API Health', () => {
            const res = http.get(`${API_BASE}/health`);
            check(res, {
                'health: status 200': (r) => r.status === 200,
            });
        });

    } else if (scenario < 0.85) {
        // 20%: Static assets (parallel batch)
        group('Static Assets Batch', () => {
            const responses = http.batch([
                ['GET', `${BASE_URL}/css/nimbusdocs-theme.css`],
                ['GET', `${BASE_URL}/assets/vendor/bootstrap/bootstrap.min.css`],
                ['GET', `${BASE_URL}/assets/vendor/bootstrap-icons/bootstrap-icons.min.css`],
                ['GET', `${BASE_URL}/assets/fonts/fonts.css`],
            ]);

            for (const res of responses) {
                check(res, {
                    'asset: loaded': (r) => r.status === 200,
                });
            }
        });

    } else {
        // 15%: API authentication attempts (simulates login pressure)
        group('API Auth Pressure', () => {
            const res = http.post(`${API_BASE}/auth/login`, JSON.stringify({
                email: `stress_${__VU}_${__ITER}@test.com`,
                password: 'FakePassword1!',
            }), JSON_HEADERS);

            check(res, {
                'auth: responds (any status)': (r) => r.status > 0,
            });
        });
    }

    // Think time — randomized to avoid thundering herd
    sleep(Math.random() * 2 + 0.5);
}
