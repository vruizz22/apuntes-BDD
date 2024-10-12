<?php
require_once 'Cargador.php';

// Llamada al módulo Cargador
$cargador = new Cargador("host=localhost port=5432 dbname=grupo15 user=grupo15 password=Elefante$15");
$cargador->CrearTablas();
?>