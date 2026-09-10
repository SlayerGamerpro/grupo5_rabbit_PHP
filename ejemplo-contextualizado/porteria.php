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

// 2. Crear un canal
$canal = $conexion->channel();

// 3. Asegurarnos de que existe el Exchange
$canal->exchange_declare(
    'reservas_usfx',
    'direct',
    false,
    true,
    false
);

// 4. Asegurarnos de que existe la cola de llaves
$canal->queue_declare(
    'cola_llaves',
    false,
    true,
    false,
    false
);

// 5. Binding: llave -> cola_llaves
$canal->queue_bind(
    'cola_llaves',
    'reservas_usfx',
    'llave'
);


echo "            PORTERIA USFX\n";

echo "Esperando solicitudes de llaves...\n\n";

// 6. Qué hacer cuando llegue un mensaje
$callback = function ($mensaje) {

    // Convertimos JSON a arreglo PHP
    $solicitud = json_decode(
        $mensaje->getBody(),
        true
    );

    
    echo " NUEVA SOLICITUD DE LLAVE\n";
    

    echo "ID: {$solicitud['id']}\n";
    echo "Solicitante: {$solicitud['solicitante']}\n";
    echo "Aula: {$solicitud['recurso']}\n";
    echo "Hora: {$solicitud['hora']}\n";

    echo "\nProcesando solicitud...\n";

    // Lo dejamos unos segundos para observar UNACKED
    sleep(8);

    // 7. Confirmamos que ya se procesó
    $mensaje->ack();

    echo "Solicitud procesada correctamente.\n";
    echo "ACK enviado a RabbitMQ.\n\n";
};

// 8. Consumir mensajes de cola_llaves
$canal->basic_consume(
    'cola_llaves',
    '',
    false,
    false,
    false,
    false,
    $callback
);

// 9. Mantener el consumidor esperando mensajes
while ($canal->is_consuming()) {
    $canal->wait();
}