<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */


require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

// 1. Conectarse a RabbitMQ
$conexion = new AMQPStreamConnection(
    'localhost',
    5672,
    'guest',
    'guest'
);

// 2. Crear canal
$canal = $conexion->channel();

// 3. Asegurarnos de que existe el Exchange
$canal->exchange_declare(
    'reservas_usfx',
    'direct',
    false,
    true,
    false
);

// 4. Declarar la cola de equipos
$canal->queue_declare(
    'cola_equipos',
    false,
    true,
    false,
    false
);

// 5. Binding: equipo -> cola_equipos
$canal->queue_bind(
    'cola_equipos',
    'reservas_usfx',
    'equipo'
);


echo "         LABORATORIO USFX\n";

echo "Esperando solicitudes de equipos...\n\n";

// 6. Qué hacer cuando llegue un mensaje
$callback = function ($mensaje) {

    $solicitud = json_decode(
        $mensaje->getBody(),
        true
    );

    
    echo " NUEVA SOLICITUD DE EQUIPO\n";
   

    echo "ID: {$solicitud['id']}\n";
    echo "Solicitante: {$solicitud['solicitante']}\n";
    echo "Equipo: {$solicitud['recurso']}\n";
    echo "Hora: {$solicitud['hora']}\n";

    echo "\nProcesando solicitud...\n";

    sleep(8);

    // Confirmación
    $mensaje->ack();

    echo "Solicitud procesada correctamente.\n";
    echo "ACK enviado a RabbitMQ.\n\n";
};

// 7. Escuchar cola_equipos
$canal->basic_consume(
    'cola_equipos',
    '',
    false,
    false,
    false,
    false,
    $callback
);

// 8. Mantenerse esperando
while ($canal->is_consuming()) {
    $canal->wait();
}