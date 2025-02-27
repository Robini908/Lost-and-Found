# Set error action preference to continue
$ErrorActionPreference = "Continue"

# Set the working directory
$projectPath = "C:\my-projects\lost-found"
Set-Location $projectPath

# Create log directory if it doesn't exist
$logDir = "storage\logs"
if (-not (Test-Path $logDir)) {
    New-Item -ItemType Directory -Path $logDir -Force
}

$logFile = Join-Path $logDir "queue-worker.log"

function Write-Log {
    param($Message)
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    "$timestamp $Message" | Out-File -FilePath $logFile -Append
    Write-Host "$timestamp $Message"
}

Write-Log "Starting queue worker..."

while ($true) {
    try {
        Write-Log "Initializing queue worker..."

        # Start the queue worker with specific settings
        $process = Start-Process php -ArgumentList "artisan queue:work --tries=3 --backoff=3 --sleep=3 --timeout=60" -NoNewWindow -PassThru

        Write-Log "Queue worker started with PID: $($process.Id)"

        # Wait for the process to exit
        $process.WaitForExit()

        # If the process exits, log it and wait before restarting
        Write-Log "Queue worker stopped. Restarting in 5 seconds..."
        Start-Sleep -Seconds 5

    } catch {
        $errorMessage = $_.Exception.Message
        Write-Log "Error occurred: $errorMessage"
        Write-Log "Restarting queue worker in 10 seconds..."
        Start-Sleep -Seconds 10
    }
}
