<?php

require __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

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

$mensaje = "Procesar usuario Luis";

$msg = new AMQPMessage(
    $mensaje,
    [
        'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
    ]
);

$channel->basic_publish(
    $msg,
    '',
    $queue
);

echo "Mensaje enviado: $mensaje\n";

$channel->close();
$connection->close();