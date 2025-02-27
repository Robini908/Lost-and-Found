$processName = "php"
$arguments = "artisan queue:work --daemon"
$workingDirectory = "C:\my-projects\lost-found"

# Change to the working directory
Set-Location $workingDirectory

# Start the process
Start-Process $processName -ArgumentList $arguments -NoNewWindow
