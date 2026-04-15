# Safe per-table non-destructive data export (PowerShell)

# Run from project root. Requires Docker + container 'carehub-db' running.



# 1) configure tables (order matters)

$candidateTables = @(

  'children',

  'placements',

  'appointment',

  'carerappointment',

  'messages',

  'alert',

  'carer_documents'   # adjust if your documents table uses a different name (e.g. 'documents')

)



# container & DB creds

$container = 'carehub-db'

$db = 'carehub'

$dbUser = 'carehub_user'

$dbPass = 'carehub_pass'   # <-- CHANGE if different



# 2) helper - check if table exists

function TableExists($t) {

  $sql = "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='$db' AND TABLE_NAME='$t';"

  $out = docker exec -i $container mysql -u$dbUser -p$dbPass -e $sql 2>&1

  if ($out -match 'ERROR') {

    Write-Host "MySQL error checking table ${t}:`n$out" -ForegroundColor Yellow

    return $false

  }

  $lines = $out -split "`n" | ForEach-Object { $_.Trim() } | Where-Object { $_ -ne '' }

  if ($lines.Count -lt 2) { return $false }

  return [int]$lines[-1] -gt 0

}



# 3) build list of present tables

$present = @()

foreach ($t in $candidateTables) {

  Write-Host "Checking table: $t ..."

  if (TableExists $t) {

    Write-Host "  -> FOUND"

    $present += $t

  } else {

    Write-Host "  -> MISSING (skipping)"

  }

}



if ($present.Count -eq 0) {

  Write-Host "`nNo candidate tables exist in DB. Run:" -ForegroundColor Yellow

  Write-Host "  docker exec -i $container mysql -u$dbUser -p$dbPass -e `"SHOW TABLES;`" $db"

  return

}



# 4) make temporary dir on host and dump each present table inside container to /tmp then copy out

$tmpDir = Join-Path $env:TEMP ("carehub_sql_" + (Get-Date -Format yyyyMMddHHmmss))

New-Item -Path $tmpDir -ItemType Directory -Force | Out-Null

Write-Host "`nDumping data-only for: $($present -join ', ')`n"



foreach ($t in $present) {

  $inside = "/tmp/dump_${t}.sql"

  $dumpCmd = "exec mysqldump -u$dbUser -p$dbPass --no-create-info --skip-triggers --skip-add-locks --insert-ignore $db `"$t`" > $inside"

  Write-Host "Running on container: mysqldump $t ..."

  docker exec $container sh -c $dumpCmd

  $hostPath = Join-Path $tmpDir ("$t.sql")

  docker cp "$($container):$inside" $hostPath 2>$null                 # <-- fixed: copy to $hostPath and redirect stderr

  if (Test-Path $hostPath) {

    Write-Host " Wrote $t -> $hostPath"

  } else {

    Write-Host " WARNING: Expected dump for $t not found at $hostPath" -ForegroundColor Yellow

  }

}



# 5) concat with FK wrapper

$outFile = Join-Path (Get-Location) "carehub_data_import_wrapped.sql"

"SET FOREIGN_KEY_CHECKS=0;" | Out-File -FilePath $outFile -Encoding utf8

foreach ($t in $present) {

  $part = Join-Path $tmpDir ("$t.sql")

  if (Test-Path $part) {

    Get-Content -Path $part | Out-File -Append -FilePath $outFile -Encoding utf8

    "`n-- end of $t`n" | Out-File -Append -FilePath $outFile -Encoding utf8

  } else {

    Write-Host "Skipping missing part file for $t" -ForegroundColor Yellow

  }

}

"SET FOREIGN_KEY_CHECKS=1;" | Out-File -Append -FilePath $outFile -Encoding utf8



# 6) diagnostics

Write-Host "`nFile created: $outFile"

Get-Item $outFile | Select-Object FullName, Length | Format-List



Write-Host "`nPreview (first 40 lines):"

Get-Content -Path $outFile -TotalCount 40 | ForEach-Object { $_ }



Write-Host "`nRow counts in source DB for present tables:"

foreach ($t in $present) {

  $cnt = docker exec -i $container mysql -u$dbUser -p$dbPass -e "SELECT '$t' AS tbl, COUNT(*) AS cnt FROM \`$t\`;" $db 2>&1

  Write-Host $cnt

}



Write-Host "`nSHA256 of file:"

Get-FileHash $outFile -Algorithm SHA256 | Format-List



Write-Host "`nFinished. If file looks good, zip it and send to Sidha or copy it to a shared location."





