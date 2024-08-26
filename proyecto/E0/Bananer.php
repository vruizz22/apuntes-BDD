<?php
/* Este codigo es Bananer un modúlo php que contendra
la clase Bananer la cual realizara los distintos trabajos
especificados en el enunciado por sus metodos */

class Bananer
{
    public $archivo1;
    public $archivo2;
    public $array1; # Array que guarda los datos del archivo 1
    public $array2; # Array que guarda los datos del archivo 2

    public function __construct($archivo1, $archivo2)
    {
        /* El archivo 1 contiene información de los cursos 
        que se han impartido desde el comienzo de los tiempos.
        
        El archivo 2 contiene la información de los profesores
        y administrativos vigenes en la institución. */

        $this->archivo1 = $archivo1;
        $this->archivo2 = $archivo2;
        $this->array1 = $this->leerArchivo($archivo1);
        $this->array2 = $this->leerArchivo($archivo2);

        # Reemplazo de caracteres especiales
        $this->array1 = $this->reemplazarCaracteresEspeciales($this->array1);
        $this->array2 = $this->reemplazarCaracteresEspeciales($this->array2);
    }

    public function leerArchivo($archivo)
    {
        /* leerArchivo recibe un archivo .csv
        y realiza la lectura para retornalo como array*/

        $file = fopen($archivo, 'r');
        $array = [];
        $firstLine = true;
        while (($line = fgetcsv($file)) !== FALSE) {
            if ($firstLine) {
                $firstLine = false;
                continue; // Ignorar la primera línea
            }
            $array[] = $line;
        }
        fclose($file);
        return $array;
    }

    public function deteccionArray()
    {
        /* deteccionArray realica la detección
        de los datos que no cumplan con los formatos
        correctos indicados, si es posible los corrige
        si no los descarta. No nulo, significa que no acepta vacio "" */

        /* Comenzamos el analisis de los datos, 
        no nulo significa que no acepta datos nulos */

        # Analisis de archivo 1

        # Indices de las columnas que no aceptan nulos
        $no_nulos = [0, 1, 2, 3, 4, 5, 6, 7, 9, 10, 13, 14, 15, 16, 17, 18, 20, 21, 22];

        foreach ($this->array1 as $key => $value) {
            foreach ($value as $subKey => $subValue) {

                /*Subvalue es un string que contiene la linea completa
                de datos, se debe separar cada dato por ";" */

                $datos = explode(";", $subValue);

                # Eliminar vacios
                foreach ($datos as $index => $dato) {
                    # Caso especial RUN (4) nulo: #N/A
                    if ($index == 4 && $dato == "#N/A") {
                        $datos[$index] = "x";
                    }

                    # Caso general
                    elseif ($dato == "" && in_array($index, $no_nulos)) {
                        $datos[$index] = "x";
                    }
                }
                $datos = implode(";", $datos);
                $this->array1[$key][$subKey] = $datos;
            }
        }

        # Analisis de archivo 2

        # Indices de las columnas que no aceptan nulos
        $no_nulos = [0, 1];

        foreach ($this->array2 as $key => $value) {
            foreach ($value as $subKey => $subValue) {

                /*Subvalue es un string que contiene la linea completa
                de datos, se debe separar cada dato por ";" */

                $datos = explode(";", $subValue);

                # Eliminar vacios
                foreach ($datos as $index => $dato) {
                    if ($dato == "" && in_array($index, $no_nulos)) {
                        $datos[$index] = "x";
                    }
                }
                $datos = implode(";", $datos);
                $this->array2[$key][$subKey] = $datos;
            }
        }

        /* Corrección de datos, se traspasan los valores
        al tipo de dato especificado inicialmente (int o float)*/

        # Corrección de datos en archivo 1

        # Indices de las columnas int
        $col_int = [4, 10, 16, 17];

        # Indices de las columnas float
        $col_float = [19];
        foreach ($this->array1 as $key => $value) {
            foreach ($value as $subKey => $subValue) {
                $datos = explode(";", $subValue);

                # Corrección de datos, si el dato es un número (int o float) y no es nulo
                foreach ($datos as $index => $dato) {
                    if (in_array($index, $col_int) && $dato != "x") {
                        $datos[$index] = (int) $dato;
                    } elseif (in_array($index, $col_float) && $dato != "x") {
                        $datos[$index] = (float) $dato;
                    }
                }
            }
        }

        # Corrección de datos en archivo 2

        # Indices de las columnas numéricas
        $col_num = [0, 4];

        foreach ($this->array2 as $key => $value) {
            foreach ($value as $subKey => $subValue) {
                $datos = explode(";", $subValue);

                # Corrección de datos, si el dato es un número (int) y no es nulo
                foreach ($datos as $index => $dato) {
                    if (in_array($index, $col_num) && $dato != "x") {
                        $datos[$index] = (int) $dato;
                    }
                }
            }
        }
    }

    public function tablasArray()
    {
        /* tablasArray carga el array de la clase
        ya formateada por deteccionArray, en tablas
        como matrices PHP, las cuales no admiten valores repetidos. */

        # Tabla Persona

        /* Accedemos a archivo 1 para obtener la mayoria de datos de la persona
        y accedemos a archivo 2 para obtener el telefono */
        $persona = [];
        foreach ($this->array1 as $key => $value) {
            foreach ($value as $subKey => $subValue) {
                $datos = explode(";", $subValue);
                if (isset(
                    $datos[4],
                    $datos[5],
                    $datos[6],
                    $datos[7],
                    $datos[8],
                    $datos[9],
                    $datos[11],
                    $datos[12]
                )) {
                    $persona[$datos[4]] = [
                        'RUN' => $datos[4],
                        'DV' => $datos[5],
                        'Nombres' => $datos[6],
                        'Apellido Paterno' => $datos[7],
                        'Apellido Materno' => $datos[8],
                        'Nombre completo' => $datos[9],
                        'Teléfono' => "",
                        'mail personal' => $datos[11],
                        'mail institucional' => $datos[12]
                    ];
                }
            }
        }

        foreach ($this->array2 as $key => $value) {
            foreach ($value as $subKey => $subValue) {
                $datos = explode(";", $subValue);
                if (isset($datos[0], $datos[7])) {
                    $persona[$datos[0]]['Teléfono'] = $datos[7];
                }
            }
        }

        # Tabla Estudiante
        $estudiante = [];
        foreach ($this->array1 as $key => $value) {
            foreach ($value as $subKey => $subValue) {
                $datos = explode(";", $subValue);
                if (isset(
                    $datos[0],
                    $datos[10],
                    $datos[1],
                    $datos[20],
                    $datos[21],
                    $datos[22]
                )) {
                    $estudiante[$datos[10]] = [
                        'Cohorte' => $datos[0],
                        'Número de estudiante' => $datos[10],
                        'Código Plan' => $datos[1],
                        'Último Logro' => $datos[20],
                        'Fecha Logro' => $datos[21],
                        'Última toma de ramos' => $datos[22]
                    ];
                }
            }
        }

        # Tabla Profesor
        $profesor = [];
        foreach ($this->array2 as $key => $value) {
            foreach ($value as $subKey => $subValue) {
                $datos = explode(";", $subValue);
                if (isset(
                    $datos[0],
                    $datos[1],
                    $datos[8],
                    $datos[9],
                    $datos[10],
                    $datos[11],
                    $datos[12],
                    $datos[13],
                    $datos[14]
                )) {

                    if ($datos[9] == "diurno" && $datos[10] == "vespertino") {
                        $jornada = "completa";
                    } elseif ($datos[9] == "diurno") {
                        $jornada = "diurna";
                    } elseif ($datos[10] == "vespertino") {
                        $jornada = "vespertina";
                    } else {
                        $jornada = "";
                    }

                    $profesor[$datos[0]] = [
                        'RUN' => $datos[0],
                        'DV' => $datos[1],
                        'contrato' => $datos[8],
                        'jornada' => $jornada,
                        'dedicación' => $datos[11],
                        'grado académico' => $datos[12],
                        'jerarquía' => $datos[13],
                        'cargo' => $datos[14]
                    ];
                }
            }
        }

        # Tabla Administrativo
        /* Se considera administrativo solo a los que tengan un cargo */

        $administrativo = [];
        foreach ($this->array2 as $key => $value) {
            foreach ($value as $subKey => $subValue) {
                $datos = explode(";", $subValue);
                if (isset($datos[0], $datos[1], $datos[10]) && $datos[10] != "") {
                    $administrativo[$datos[0]] = [
                        'RUN' => $datos[0],
                        'DV' => $datos[1],
                        'cargo' => $datos[10]
                    ];
                }
            }
        }

        # Tabla Cursos
        $cursos = [];
        foreach ($this->array1 as $key => $value) {
            foreach ($value as $subKey => $subValue) {
                $datos = explode(";", $subValue);
                if (isset($datos[14], $datos[15], $datos[16], $datos[17])) {
                    $cursos[$datos[14]] = [
                        'sigla curso' => $datos[14],
                        'curso' => $datos[15],
                        'Secciones' => $datos[16],
                        'Nivel del curso' => $datos[17]
                    ];
                }
            }
        }

        # Tabla Notas
        $notas = [];
        foreach ($this->array1 as $key => $value) {
            foreach ($value as $subKey => $subValue) {
                $datos = explode(";", $subValue);
                if (isset(
                    $datos[4],
                    $datos[10],
                    $datos[14],
                    $datos[13],
                    $datos[18],
                    $datos[19]
                )) {
                    $notas[] = [
                        'RUN' => $datos[4],
                        'Número de estudiante' => $datos[10],
                        'sigla curso' => $datos[14],
                        'Periodo curso' => $datos[13],
                        'Calificación' => $datos[18],
                        'Nota' => $datos[19]
                    ];
                }
            }
        }

        # Revisar lineas repetidas en Notas y borrarlas para dejar solo una
        $notas = array_map("unserialize", array_unique(array_map("serialize", $notas)));

        # Crear y escribir en archivos CSV
        $this->escribirCSV('persona.csv', $persona);
        $this->escribirCSV('estudiante.csv', $estudiante);
        $this->escribirCSV('profesor.csv', $profesor);
        $this->escribirCSV('administrativo.csv', $administrativo);
        $this->escribirCSV('cursos.csv', $cursos);
        $this->escribirCSV('notas.csv', $notas);

        echo "Tablas guardadas en archivos CSV\n";
    }

    public function consultasArray()
    {
        /* consultasArray realiza dos tipos de consulta

        1. Carga académica acumulada: Entrega en una linea
        cada curso realizado por un estudiante (run ingresado
        en consola) indicando, periodo del curso, sigla del curso
        nombre curso, nota y calificación. Además, al final de cada
        periodo entregar el promedio del semestre (PPS) y al final
        de todo el reporte el promedio de todos los cursos (PPA).

        2. Lista de curso: Entrega en una linea cada estudiante
        perteneciente a un curso y periodo (ingresado en consola)
        indicando: cohorte, nombre compleo, RUN y número de estudiante.
        
        NOTA: Para las consultas accedera a cualquiera de las tablas csv ya creadas,
        las cuales son: persona.csv, estudiante.csv, profesor.csv, administrativo.csv, 
        cursos.csv y notas.csv

        Cualquiera sea el tipo de consulta se debe presntar lo pedido en una 
        sola linea en la consola.*/

        # Elegir el tipo de consulta por consola
        echo "Ingrese el tipo de consulta: ";
        $tipo_consulta = trim(fgets(STDIN));

        # Carga académica acumulada
        if ($tipo_consulta == 1) {
            echo "Ingrese el RUN del estudiante: ";
            $run = trim(fgets(STDIN));

            /* Hay que acceder a notas.csv para obtener la
            sigla del curso y el resto de los datos, además
            hay que acceder a cursos.csv para obtener el nombre
            del curso en base a la sigla. */

            # Leer el archivo notas.csv
            $file = fopen('notas.csv', 'r');
            $notas = [];
            $firstLine = true;
            while (($line = fgetcsv($file)) !== FALSE) {
                if ($firstLine) {
                    $firstLine = false;
                    continue; // Ignorar la primera línea
                }
                $notas[] = $line;
            }
            fclose($file);

            # Leer el archivo cursos.csv
            $file = fopen('cursos.csv', 'r');
            $cursos = [];
            $firstLine = true;
            while (($line = fgetcsv($file)) !== FALSE) {
                if ($firstLine) {
                    $firstLine = false;
                    continue; // Ignorar la primera línea
                }
                $cursos[] = $line;
            }
            fclose($file);

            /* El PPS es el promedio de calificaciones semestrales
            es decir el promedio de todas las calificaciones obtenidas por el 
            estudiante durante el mismo periodo.
            
            Por otro lado el PPA es el promedio general de todos los cursos dados
            en todo en tiempo que haya estado, es decir, el promedio de todos los cursos
            realizados es decir no nulos.
            
            Nota: Ambas cosas solo se pueden calcular con calificaciones no nulas,
            es decir que no sean iguales a "x", cabe destacar que en notas
            el vacio se deja como "", dado que acepta nulos, por lo que para ese caso 
            debe ser distinto de "", en el caso de la calificación 
            se deja como "x" dado que no acepta nulos, 
            entonces para ese caso debe ser distinto de "x".
            
            Nota 2: El archivo notas tiene estas columnas:
            RUN,"Número de estudiante","sigla curso","Periodo curso",Calificación,Nota */

            $suma_notas = 0;
            $contador = 0;
            $periodo_actual = "";
            $promedio_semestre = 0;
            $promedio_total = 0;
            $cantidad_semestres = 0;
            $nombre_curso = "";

            foreach ($notas as $nota) {
                while ($nota[0] == $run) {
                    foreach ($cursos as $curso) {
                        if ($curso[0] == $nota[2]) {
                            $nombre_curso = $curso[1];
                            break;
                        }
                    }
                    break;
                }
                if ($nota[0] == $run) {
                    if ($periodo_actual != $nota[3]) {
                        if ($contador != 0) {
                            $promedio_semestre = $suma_notas / $contador;
                            echo "PPS: $promedio_semestre\n";
                            $promedio_total += $promedio_semestre;
                            $cantidad_semestres++;
                        }
                        $periodo_actual = $nota[3];
                        $suma_notas = 0;
                        $contador = 0;
                    }

                    echo "Periodo: $nota[3], Sigla: $nota[2], Curso: $nombre_curso, Nota: $nota[5], Calificación: $nota[4]\n";

                    if ($nota[4] != "x" && $nota[5] != "") {
                        $suma_notas += (float)$nota[5];
                        $contador++;
                    }
                }
            }
            if ($contador != 0) {
                echo "Ultimo semestre ->";
                $promedio_semestre = $suma_notas / $contador;
                echo "PPS: $promedio_semestre\n";
                $promedio_total += $promedio_semestre;
            }
            $promedio_total /= $cantidad_semestres;
            echo "PPA: $promedio_total\n";
            echo "Consulta realizada con éxito\n";
        } elseif ($tipo_consulta == 2) {
            echo "Ingrese el periodo del curso: ";
            $periodo = trim(fgets(STDIN));
            echo "Ingrese la sigla del curso: ";
            $sigla = trim(fgets(STDIN));


            /* Lista de curso,  cohorte, nombre completo, RUN y n´umero de estudiante
            Hay que acceder a notas.csv para el rut del estudiante y numero de estudiante
            luego a la tabla estudiante.csv para obtener la cohorte. en base al num de estudiante
            ademas acceder a persona.csv para obtener el nombre completo en base al rut. */

            # Leer el archivo notas.csv

            $file = fopen('notas.csv', 'r');
            $notas = [];
            $firstLine = true;
            while (($line = fgetcsv($file)) !== FALSE) {
                if ($firstLine) {
                    $firstLine = false;
                    continue; // Ignorar la primera línea
                }
                $notas[] = $line;
            }
            fclose($file);

            # Leer el archivo estudiante.csv
            $file = fopen('estudiante.csv', 'r');
            $estudiantes = [];
            $firstLine = true;
            while (($line = fgetcsv($file)) !== FALSE) {
                if ($firstLine) {
                    $firstLine = false;
                    continue; // Ignorar la primera línea
                }
                $estudiantes[] = $line;
            }
            fclose($file);

            # Leer el archivo persona.csv
            $file = fopen('persona.csv', 'r');
            $personas = [];
            $firstLine = true;
            while (($line = fgetcsv($file)) !== FALSE) {
                if ($firstLine) {
                    $firstLine = false;
                    continue; // Ignorar la primera línea
                }
                $personas[] = $line;
            }
            fclose($file);

            /* Nota: El archivo notas tiene estas columnas:
            RUN,"Número de estudiante","sigla curso","Periodo curso",Calificación,Nota 
            
            El archivo estudiante tiene estas columnas:
            Cohorte,"Número de estudiante","Código Plan","Último Logro",
            "Fecha Logro","Última toma de ramos"
            
            El archivo persona tiene estas columnnas:
            RUN,DV,Nombres,"Apellido Paterno","Apellido Materno","Nombre completo",
            Teléfono,"mail personal","mail institucional"*/

            foreach ($notas as $nota) {
                if ($nota[2] == $sigla && $nota[3] == $periodo) {
                    $run = $nota[0];
                    $numero_estudiante = $nota[1];
                    $cohorte = "";
                    $nombre_completo = "";
                    foreach ($estudiantes as $estudiante) {
                        if ($estudiante[1] == $numero_estudiante) {
                            $cohorte = $estudiante[0];
                            break;
                        }
                    }
                    foreach ($personas as $persona) {
                        if ($persona[0] == $run) {
                            $nombre_completo = $persona[5];
                            break;
                        }
                    }
                    echo "Cohorte: $cohorte, Nombre completo: $nombre_completo, RUN: $run, Número de estudiante: $numero_estudiante\n";
                }
            }
            echo "Consulta realizada con éxito\n";
        }
    }

    public function repetidosArray()
    {
        /* repetidosArray revisa que ninguna tabla tenga valores repetidos,
        las cuales son: persona.csv, estudiante.csv, profesor.csv, administrativo.csv, 
        cursos.csv y notas.csv */

        $tablas = [
            'persona.csv',
            'estudiante.csv',
            'profesor.csv',
            'administrativo.csv',
            'cursos.csv',
            'notas.csv'
        ];

        foreach ($tablas as $tabla) {
            $file = fopen($tabla, 'r');
            $data = [];
            $firstLine = true;
            while (($line = fgetcsv($file)) !== FALSE) {
                if ($firstLine) {
                    $firstLine = false;
                    continue; // Ignorar la primera línea
                }
                $data[] = $line;
            }
            fclose($file);

            $unique = array_unique($data, SORT_REGULAR);
            if (count($data) != count($unique)) {
                echo "La tabla $tabla tiene valores repetidos\n";
            } else {
                echo "La tabla $tabla no tiene valores repetidos\n";
            }
        }

        echo "Revisión de tablas completada\n";
    }

    private function escribirCSV($filename, $data)
    {
        $file = fopen($filename, 'w');
        if (!empty($data)) {
            fputcsv($file, array_keys(reset($data))); // Escribir encabezados
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
        }
        fclose($file);
    }

    private function reemplazarCaracteresEspeciales($array)
    {
        $caracteres_especiales = [
            '´a' => 'á',
            '´e' => 'é',
            '´i' => 'í',
            '´o' => 'ó',
            '´u' => 'ú',
            '´A' => 'Á',
            '´E' => 'É',
            '´I' => 'Í',
            '´O' => 'Ó',
            '´U' => 'Ú',
            '´n' => 'ñ',
            '´N' => 'Ñ',
            '´c' => 'ç',
            '´C' => 'Ç'
        ];

        foreach ($array as $key => $value) {
            foreach ($value as $subKey => $subValue) {

                /* Subvalue es un string que contiene la linea completa
                de datos, se debe separar cada dato por ";" */

                $datos = explode(";", $subValue);

                # Reemplazar caracteres especiales
                foreach ($datos as $index => $dato) {
                    foreach ($caracteres_especiales as $caracter => $reemplazo) {
                        $datos[$index] = str_replace($caracter, $reemplazo, $dato);
                    }
                }
            }
        }
        return $array;
    }
}
