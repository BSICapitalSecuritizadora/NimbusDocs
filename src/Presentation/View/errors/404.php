<?php
http_response_code(404);
$pageTitle = 'Página não encontrada';
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
            max-width: 500px;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
            z-index: 10;
        }
        
        .ambient-glow {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(234, 179, 8, 0.08) 0%, rgba(0,0,0,0) 70%);
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
        
        .nd-btn-gold {
            background: linear-gradient(135deg, #d4a84b 0%, #b88a32 100%);
            border: none;
            color: #fff;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .nd-btn-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(212, 168, 75, 0.4);
            color: #fff;
        }
    </style>
</head>
<body>
    
    <div class="ambient-glow"></div>

    <div class="nd-glass-card">
        <div class="mb-4">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" 
                 style="width: 64px; height: 64px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                <i class="bi bi-signpost-split text-warning" style="font-size: 2rem;"></i>
            </div>
            <div class="error-code">404</div>
            <h1 class="h4 fw-bold text-white mb-2">Página não encontrada</h1>
            <p class="text-white-50 mb-4 px-4">
                O conteúdo que você procura pode ter sido movido ou não existe mais.
            </p>
        </div>

        <div class="d-flex flex-column gap-3">
            <a href="javascript:history.back()" class="btn nd-btn-gold py-3 rounded-3 fw-bold shadow-lg">
                <i class="bi bi-arrow-left me-2"></i> Voltar
            </a>
            <a href="/portal" class="btn btn-outline-light py-3 rounded-3 fw-medium border-opacity-25 hover-bg-light-10">
                <i class="bi bi-house me-2"></i> Ir para o Início
            </a>
        </div>
        
        <div class="mt-4 pt-4 border-top border-white-10">
             <code class="text-white-50 x-small bg-dark bg-opacity-50 px-2 py-1 rounded">
                <?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'URL desconhecida') ?>
             </code>
        </div>
    </div>

</body>
</html>
