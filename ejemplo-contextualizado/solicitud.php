<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */


require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


echo " SOLICITUD DE RECURSOS - USFX  ";


$solicitante = readline("Nombre del solicitante: ");

echo "\nTipo de recurso:\n";
echo "1. Llave de aula\n";
echo "2. Equipo de laboratorio\n";

$opcion = readline("Seleccione una opcion: ");

if ($opcion === "1") {

    $tipo = "llave";
    $recurso = readline("Aula solicitada: ");

} elseif ($opcion === "2") {

    $tipo = "equipo";
    $recurso = readline("Equipo solicitado: ");

} else {

    echo "Opcion incorrecta.\n";
    exit;
}

$hora = readline("Hora requerida: ");

// Datos de la solicitud
$solicitud = [
    'id' => uniqid('SOL-'),
    'solicitante' => $solicitante,
    'tipo' => $tipo,
    'recurso' => $recurso,
    'hora' => $hora
];

// Convertir los datos a JSON
$json = json_encode(
    $solicitud,
    JSON_UNESCAPED_UNICODE
);

// Conectar con RabbitMQ
$conexion = new AMQPStreamConnection(
    'localhost',
    5672,
    'guest',
    'guest'
);

$canal = $conexion->channel();

// Utilizar nuestro Exchange
$canal->exchange_declare(
    'reservas_usfx',
    'direct',
    false,
    true,
    false
);

// Crear el mensaje
$mensaje = new AMQPMessage(
    $json,
    [
        'content_type' => 'application/json',
        'delivery_mode' => 2
    ]
);

// Publicar
$canal->basic_publish(
    $mensaje,
    'reservas_usfx',
    $tipo
);

echo "\n========================================\n";
echo " SOLICITUD ENVIADA\n";
echo "========================================\n";
echo "ID: {$solicitud['id']}\n";
echo "Solicitante: {$solicitud['solicitante']}\n";
echo "Tipo: {$solicitud['tipo']}\n";
echo "Recurso: {$solicitud['recurso']}\n";
echo "Hora: {$solicitud['hora']}\n";
echo "Routing Key: $tipo\n";

$canal->close();
$conexion->close();