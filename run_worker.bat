@echo off
title NimbusDocs - Processador de E-mails
color 0A
echo ===============================================
echo   NIMBUSDOCS - PROCESSADOR DE NOTIFICACOES
echo ===============================================
echo.
echo Iniciando worker...
:loop
php bin/notifications-worker.php
:: Aguarda 5 segundos antes de rodar novamente para nao sobrecarregar a CPU se estiver vazio
timeout /t 5 >nul
goto loop
