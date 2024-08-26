<?php
require_once 'Bananer.php';

$bananer = new Bananer('Archivo1.csv', 'Archivo2.csv');

# Realizar la limpeza de datos de los archivos
$bananer->deteccionArray();

# Crea las tablas en la base de datos
$bananer->tablasArray();

# Revisar que ninguna tabla tenga valores repetidos
$bananer->repetidosArray();

# Realizar las consultas
$bananer->consultasArray();
