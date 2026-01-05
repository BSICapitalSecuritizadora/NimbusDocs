<?php
$userName = htmlspecialchars($user['full_name'] ?? 'Usuário');
$submissionId = (int)($submission['id'] ?? 0);
$oldStatus = htmlspecialchars($submission['old_status'] ?? '');
$newStatus = htmlspecialchars($submission['status'] ?? '');
$statusUpdatedAt = htmlspecialchars($submission['status_updated_at'] ?? date('Y-m-d H:i:s'));
$url = "https://nimbusdocs.local/portal/submissions/{$submissionId}";

// Define cores e textos baseados no status
$statusConfig = [
    'PENDING' => ['color' => '#ff9800', 'text' => 'Pendente', 'icon' => '⏳'],
    'UNDER_REVIEW' => ['color' => '#2196F3', 'text' => 'Em análise', 'icon' => '🔍'],
    'COMPLETED' => ['color' => '#4CAF50', 'text' => 'Finalizada', 'icon' => '✅'],
    'REJECTED' => ['color' => '#f44336', 'text' => 'Rejeitada', 'icon' => '❌'],
];

$statusInfo = $statusConfig[$newStatus] ?? ['color' => '#999', 'text' => $newStatus, 'icon' => 'ℹ️'];
$statusColor = $statusInfo['color'];
$statusText = $statusInfo['text'];
$statusIcon = $statusInfo['icon'];
?>

<table width="100%" cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <tr>
        <td align="center" style="padding: 40px;">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <tr>
                    <td style="padding: 30px;">
                        <h2 style="margin-top:0; color:#00205B; border-bottom: 3px solid #ffc20e; padding-bottom: 10px;">
                            🔔 Atualização de Status
                        </h2>

                        <p style="color:#333; line-height: 1.6;">
                            Olá <strong><?= $userName ?></strong>,
                        </p>

                        <p style="color:#333; line-height: 1.6;">
                            O status da sua submissão no portal <strong>NimbusDocs</strong> foi atualizado.
                        </p>

                        <div style="background: #f9f9f9; border-left: 4px solid <?= $statusColor ?>; padding: 15px; margin: 20px 0;">
                            <p style="margin: 5px 0; color: #555;">
                                <strong>ID da Submissão:</strong> #<?= $submissionId ?>
                            </p>
                            <p style="margin: 5px 0; color: #555;">
                                <strong>Data da atualização:</strong> <?= $statusUpdatedAt ?>
                            </p>
                            <p style="margin: 15px 0 5px 0; color: #555;">
                                <strong>Novo Status:</strong>
                            </p>
                            <p style="margin: 5px 0;">
                                <span style="display: inline-block; padding: 8px 16px; background: <?= $statusColor ?>; 
                                      color: #fff; border-radius: 4px; font-weight: bold; font-size: 16px;">
                                    <?= $statusIcon ?> <?= $statusText ?>
                                </span>
                            </p>
                        </div>

                        <?php if ($newStatus === 'COMPLETED'): ?>
                            <div style="background: #e8f5e9; padding: 15px; border-radius: 5px; margin: 20px 0;">
                                <p style="margin: 0; color: #2e7d32; font-weight: bold;">
                                    ✨ Parabéns! Sua submissão foi finalizada com sucesso.
                                </p>
                            </div>
                        <?php elseif ($newStatus === 'REJECTED'): ?>
                            <div style="background: #ffebee; padding: 15px; border-radius: 5px; margin: 20px 0;">
                                <p style="margin: 0; color: #c62828;">
                                    ⚠️ Sua submissão foi rejeitada. Verifique os detalhes na plataforma.
                                </p>
                            </div>
                        <?php endif; ?>

                        <p style="color:#333; line-height: 1.6;">
                            Para mais detalhes, acesse a plataforma e visualize sua submissão.
                        </p>

                        <p style="margin:30px 0; text-align: center;">
                            <a href="<?= $url ?>"
                                style="display: inline-block; padding:12px 30px; background:#00205B; color:#fff; 
                                text-decoration:none; border-radius:5px; font-weight: bold;">
                                Ver detalhes da submissão
                            </a>
                        </p>

                        <p style="color:#888; font-size:12px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0;">
                            Esta é uma mensagem automática do sistema NimbusDocs. Por favor, não responda.
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
