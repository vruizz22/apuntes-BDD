<?php
// Abrimos lo archivos a trabajar
$archivo1 = fopen("archivo1.txt", "r");
$archivo2 = fopen("archivo2.txt", "w");
$archivo3 = fopen("archivo3.txt", "a");

// Obtenemos la información de los archivos

// Archivo 1
$info = array(); # Creamos un arreglo para guardar la información
echo "Archivo 1: <br>";
while (!feof($archivo1)) {
    $linea = fgets($archivo1);
    $info[] = $linea;
}
fclose($archivo1);

// Archivo 2
echo "Archivo 2: <br>";
for ($i = 0; $i < count($info); $i++) {
    fwrite($archivo2, $info[$i]);
}
fclose($archivo2);

// Archivo 3
echo "Archivo 3: <br>";
fwrite($archivo3, "Información adicional\n");
fclose($archivo3);


// Mostramos la información de los archivos

$archivo1 = fopen("archivo1.txt", "r");
$archivo2 = fopen("archivo2.txt", "r");
$archivo3 = fopen("archivo3.txt", "r");

echo "Archivo 1: <br>";
while (!feof($archivo1)) {
    $linea = fgets($archivo1);
    echo $linea;
}

echo "Archivo 2: <br>";
while (!feof($archivo2)) {
    $linea = fgets($archivo2);
    echo $linea;
}

while (!feof($archivo3)) {
    $linea = fgets($archivo3);
    echo $linea;
}

fclose($archivo1);
fclose($archivo2);
fclose($archivo3);
