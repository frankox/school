#requires -Version 5.1
param([string]$Percorso = 'aurora')
$ErrorActionPreference = 'Stop'

$cartella = Get-Item -LiteralPath $Percorso
$marcatore = Join-Path $Percorso '.stazione-aurora'
if (-not (Test-Path -LiteralPath $marcatore -PathType Leaf)) {
    throw "Non riconosco una stazione Aurora in: $Percorso"
}
$fileMarcatore = Get-Item -LiteralPath $marcatore -Force
if (($cartella.Attributes -band [IO.FileAttributes]::ReparsePoint) -or
    ($fileMarcatore.Attributes -band [IO.FileAttributes]::ReparsePoint) -or
    (Get-Content -LiteralPath $marcatore -Raw).Trim() -ne 'AURORA-LAB-V1') {
    throw "Non riconosco una stazione Aurora in: $Percorso"
}

$conferma = Read-Host "Eliminare '$($cartella.FullName)' e TUTTO il suo contenuto? Scrivi ELIMINA"
if ($conferma -cne 'ELIMINA') {
    Write-Host 'Annullato.'
    return
}
Remove-Item -LiteralPath $cartella.FullName -Recurse -Force
Write-Host 'Stazione eliminata.'
