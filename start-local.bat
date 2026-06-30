@echo off
set PHP=C:\Users\user\Documents\Program\lord\.local\php\php.exe
set PATH=%USERPROFILE%\scoop\shims;%PATH%

echo Starting MariaDB (if not already running)...
start "" mysqld --console

timeout /t 3 /nobreak >nul

cd /d C:\Users\user\Documents\Program\lord
echo Starting Laravel at http://127.0.0.1:8000
"%PHP%" artisan serve --host=127.0.0.1 --port=8000
