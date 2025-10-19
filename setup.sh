#!/bin/bash

echo 
echo "================================"

# Kopiranje .env fajla
if [ ! -f .env ]; then
    echo "📋 Kopiram .env.example u .env..."
    cp .env.example .env
else
    echo "✅ .env fajl već postoji"
fi

# Instaliranje Composer dependency-ja
echo "📦 Instaliram PHP dependency-je..."
composer install

# Generisanje application key
echo "🔑 Generiram application key..."
php artisan key:generate

# Instaliranje npm dependency-ja
echo "🟢 Instaliram JavaScript dependency-je..."
npm install

# Build frontend assets
echo "🏗️  Kompajliram frontend assets..."
npm run build

echo ""
echo "✅ Setup završen!"
echo ""
echo "💡 Ne zaboravite da:"
echo "   1. Podesite bazu podataka u .env fajlu"
echo "   2. Pokrenete: php artisan migrate"
echo "   3. Pokrenete: php artisan serve"