<?php

declare(strict_types=1);

/**
 * Script de Migração: Criptografar dados sensíveis (CPF, Telefone) na tabela portal_users.
 * Pode ser rodado múltiplas vezes (ele re-criptografa, rotacionando o IV).
 */

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../bootstrap/app.php';
$pdo = $config['pdo'];

use App\Support\Encrypter;

echo "[" . date('Y-m-d H:i:s') . "] --- INICIANDO CRIPTOGRAFIA DE DADOS (LGPD) ---\n";

try {
    // Busca todos os usuários (sem usar o Repository para pegar dados raw, 
    // mas o Repository já desencripta... então podemos usar ele mesmo para normalizar!)
    // Se usarmos PDO direto, pegamos "123" ou "ENC_123".
    // Se usarmos Repo->all(), não vem esses campos.
    // Vamos usar PDO direto para ter controle total.

    $stmt = $pdo->query("SELECT id, full_name, document_number, phone_number FROM portal_users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Encontrados " . count($users) . " usuários.\n";

    $updated = 0;
    
    foreach ($users as $user) {
        $id = (int)$user['id'];
        $doc = $user['document_number'];
        $phone = $user['phone_number'];

        $newDoc = null;
        $newPhone = null;
        $needsUpdate = false;

        // Lógica:
        // 1. Tenta desencriptar (normaliza para plain text se já estiver criptografado, ou mantém plain se não estiver)
        // 2. Criptografa novamente.
        // Isso garante que tudo fique no formato novo.

        if (!empty($doc)) {
            $plainDoc = Encrypter::decrypt($doc);
            // Verifica se houve erro (convertido para null?) ou se o decrypt retornou o proprio payload
            // Encrypter::decrypt retorna null se falhar integridade, ou o payload se não for json.
            // Se for null, algo corrompeu. Se for igual, era plain text.
            
            if ($plainDoc === null) {
                echo "   [AVISO] Falha ao decifrar CPF do usuário $id. Mantendo original.\n";
            } else {
                 $encrypted = Encrypter::encrypt($plainDoc);
                 // Só marca update se mudou (mas sempre muda por causa do IV rs)
                 // Para evitar loop infinito de re-encriptação se rodar toda hora,
                 // poderiamos checar se $doc JÁ é valido JSON/Base64.
                 // Mas vamos forçar a garantia.
                 $newDoc = $encrypted;
                 $needsUpdate = true;
            }
        }

        if (!empty($phone)) {
            $plainPhone = Encrypter::decrypt($phone);
            if ($plainPhone === null) {
                echo "   [AVISO] Falha ao decifrar Telefone do usuário $id. Mantendo original.\n";
            } else {
                 $newPhone = Encrypter::encrypt($plainPhone);
                 $needsUpdate = true;
            }
        }

        if ($needsUpdate) {
            // Update manual para não passar pelo repository (que faria encrypt duplo se estivesse mal configurado,
            // mas o repository já trata envio de plain text.
            // Aqui vamos fazer SQL direto para performance e certeza.
            
            $upd = $pdo->prepare("UPDATE portal_users SET document_number = :doc, phone_number = :phone, updated_at = NOW() WHERE id = :id");
            $upd->execute([
                ':doc'   => $newDoc ?? $doc,     // Se foi calculado novo, usa. Senão mantem o old (que pode ser null ou o original)
                ':phone' => $newPhone ?? $phone,
                ':id'    => $id
            ]);
            $updated++;
            
            if ($updated % 10 === 0) {
                echo ".";
            }
        }
    }

    echo "\nProcesso finalizado. {$updated} usuários atualizados.\n";

} catch (\Throwable $e) {
    echo "\n[ERRO CRÍTICO] " . $e->getMessage() . "\n";
    exit(1);
}
