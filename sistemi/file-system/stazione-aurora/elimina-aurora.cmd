@echo off
setlocal
set "destinazione=%~1"
if not defined destinazione set "destinazione=aurora"
if not exist "%destinazione%\.stazione-aurora" goto non_riconosciuta
set "marcatore="
set /p "marcatore=" < "%destinazione%\.stazione-aurora"
if not "%marcatore%"=="AURORA-LAB-V1" goto non_riconosciuta

echo Eliminare "%destinazione%" e TUTTO il suo contenuto?
set "conferma="
set /p "conferma=Scrivi ELIMINA: "
if not "%conferma%"=="ELIMINA" (
    echo Annullato.
    exit /b 0
)
rmdir /s /q "%destinazione%" || exit /b 1
echo Stazione eliminata.
exit /b 0

:non_riconosciuta
echo Non riconosco una stazione Aurora in: "%destinazione%"
exit /b 1
