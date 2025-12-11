<?php
$tokenValue = $token['token'] ?? null;
$urlLogin   = $tokenValue
    ? "https://nimbusdocs.local/portal/access/{$tokenValue}"
    : "https://nimbusdocs.local/portal";
?>
<table width="100%" cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif;">
    <tr>
        <td align="center" style="padding: 40px;">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#fff; border-radius: 8px;">
                <tr>
                    <td style="padding: 30px;">
                        <h2 style="margin-top:0;color:#00205B;">Bem-vindo ao NimbusDocs</h2>

                        <p>Seu acesso ao portal <strong>NimbusDocs</strong> foi criado.</p>

                        <?php if ($tokenValue): ?>
                            <p>Utilize o botão abaixo para acessar o portal pela primeira vez:</p>
                            <p style="margin:30px 0;">
                                <a href="<?= $urlLogin ?>" style="padding:12px 20px; background:#00205B; color:#fff; text-decoration:none; border-radius:5px;">
                                    Acessar o portal
                                </a>
                            </p>
                        <?php else: ?>
                            <p>Entre em contato com a BSI para obter instruções de acesso.</p>
                        <?php endif; ?>

                        <p style="color:#888;font-size:12px;">Mensagem automática · Não responda este e-mail.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>