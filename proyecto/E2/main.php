<?php
require_once 'Cargador.php';
require_once 'Corrector.php';

$env_string = "host=localhost port=5432 dbname=grupo15 user=grupo15 password=Elefante$15";

// Llamada al módulo Cargador
$cargador = new Cargador($env_string);
$cargador->CrearTablas();
$cargador->CargarDatos();
