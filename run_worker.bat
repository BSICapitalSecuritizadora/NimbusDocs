@echo off
REM =============================================================================
REM NimbusDocs Notifications Worker - Windows Batch Script
REM =============================================================================
REM 
REM Executa o worker de notificações em loop contínuo no Windows.
REM 
REM Uso:
REM   run_worker.bat           - Executa em loop contínuo
REM   run_worker.bat --once    - Executa apenas uma vez
REM
REM Para rodar como serviço Windows, use NSSM:
REM   nssm install NimbusDocsWorker "C:\xampp\htdocs\NimbusDocs\run_worker.bat"
REM   nssm start NimbusDocsWorker
REM
REM =============================================================================

setlocal enabledelayedexpansion

REM Configuração do PHP
set PHP_PATH=C:\xampp\php\php.exe
set WORKER_PATH=%~dp0bin\notifications-worker.php

REM Verifica argumentos
if "%1"=="--once" (
    echo [run_worker] Executando uma vez...
    "%PHP_PATH%" "%WORKER_PATH%" --once
    goto :eof
)

echo ============================================
echo  NimbusDocs Notifications Worker
echo  Pressione Ctrl+C para parar
echo ============================================
echo.

:loop
    echo [%date% %time%] Iniciando ciclo do worker...
    
    REM Executa o worker (ele processa um lote e retorna)
    "%PHP_PATH%" "%WORKER_PATH%" --once
    
    REM Pausa entre ciclos (5 segundos)
    timeout /t 5 /nobreak > nul
    
    goto loop

:eof
echo [run_worker] Finalizado.
