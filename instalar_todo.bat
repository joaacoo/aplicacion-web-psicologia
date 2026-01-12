@echo off
echo ===================================================
echo INSTALADOR AUTOMATICO - APLICACION PSICOLOGA NAZA
echo ===================================================
echo.
echo Pasos que realizara este script:
echo 1. Descargar Laravel Framework (core) sin sobrescribir tus archivos.
echo 2. Instalar dependencias (Composer).
echo 3. Generar la clave de aplicacion.
echo.
echo IMPORTANTE: Necesitas tener 'composer' instalado en tu sistema.
pause

echo.
echo [1/3] Instalando Larvel Framework...
call composer create-project laravel/laravel:^8.0 temp_install --prefer-dist --no-interaction
echo Moviendo archivos del framework...
xcopy /E /Y /H temp_install\* .
rmdir /S /Q temp_install

echo.
echo [2/3] Instalando dependencias completas...
call composer install
call npm install

echo.
echo [3/3] Configurando seguridad...
call php artisan key:generate
call php artisan storage:link

echo.
echo ===================================================
echo INSTALACION COMPLETADA
echo ===================================================
echo.
echo Ahora intenta ejecutar: php artisan serve
echo Y abre en tu navegador: http://127.0.0.1:8000
pause
