<?php

declare(strict_types=1);

namespace App\Support;

final class PasswordValidator
{
    /**
     * Valida se a senha atende aos critérios de "Senha Forte":
     * - Mínimo de 8 caracteres
     * - Pelo menos 1 letra maiúscula
     * - Pelo menos 1 letra minúscula
     * - Pelo menos 1 número
     * - Pelo menos 1 caractere especial (!@#$%^&* etc.)
     *
     * @param string $password
     * @return array<int, string> Lista de erros (vazia se a senha for válida)
     */
    public static function validate(string $password): array
    {
        $errors = [];

        if (mb_strlen($password) < 8) {
            $errors[] = 'A senha deve ter pelo menos 8 caracteres.';
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'A senha deve conter pelo menos uma letra maiúscula.';
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'A senha deve conter pelo menos uma letra minúscula.';
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'A senha deve conter pelo menos um número.';
        }

        if (!preg_match('/[\W_]/', $password)) { // \W matches any non-word character, _ matches underscore
            $errors[] = 'A senha deve conter pelo menos um caractere especial (ex: !@#$%^&*).';
        }

        return $errors;
    }
}
