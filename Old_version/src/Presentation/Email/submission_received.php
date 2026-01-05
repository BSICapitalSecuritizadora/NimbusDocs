<?php
$userName = htmlspecialchars($user['full_name'] ?? 'Usuário');
$submissionId = (int)($submission['id'] ?? 0);
$submittedAt = htmlspecialchars($submission['submitted_at'] ?? date('Y-m-d H:i:s'));
$url = "https://nimbusdocs.local/portal/submissions/{$submissionId}";
?>

<table width="100%" cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <tr>
        <td align="center" style="padding: 40px;">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <tr>
                    <td style="padding: 30px;">
                        <h2 style="margin-top:0; color:#00205B; border-bottom: 3px solid #ffc20e; padding-bottom: 10px;">
                            ✅ Submissão Recebida
                        </h2>

                        <p style="color:#333; line-height: 1.6;">
                            Olá <strong><?= $userName ?></strong>,
                        </p>

                        <p style="color:#333; line-height: 1.6;">
                            Sua submissão foi recebida com sucesso no portal <strong>NimbusDocs</strong> e está sendo processada.
                        </p>

                        <div style="background: #f0f8ff; border-left: 4px solid #4CAF50; padding: 15px; margin: 20px 0;">
                            <p style="margin: 5px 0; color: #555;">
                                <strong>ID da Submissão:</strong> #<?= $submissionId ?>
                            </p>
                            <p style="margin: 5px 0; color: #555;">
                                <strong>Data/Hora:</strong> <?= $submittedAt ?>
                            </p>
                            <p style="margin: 5px 0; color: #555;">
                                <strong>Status:</strong> <span style="color: #ff9800;">Em análise</span>
                            </p>
                        </div>

                        <p style="color:#333; line-height: 1.6;">
                            Você receberá uma notificação por e-mail assim que houver atualizações sobre sua submissão.
                        </p>

                        <p style="margin:30px 0; text-align: center;">
                            <a href="<?= $url ?>"
                                style="display: inline-block; padding:12px 30px; background:#00205B; color:#fff; 
                                text-decoration:none; border-radius:5px; font-weight: bold;">
                                Acompanhar submissão
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
