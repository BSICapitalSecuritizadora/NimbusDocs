<?php
http_response_code(500);
$pageTitle = 'Erro interno do servidor';
$branding = $config['branding'] ?? [];
$appName = $branding['app_name'] ?? 'NimbusDocs';

// Separate error message from trace
$errorMessage = '';
$errorTrace = '';
if (!empty($error)) {
    $parts = explode("\n\n", $error, 2);
    $errorMessage = trim($parts[0] ?? '');
    $errorTrace = trim($parts[1] ?? '');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle . ' - ' . $appName) ?></title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/css/nimbusdocs-theme.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background-color: #0a0e1a;
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, hsla(253,16%,7%,0) 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, hsla(225,39%,30%,0) 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, hsla(339,49%,30%,0) 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 1rem;
        }

        .nd-glass-card {
            background: rgba(255, 255, 255, 0.04); 
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 2.5rem;
            width: 100%;
            max-width: 560px;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
            z-index: 10;
            animation: fadeInUp 0.5s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .ambient-glow {
            position: fixed;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(220, 38, 38, 0.06) 0%, rgba(0,0,0,0) 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            z-index: 0;
        }

        .error-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: rgba(220, 38, 38, 0.1);
            border: 1px solid rgba(220, 38, 38, 0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .error-icon i {
            font-size: 1.5rem;
            color: #f87171;
        }

        .error-code {
            font-size: 5rem;
            font-weight: 800;
            line-height: 1;
            background: linear-gradient(135deg, #fff 0%, #ffffff60 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
            letter-spacing: -3px;
        }

        .error-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.5rem;
        }

        .error-subtitle {
            color: rgba(255,255,255,0.5);
            font-size: 0.875rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Error details - clean accordion style */
        .error-details {
            text-align: left;
            margin-bottom: 1.5rem;
        }

        .error-details-toggle {
            width: 100%;
            background: rgba(220, 38, 38, 0.06);
            border: 1px solid rgba(220, 38, 38, 0.12);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            color: rgba(255,255,255,0.6);
            font-family: 'Inter', sans-serif;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }

        .error-details-toggle:hover {
            background: rgba(220, 38, 38, 0.1);
            color: rgba(255,255,255,0.8);
        }

        .error-details-toggle i.bi-chevron-down {
            margin-left: auto;
            transition: transform 0.3s ease;
        }

        .error-details-toggle.active i.bi-chevron-down {
            transform: rotate(180deg);
        }

        .error-details-toggle.active {
            border-radius: 12px 12px 0 0;
        }

        .error-details-content {
            display: none;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(220, 38, 38, 0.12);
            border-top: none;
            border-radius: 0 0 12px 12px;
            padding: 1rem;
            overflow: auto;
            max-height: 200px;
        }

        .error-details-content.show { display: block; }

        .error-msg {
            font-family: 'JetBrains Mono', 'Fira Code', 'SF Mono', Consolas, monospace;
            font-size: 0.75rem;
            line-height: 1.7;
            color: #f87171;
            word-break: break-word;
            white-space: pre-wrap;
        }

        .error-trace {
            font-family: 'JetBrains Mono', 'Fira Code', 'SF Mono', Consolas, monospace;
            font-size: 0.65rem;
            line-height: 1.6;
            color: rgba(255,255,255,0.35);
            word-break: break-all;
            white-space: pre-wrap;
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px solid rgba(255,255,255,0.06);
        }

        /* Buttons */
        .nd-btn-danger-glow {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            border: none;
            color: #fff;
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0.875rem 1.5rem;
            border-radius: 12px;
            transition: all 0.25s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .nd-btn-danger-glow:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -6px rgba(220, 38, 38, 0.4);
            color: #fff;
        }

        .nd-btn-ghost {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.7);
            font-weight: 500;
            font-size: 0.8125rem;
            padding: 0.75rem 1.25rem;
            border-radius: 12px;
            transition: all 0.25s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .nd-btn-ghost:hover {
            background: rgba(255,255,255,0.08);
            border-color: rgba(255,255,255,0.15);
            color: #fff;
        }

        /* Scrollbar */
        .error-details-content::-webkit-scrollbar { width: 4px; }
        .error-details-content::-webkit-scrollbar-track { background: transparent; }
        .error-details-content::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 100px; }

        @media (max-width: 575px) {
            .nd-glass-card { padding: 2rem 1.5rem; }
            .error-code { font-size: 4rem; }
        }
    </style>
</head>
<body>
    
    <div class="ambient-glow"></div>

    <div class="nd-glass-card">
        <div class="error-icon">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>

        <div class="error-code">500</div>
        <h1 class="error-title">Erro Interno</h1>
        <p class="error-subtitle">
            Algo não saiu como esperado. Nossa equipe técnica já foi notificada.
        </p>
        
        <?php if (!empty($errorMessage)): ?>
            <div class="error-details">
                <button type="button" class="error-details-toggle" onclick="toggleDetails(this)">
                    <i class="bi bi-bug"></i>
                    Detalhes técnicos
                    <i class="bi bi-chevron-down"></i>
                </button>
                <div class="error-details-content">
                    <div class="error-msg"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
                    <?php if (!empty($errorTrace)): ?>
                        <div class="error-trace"><?= htmlspecialchars($errorTrace, ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="d-flex flex-column gap-2">
            <a href="javascript:location.reload()" class="nd-btn-danger-glow">
                <i class="bi bi-arrow-clockwise"></i> Tentar Novamente
            </a>
            <div class="d-flex gap-2">
                 <a href="javascript:history.back()" class="nd-btn-ghost flex-fill">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
                <a href="/admin/dashboard" class="nd-btn-ghost flex-fill">
                    <i class="bi bi-house"></i> Dashboard
                </a>
            </div>
        </div>
    </div>

    <script>
    function toggleDetails(btn) {
        const content = btn.nextElementSibling;
        btn.classList.toggle('active');
        content.classList.toggle('show');
    }
    </script>

</body>
</html>
