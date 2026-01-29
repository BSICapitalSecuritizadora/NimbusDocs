<?php
http_response_code(500);
$pageTitle = 'Erro interno do servidor';
$branding = $config['branding'] ?? [];
$appName = $branding['app_name'] ?? 'NimbusDocs';
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
        body {
            background-color: var(--nd-navy-900);
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, hsla(253,16%,7%,0) 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, hsla(225,39%,30%,0) 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, hsla(339,49%,30%,0) 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
            margin: 0;
        }

        .nd-glass-card {
            background: rgba(255, 255, 255, 0.03); 
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 3rem;
            width: 100%;
            max-width: 600px;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
            z-index: 10;
        }
        
        .ambient-glow {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(220, 38, 38, 0.08) 0%, rgba(0,0,0,0) 70%); /* Red glow for error */
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            z-index: 0;
        }

        .error-code {
            font-size: 6rem;
            font-weight: 800;
            line-height: 1;
            background: linear-gradient(135deg, #fff 0%, #ffffff80 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
            letter-spacing: -3px;
        }
        
        .nd-btn-danger {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            border: none;
            color: #fff;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .nd-btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(220, 38, 38, 0.4);
            color: #fff;
        }
    </style>
</head>
<body>
    
    <div class="ambient-glow"></div>

    <div class="nd-glass-card">
        <div class="mb-4">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" 
                 style="width: 64px; height: 64px; background: rgba(220, 38, 38, 0.1); border: 1px solid rgba(220, 38, 38, 0.2);">
                <i class="bi bi-exclamation-triangle-fill text-danger shadow-sm" style="font-size: 2rem;"></i>
            </div>
            <div class="error-code">500</div>
            <h1 class="h4 fw-bold text-white mb-2">Erro Interno</h1>
            <p class="text-white-50 mb-4 px-4">
                Encontramos uma inconsistência ao processar sua solicitação.
                Nossa equipe técnica já foi notificada.
            </p>
            
            <?php if (!empty($error)): ?>
                <div class="text-start bg-black bg-opacity-25 p-3 rounded-3 mb-4 overflow-auto border border-white-10" style="max-height: 150px; font-size: 0.8rem;">
                    <div class="d-flex align-items-center gap-2 mb-2 text-danger-emphasis">
                        <i class="bi bi-bug"></i>
                        <strong class="text-uppercase x-small ls-1">Detalhes do Erro</strong>
                    </div>
                    <code class="text-white-50" style="white-space: pre-wrap;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></code>
                </div>
            <?php endif; ?>
        </div>

        <div class="d-flex flex-column gap-3">
            <a href="javascript:location.reload()" class="btn nd-btn-danger py-3 rounded-3 fw-bold shadow-lg">
                <i class="bi bi-arrow-clockwise me-2"></i> Tentar Novamente
            </a>
            <div class="d-flex gap-3">
                 <a href="javascript:history.back()" class="btn btn-outline-light w-50 py-3 rounded-3 fw-medium border-opacity-25 hover-bg-light-10">
                    <i class="bi bi-arrow-left me-2"></i> Voltar
                </a>
                <a href="/portal" class="btn btn-outline-light w-50 py-3 rounded-3 fw-medium border-opacity-25 hover-bg-light-10">
                    <i class="bi bi-house me-2"></i> Início
                </a>
            </div>
        </div>
    </div>

</body>
</html>
