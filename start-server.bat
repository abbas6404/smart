@echo off
echo Starting Smart Cash Club Laravel Server...
echo.
echo Your project will be available at:
echo - Local: http://localhost:9000
echo - Network: http://192.168.50.237:9000
echo - API: http://192.168.50.237:9000/api
echo.
echo Press Ctrl+C to stop the server
echo.
cd /d "C:\Users\aioli\Herd\smart"
php -S 0.0.0.0:9000 -t public
