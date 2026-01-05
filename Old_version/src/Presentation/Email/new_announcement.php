<?php
$title = htmlspecialchars($announcement['title'] ?? 'Novo aviso');
$content = $announcement['content'] ?? '';
$url = "https://nimbusdocs.local/portal/announcements";
?>

<table width="100%" cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <tr>
        <td align="center" style="padding: 40px;">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <tr>
                    <td style="padding: 30px;">
                        <h2 style="margin-top:0; color:#00205B; border-bottom: 3px solid #ffc20e; padding-bottom: 10px;">
                            📢 Novo Aviso
                        </h2>

                        <p style="color:#333; line-height: 1.6;">
                            Olá! Um novo aviso foi publicado no portal <strong>NimbusDocs</strong>:
                        </p>

                        <div style="background: #f9f9f9; border-left: 4px solid #00205B; padding: 15px; margin: 20px 0;">
                            <h3 style="margin: 0 0 10px 0; color: #00205B; font-size: 18px;">
                                <?= $title ?>
                            </h3>
                            <div style="color: #555; line-height: 1.6;">
                                <?= nl2br(htmlspecialchars($content)) ?>
                            </div>
                        </div>

                        <p style="margin:30px 0; text-align: center;">
                            <a href="<?= $url ?>"
                                style="display: inline-block; padding:12px 30px; background:#00205B; color:#fff; 
                                text-decoration:none; border-radius:5px; font-weight: bold;">
                                Ver todos os avisos
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
