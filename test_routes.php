<?php

// Script para testar todas as rotas do projeto
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Route;

echo "=== TESTE DE ROTAS DO ODONTO360 ===\n\n";

// Listar todas as rotas
$routes = Route::getRoutes();

echo "ROTAS WEB:\n";
echo "==========\n";
foreach ($routes as $route) {
    if (strpos($route->uri(), 'api/') === false) {
        echo sprintf("%-8s %-40s %s\n",
            implode('|', $route->methods()),
            $route->uri(),
            $route->getName() ?? 'N/A'
        );
    }
}

echo "\n\nROTAS API:\n";
echo "==========\n";
foreach ($routes as $route) {
    if (strpos($route->uri(), 'api/') === 0) {
        echo sprintf("%-8s %-40s %s\n",
            implode('|', $route->methods()),
            $route->uri(),
            $route->getName() ?? 'N/A'
        );
    }
}

echo "\n\nROTAS DE AUTENTICAÇÃO:\n";
echo "=====================\n";
$authRoutes = ['login', 'register', 'logout', 'auth.google'];
foreach ($authRoutes as $routeName) {
    $route = $routes->getByName($routeName);
    if ($route) {
        echo "✅ $routeName: ".$route->uri()."\n";
    } else {
        echo "❌ $routeName: NÃO ENCONTRADA\n";
    }
}

echo "\n\nROTAS DE IA:\n";
echo "============\n";
$aiRoutes = ['ai.suggestions', 'ai.analysis', 'ai.predictions'];
foreach ($aiRoutes as $routeName) {
    $route = $routes->getByName($routeName);
    if ($route) {
        echo "✅ $routeName: ".$route->uri()."\n";
    } else {
        echo "❌ $routeName: NÃO ENCONTRADA\n";
    }
}

echo "\n=== FIM DO TESTE ===\n";
