param(
    [string]$Path = "composer.json"
)

if (-not (Test-Path -LiteralPath $Path)) {
    Write-Error "File not found: $Path"
    exit 1
}

Write-Host "Checking BOM: $Path"
$bytes = [System.IO.File]::ReadAllBytes($Path)
if ($bytes.Length -ge 3 -and $bytes[0] -eq 239 -and $bytes[1] -eq 187 -and $bytes[2] -eq 191) {
    Write-Host "BOM detected. Rewriting without BOM..."
    $text = [System.Text.Encoding]::UTF8.GetString($bytes, 3, $bytes.Length - 3)
} else {
    Write-Host "No BOM at start. Ensuring no stray zero-width chars..."
    $text = [System.Text.Encoding]::UTF8.GetString($bytes)
}

# Strip common zero-width characters that might confuse parsers
$text = $text.TrimStart([char]0xFEFF,[char]0x200B,[char]0x200C,[char]0x200D)

$utf8NoBom = New-Object System.Text.UTF8Encoding($false)
[System.IO.File]::WriteAllText($Path, $text, $utf8NoBom)

Write-Host "Rewrote $Path with UTF-8 (no BOM)."
