<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */


require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

//  Conectamoos a RabbitMQ
$conexion = new AMQPStreamConnection(
    'localhost',
    5672,
    'guest',
    'guest'
);

// creamos un canal
$canal = $conexion->channel();

// Crear el Exchange
$canal->exchange_declare(
    'reservas_usfx',
    'direct',
    false,
    true,
    false
);

// Crear cola para llaves
$canal->queue_declare(
    'cola_llaves',
    false,
    true,
    false,
    false
);

// Crear cola para equipos
$canal->queue_declare(
    'cola_equipos',
    false,
    true,
    false,
    false
);

//  Binding: llave -> cola_llaves
$canal->queue_bind(
    'cola_llaves',
    'reservas_usfx',
    'llave'
);

//  Binding: equipo -> cola_equipos
$canal->queue_bind(
    'cola_equipos',
    'reservas_usfx',
    'equipo'
);


echo " RABBITMQ - RECURSOS USFX\n";

echo "Exchange: reservas_usfx\n";
echo "llave  -> cola_llaves\n";
echo "equipo -> cola_equipos\n";
echo "Configuracion correcta.\n";

// 8. Cerrar canal y conexión
$canal->close();
$conexion->close();