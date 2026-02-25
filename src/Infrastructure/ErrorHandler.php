<?php

declare(strict_types=1);

namespace App\Infrastructure;

/**
 * Handler global de exceções e erros
 * Captura erros não tratados e mostra views personalizadas
 */
final class ErrorHandler
{
    private string $errorViewsPath;

    private bool $debug;

    public function __construct(string $errorViewsPath, bool $debug = false)
    {
        $this->errorViewsPath = $errorViewsPath;
        $this->debug = $debug;

        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
    }

    /**
     * Handler para erros PHP
     */
    public function handleError(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        // Não captura notices em produção
        if (!$this->debug && in_array($errno, [E_NOTICE, E_DEPRECATED, E_USER_NOTICE])) {
            return false;
        }

        $this->logError($errno, $errstr, $errfile, $errline);

        // Se for erro fatal, mostra página de erro
        if (in_array($errno, [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $this->show500($errstr);
        }

        return true;
    }

    /**
     * Handler para exceções
     */
    public function handleException(\Throwable $e): void
    {
        $this->logError(
            0,
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );

        $this->show500($e->getMessage() . ' (em ' . basename($e->getFile()) . ':' . $e->getLine() . ')');
    }

    /**
     * Mostra página de erro 500
     */
    private function show500(string $error = null): void
    {
        http_response_code(500);

        // Inclui view de erro
        $errorViewFile = $this->errorViewsPath . '/500.php';
        if (file_exists($errorViewFile)) {
            ob_clean();
            if ($this->debug) {
                include $errorViewFile;
            } else {
                // Em produção, não mostra detalhes
                include $errorViewFile;
            }
        } else {
            echo '<div style="font-family:system-ui;padding:2rem">';
            echo '<h2>500 - Erro interno do servidor</h2>';
            echo '<p>Desculpe, algo deu errado.</p>';
            if ($this->debug && $error) {
                echo '<pre style="background:#f5f5f5;padding:1rem;overflow:auto;">';
                echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8');
                echo '</pre>';
            }
            echo '</div>';
        }

        exit;
    }

    /**
     * Log do erro
     */
    private function logError(int $errno, string $errstr, string $errfile, int $errline, ?string $trace = null): void
    {
        $logFile = dirname($this->errorViewsPath, 4) . '/storage/logs/errors.log';
        $logDir = dirname($logFile);

        if (!is_dir($logDir)) {
            mkdir($logDir, 0775, true);
        }

        $message = sprintf(
            "[%s] %s: %s in %s:%d\n",
            date('Y-m-d H:i:s'),
            $this->getErrorName($errno),
            $errstr,
            $errfile,
            $errline
        );

        if ($trace) {
            $message .= "Trace:\n" . $trace . "\n";
        }

        $message .= str_repeat('=', 80) . "\n";

        error_log($message, 3, $logFile);
    }

    /**
     * Nome legível do erro
     */
    private function getErrorName(int $errno): string
    {
        $errorNames = [
            E_ERROR => 'Error',
            E_WARNING => 'Warning',
            E_PARSE => 'Parse Error',
            E_NOTICE => 'Notice',
            E_CORE_ERROR => 'Core Error',
            E_CORE_WARNING => 'Core Warning',
            E_COMPILE_ERROR => 'Compile Error',
            E_COMPILE_WARNING => 'Compile Warning',
            E_USER_ERROR => 'User Error',
            E_USER_WARNING => 'User Warning',
            E_USER_NOTICE => 'User Notice',
            E_STRICT => 'Strict',
            E_DEPRECATED => 'Deprecated',
            E_USER_DEPRECATED => 'User Deprecated',
        ];

        return $errorNames[$errno] ?? 'Unknown Error';
    }
}
