# Cap nhat cdn-version.txt = commit SHA hien tai, roi push.
# Chay sau moi lan sua script.js / styles.css / assets.
Set-Location $PSScriptRoot\..
$sha = (git rev-parse --short HEAD).Trim()
Set-Content -Path "phucdat\cdn-version.txt" -Value $sha -NoNewline -Encoding ascii
git add phucdat/cdn-version.txt
git commit -m "Bump CDN version to $sha for instant Ladipage updates."
git push origin HEAD
Write-Host "CDN version -> $sha"
Write-Host "Ladipage se lay ban moi trong vai giay (khong can doi snippet)."
