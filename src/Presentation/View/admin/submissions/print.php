<?php
/** @var array $submissions */
/** @var string $pageTitle */

// Helpers para exibição
function formatStatus($status) {
    return match($status) {
        'PENDING' => 'Pendente',
        'IN_REVIEW', 'UNDER_REVIEW' => 'Em Análise',
        'COMPLETED', 'APPROVED' => 'Concluída',
        'REJECTED' => 'Rejeitada',
        default => $status
    };
}

function getStatusColor($status) {
    return match($status) {
        'PENDING' => '#f59e0b',
        'IN_REVIEW', 'UNDER_REVIEW' => '#3b82f6',
        'COMPLETED', 'APPROVED' => '#10b981',
        'REJECTED' => '#ef4444',
        default => '#6b7280'
    };
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #1a2942;
            border-bottom: 2px solid #ccc;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .meta {
            margin-bottom: 20px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
            color: #1a2942;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            color: white;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
    </style>
</head>
<body onload="window.print()">

    <h1>NimbusDocs - Relatório de Submissões</h1>
    
    <div class="meta">
        Gerado em: <?= date('d/m/Y H:i') ?><br>
        Total de registros: <?= count($submissions) ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>Protocolo</th>
                <th>Data</th>
                <th>Assunto/Título</th>
                <th>Solicitante</th>
                <th>CNPJ/Empresa</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($submissions as $s): ?>
                <tr>
                    <td style="font-family: monospace;"><?= htmlspecialchars($s['reference_code'] ?? '-') ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($s['submitted_at'])) ?></td>
                    <td><?= htmlspecialchars($s['title'] ?? '') ?></td>
                    <td>
                        <?= htmlspecialchars($s['user_name'] ?? '') ?><br>
                        <small style="color:#666"><?= htmlspecialchars($s['user_email'] ?? '') ?></small>
                    </td>
                    <td>
                        <?= htmlspecialchars($s['company_name'] ?? 'N/A') ?><br>
                        <small style="color:#666"><?= htmlspecialchars($s['company_cnpj'] ?? '') ?></small>
                    </td>
                    <td>
                        <span class="status-badge" style="background-color: <?= getStatusColor($s['status'] ?? '') ?>">
                            <?= formatStatus($s['status'] ?? '') ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="text-align: center; color: #999; margin-top: 40px; font-size: 10px; border-top: 1px solid #eee; padding-top: 10px;">
        NimbusDocs - Sistema de Gestão de Documentos
    </div>

</body>
</html>
