<?php
$tokenValue = htmlspecialchars($token['token']);
$urlLogin   = "https://nimbusdocs.local/portal/access/{$tokenValue}";
?>
<table width="100%" cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif;">
    <tr>
        <td align="center" style="padding: 40px;">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#fff; border-radius: 8px;">
                <tr>
                    <td style="padding: 30px;">
                        <h2 style="margin-top:0;color:#00205B;">Acesso ao portal NimbusDocs</h2>

                        <p>Foi gerado um link de acesso ao portal NimbusDocs para você.</p>

                        <p style="margin:30px 0;">
                            <a href="<?= $urlLogin ?>" style="padding:12px 20px; background:#00205B; color:#fff; text-decoration:none; border-radius:5px;">
                                Acessar o portal
                            </a>
                        </p>

                        <p style="color:#888;font-size:12px;">
                            Este link é de uso único e possui tempo de expiração.
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>