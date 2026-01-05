<?php
$docTitle = htmlspecialchars($doc['title']);
$category = htmlspecialchars($doc['category_name']);
$url = "https://nimbusdocs.local/portal/documents/general/{$doc['id']}/view";
?>

<table width="100%" cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif;">
    <tr>
        <td align="center" style="padding: 40px;">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#fff; border-radius: 8px;">
                <tr>
                    <td style="padding: 30px;">
                        <h2 style="margin-top:0;color:#00205B;">Novo documento disponível</h2>

                        <p>Um novo documento foi disponibilizado para você no portal <strong>NimbusDocs</strong>:</p>

                        <p>
                            <strong><?= $docTitle ?></strong><br>
                            <span style="color:#777;">Categoria: <?= $category ?></span>
                        </p>

                        <p style="margin:30px 0;">
                            <a href="<?= $url ?>"
                                style="padding:12px 20px; background:#00205B; color:#fff; 
                        text-decoration:none; border-radius:5px;">
                                Visualizar documento
                            </a>
                        </p>

                        <p style="color:#888;font-size:12px;">
                            Esta é uma mensagem automática. Por favor, não responda.
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>