<?php

require __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection(
    'rabbitmq',
    5672,
    'guest',
    'guest'
);

$channel = $connection->channel();

$queue = 'tareas';

$channel->queue_declare(
    $queue,
    false,
    true,
    false,
    false
);

echo "Esperando mensajes...\n";

$callback = function ($msg) {
    echo "Mensaje recibido: " . $msg->body . "\n";
};

$channel->basic_consume(
    $queue,
    '',
    false,
    true,
    false,
    false,
    $callback
);

while ($channel->is_consuming()) {
    $channel->wait();
}