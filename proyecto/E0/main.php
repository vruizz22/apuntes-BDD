<?php
require_once 'Bananer.php';

$bananer = new Bananer('archivo1.csv', 'archivo2.csv');

# Realizar la limpeza de datos de los archivos
$bananer->deteccionArray();

# Crea las tablas en la base de datos
$bananer->tablasArray();

# Revisar que ninguna tabla tenga valores repetidos
$bananer->repetidosArray();

# Realizar las consultas
$bananer->consultasArray();
