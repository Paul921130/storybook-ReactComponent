@echo off
set /p id="Input your magaele Name: "
xcopy _template %cd%\magaele\%id% /D /E /C /R /I /K /Y /F
echo 'Finish!'
