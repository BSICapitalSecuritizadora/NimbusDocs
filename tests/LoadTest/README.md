# 🔥 NimbusDocs — Load Testing (k6)

Testes de carga usando [k6](https://k6.io) by Grafana Labs.

## Pré-requisitos

```bash
# Instalar k6 (Windows)
choco install k6
# ou via winget:
winget install k6
# ou download: https://github.com/grafana/k6/releases
```

## Testes Disponíveis

| Teste | VUs | Duração | Objetivo |
|-------|-----|---------|----------|
| **Smoke** | 1 | 30s | Verificar se todos os endpoints respondem corretamente |
| **Load** | 5→10 | 2.5min | Simular tráfego sustentado de operação normal |
| **Stress** | 5→50 | 2.5min | Encontrar o ponto de ruptura da aplicação |

## Como Usar

```bash
# 1. Smoke Test (sempre execute primeiro!)
k6 run tests/LoadTest/smoke.js

# 2. Load Test (tráfego normal sustentado)
k6 run tests/LoadTest/load.js

# 3. Stress Test (⚠️ apenas local/staging)
k6 run tests/LoadTest/stress.js
```

### Apontar para outro servidor

```bash
k6 run -e BASE_URL=http://localhost:8080 tests/LoadTest/smoke.js
```

## Thresholds (Limites de Segurança)

| Métrica | Smoke | Load | Stress |
|---------|-------|------|--------|
| Response time (p95) | < 1.5s | < 2s | < 5s |
| Error rate | < 1% | < 5% | < 15% |

Se algum threshold for violado, o k6 reporta **FAIL** e retorna exit code 99.

## Interpretando Resultados

```
✓ admin login: status 200     ← Cada check mostra pass/fail
✗ api auth: responds           ← ✗ indica falha

http_req_duration...........: avg=142ms  min=12ms  max=1.2s  p(95)=450ms
http_req_failed.............: 0.00%      ← Taxa de erro geral
http_reqs...................: 1247       ← Total de requests feitos
vus.........................: 10         ← Usuários virtuais ativos
```

## ⚠️ Regras de Segurança

1. **NUNCA** execute contra produção sem aprovação explícita
2. **Sempre** execute o `smoke.js` antes de testes mais pesados
3. O XAMPP suporta ~10-20 VUs confortavelmente. Acima disso, espere degradação
4. Se o Apache travar, reinicie via XAMPP Control Panel
