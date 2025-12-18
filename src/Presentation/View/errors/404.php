<?php
http_response_code(404);
$pageTitle = 'Página não encontrada (404)';
$contentView = __FILE__;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'NimbusDocs', ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .error-content {
            text-align: center;
            background: white;
            padding: 3rem;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 600px;
        }
        .error-code {
            font-size: 5rem;
            font-weight: bold;
            color: #667eea;
            margin: 0;
        }
        .error-message {
            font-size: 1.5rem;
            color: #333;
            margin: 1rem 0;
        }
        .error-description {
            color: #666;
            margin: 1.5rem 0;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-content">
            <h1 class="error-code">404</h1>
            <h2 class="error-message">Página não encontrada</h2>
            <p class="error-description">
                A página que você está tentando acessar não existe ou foi removida.
            </p>
            <p class="error-description">
                <strong>URL solicitada:</strong> <code><?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? '', ENT_QUOTES, 'UTF-8') ?></code>
            </p>
            <div class="mt-3">
                <a href="/" class="btn btn-primary me-2">Página inicial</a>
                <button class="btn btn-secondary" onclick="window.history.back()">Voltar</button>
            </div>
        </div>
    </div>
</body>
</html>
