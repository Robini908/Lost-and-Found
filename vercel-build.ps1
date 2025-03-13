# Install dependencies
composer install --no-dev --optimize-autoloader

# Install and build frontend
npm install
npm run build

# Clear Laravel caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
