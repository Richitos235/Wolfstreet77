@echo off
echo Pripravuji nahrani zmen na GitHub...

:: Přidá všechny změny ve složce
git add .

:: Vytvoří commit se zprávou (můžeš změnit text v uvozovkách)
git commit -m "Automaticky update ze skriptu"

:: Odešle změny na GitHub
git push origin main

echo Hotovo!
pause