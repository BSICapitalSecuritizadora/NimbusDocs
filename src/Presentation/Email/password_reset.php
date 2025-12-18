<?php
/**
 * Password Reset Email Template
 * @var string $name
 * @var string $resetUrl
 * @var string $appName
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f5f5f5;">
        <tr>
            <td style="padding: 40px 20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #00205b 0%, #001a4d 100%); padding: 30px 40px; border-radius: 12px 12px 0 0; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 700;">
                                <?= htmlspecialchars($appName) ?>
                            </h1>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <div style="text-align: center; margin-bottom: 30px;">
                                <div style="width: 70px; height: 70px; background: linear-gradient(135deg, #00205b 0%, #001a4d 100%); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;">
                                    <span style="font-size: 32px; color: white;">🔐</span>
                                </div>
                            </div>
                            
                            <h2 style="margin: 0 0 20px 0; color: #333333; font-size: 22px; text-align: center;">
                                Recuperação de Senha
                            </h2>
                            
                            <p style="margin: 0 0 20px 0; color: #555555; font-size: 16px; line-height: 1.6;">
                                Olá <strong><?= htmlspecialchars($name) ?></strong>,
                            </p>
                            
                            <p style="margin: 0 0 20px 0; color: #555555; font-size: 16px; line-height: 1.6;">
                                Recebemos uma solicitação para redefinir a senha da sua conta no <?= htmlspecialchars($appName) ?>. 
                                Clique no botão abaixo para criar uma nova senha:
                            </p>
                            
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="<?= htmlspecialchars($resetUrl) ?>" 
                                   style="display: inline-block; background: linear-gradient(135deg, #00205b 0%, #001a4d 100%); color: #ffffff; text-decoration: none; padding: 14px 40px; border-radius: 8px; font-size: 16px; font-weight: 600;">
                                    Redefinir Minha Senha
                                </a>
                            </div>
                            
                            <p style="margin: 0 0 10px 0; color: #888888; font-size: 14px; line-height: 1.6;">
                                Este link expira em <strong>1 hora</strong>.
                            </p>
                            
                            <p style="margin: 0 0 20px 0; color: #888888; font-size: 14px; line-height: 1.6;">
                                Se você não solicitou esta recuperação, ignore este e-mail. Sua senha permanecerá inalterada.
                            </p>
                            
                            <hr style="border: none; border-top: 1px solid #eeeeee; margin: 30px 0;">
                            
                            <p style="margin: 0; color: #aaaaaa; font-size: 12px; line-height: 1.6;">
                                Se o botão não funcionar, copie e cole o link abaixo no seu navegador:
                            </p>
                            <p style="margin: 10px 0 0 0; color: #00205b; font-size: 12px; word-break: break-all;">
                                <?= htmlspecialchars($resetUrl) ?>
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px 40px; border-radius: 0 0 12px 12px; text-align: center;">
                            <p style="margin: 0; color: #888888; font-size: 12px;">
                                © <?= date('Y') ?> <?= htmlspecialchars($appName) ?>. Todos os direitos reservados.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
