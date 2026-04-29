<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../routes/web.php';

if ($app instanceof \App\Core\Application) {
    $app->run();
} else {
    throw new RuntimeException('Falha ao inicializar a aplicação.');
}
?>



