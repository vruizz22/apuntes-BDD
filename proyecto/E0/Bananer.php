<?php
/* Este codigo es Bananer, una aplicación ejectuable por consola
que leera los datos desde los archivos CSV entregados */

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

        /* Los profesores y administrativos se identifican por RUN

        Los estudiantes se identifican por RUN o n´umero de estudiante. Un RUN puede tener m´as de un
        n´umero de estudiante, cada vez que la persona entra a primer a˜no en la universidad se le asigna un
        nuevo n´umero de estudiante. 
        
        Los estudiantes pueden hacer cambio de plan sin que ello implique un cambio de n´umero de estudiante.
        
        Un estudiante puede suspender estudios y al volver se le cambia la cohorte del periodo de retorno.
        
        Las notas van de 1.0 a 7.0 o vac´ıo. Vac´ıo se usa para los cursos que a´un no terminan o los cursos
        convalidados o eximidos 
        
        Los planes duran 10 semestres, cada semestre se llama nivel, los niveles son: Ingreso, 1. . . 10,
        licenciado. Ingreso corresponde a los estudiantes ingresados en el per´ıodo actual (periodo que no
        ha terminado), Licenciado corresponde a los que adem´as de cumplir exitosamente los 10 semestres,
        aprobaron el examen de grado */

        /* NOTA: Si encuentra un error de tipo codificaci´on (Por ejemplo caracter tilde no soportada ”%’a” reempl´acelo
        por el correcto ”´a” en el programa PHP. 
        Si encuentra un valor nulo y la especificaci´on indica no nulo, reempl´acelo por una marca de valor
        inv´alido (por ejemplo en calificaci´on, ”” por ”x” en el programa PHP */

        /* Formato correcto archivo 1:
        0 Cohorte: string, Perıodo de ingreso o re-ingreso del estudiante, no nulo.
        1 Codigo Plan: string, Codigo del plan de estudios, no nulo
        2 Plan: string, Nombre del plan de estudios, no nulo
        3 Bloqueo: Boolean, Indica si el estudiante tiene alguna restricci´on (deuda), no nulo: (N) o (S)
        4 RUN del estudiante: int, Rol ´Unico Nacional, no nulo
        5 DV del estudiante: char, D´ıgito verificador del RUN, no nulo
        6 Nombres: string, todos los nombres propios, no nulo
        7 Apellido Paterno: string, Apellido Paterno, no nulo
        8 Apellido Materno: string, Apellido Materno, admite nulos
        9 Nombre completo: string, Nombres + Apellido Paterno+Apellido Materno, no nulo
        10 N´umero de estudiante: int, identifica en forma ´unica a un estudiante y su plan de estudio, no nulo
        11 mail personal: string, Correo electr´onico personal, admite nulos
        12 mail institucional: string, Correo electr´onico institucional @lamejor.cl, admite nulos
        13 Periodo curso: string, periodo en que se curs´o o est´a cursando la asignatura, no nulo
        14 sigla curso: string, c´odigo que identifica a una asignatura, no nulo
        15 curso: string nombre de la asignatura, no nulo
        16 Secci´on: secci´on de la asignatura, no nulo
        17 Nivel del curso: semestre al que pertenece la asignatura en la malla, no nulo
        18 Calificaci´on: string, resultado conceptual de la evaluaci´on obtenida, no nulo
        19 Nota: float 1.0 a 7.0 o vac´ıo, resultado num´erico de la evaluaci´on obtenida, puede contener nulo
        20 ´Ultimo logro: Nivel del curso m´as atrasado aprobado por el estudiante, no nulo
        21 Fecha Logro: string, per´ıodo en que se obtuvo el ´Ultimo logro, no nulo
        22 ´Ultima toma de ramos: Per´ıodo de la ´ultima inscripci´on de asignaturas, no nulo */

        /* Formato correcto archivo 2:
        • RUN del profesor/administrativo: int, Rol ´Unico Nacional, no nulo
        • DV del profesor/administrativo: char, D´ıgito verificador del RUN, no nulo
        • mail personal: string, Correo electr´onico personal, admite nulos
        • mail institucional: string, Correo electr´onico institucional @lamejor.cl, admite nulos
        • tel´efono: int de 9 d´ıgitos, tel´efono personal, admite nulos
        • contrato: string, tipo de contrato (fuul, part-time, hora, etc.), admite nulos
        • jornada diurna: string, ”DIURNO”, admite nulos
        • jornada vespertina: string, ”vespertino”, admite nulos
        • dedicaci´on: string, horas del contrato, admite nulos
        • grado acad´emico:string, grado acad´emico ”Bachiller”, ”Licenciado”, ”Doctor”, admite nulos
        • jerarquia: String, jerarqu´ıa acad´emica, admite nulos
        • cargo: string, cargo administrativo, admite nulos */

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

        /* Las tablas a implementar son: 
        1.Persona: Datos comunes de la persona (sin registros duplicados)
        • RUN
        • DV
        • Nombres
        • Apellido Paterno
        • Apellido Materno
        • Nombre completo
        • Tel´efono
        • mail personal: string, Correo electr´onico personal
        • mail institucional: string, Correo electr´onico institucional @lamejor.cl
        
        2. Estudiante: Datos del estudiante (sin registros duplicados)
        • Cohorte
        • N´umero de estudiante
        • C´odigo Plan
        • ´ultimo Logro
        • Fecha Logro
        • ´Ultima toma de ramos
        
        3. Profesor: Datos de Profesor
        • RUN
        • DV
        • contrato: string, tipo de contrato (fuul, part-time, hora, etc.), admite nulos
        • Jornada: Diurno o Vespertino
        • dedicaci´on: string, horas del contrato, admite nulos
        • grado acad´emico:string, grado acad´emico ”Bachiller”, ”Licenciado”, ”Doctor”, admite nulos
        • jerarquia: String, jerarqu´ıa acad´emica, admite nulos
        • cargo: string, cargo administrativo, admite nulos
        
        4. Administrativo: Datos de Administrativo
        • RUN
        • DV
        • cargo: Cargo en la universidad, admite nulos
        
        5. Cursos: Stock de cursos y datos asociados
        • sigla curso, no nulo
        • curso, no nulo
        • Secciones: int, n´umero de secciones del curso, no nulo
        • Nivel del curso, no nulo
        
        6. Notas
        • RUN
        • N´umero de estudiante, no nulo
        • sigla curso, no nulo
        • Periodo curso, no nulo
        • Calificaci´on, no nulo
        • Nota, admite nulos*/

        # Tabla Persona
        $persona = [];
        foreach ($this->array1 as $key => $value) {
            foreach ($value as $subKey => $subValue) {
                $datos = explode(";", $subValue);
                if (isset($datos[4], $datos[5], $datos[6], $datos[7], $datos[8], $datos[9], $datos[11], $datos[12], $datos[13])) {
                    $persona[$datos[4]] = [
                        'RUN' => $datos[4],
                        'DV' => $datos[5],
                        'Nombres' => $datos[6],
                        'Apellido Paterno' => $datos[7],
                        'Apellido Materno' => $datos[8],
                        'Nombre completo' => $datos[9],
                        'Teléfono' => $datos[11],
                        'mail personal' => $datos[12],
                        'mail institucional' => $datos[13]
                    ];
                }
            }
        }

        # Tabla Estudiante
        $estudiante = [];
        foreach ($this->array1 as $key => $value) {
            foreach ($value as $subKey => $subValue) {
                $datos = explode(";", $subValue);
                if (isset($datos[0], $datos[10], $datos[1], $datos[20], $datos[21], $datos[22])) {
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
                if (isset($datos[0], $datos[1], $datos[5], $datos[6], $datos[7], $datos[8], $datos[9], $datos[10])) {
                    $profesor[$datos[0]] = [
                        'RUN' => $datos[0],
                        'DV' => $datos[1],
                        'contrato' => $datos[5],
                        'jornada' => $datos[6] == "DIURNO" ? "Diurno" : "Vespertino",
                        'dedicación' => $datos[7],
                        'grado académico' => $datos[8],
                        'jerarquía' => $datos[9],
                        'cargo' => $datos[10]
                    ];
                }
            }
        }

        # Tabla Administrativo
        $administrativo = [];
        foreach ($this->array2 as $key => $value) {
            foreach ($value as $subKey => $subValue) {
                $datos = explode(";", $subValue);
                if (isset($datos[0], $datos[1], $datos[10])) {
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
                if (isset($datos[4], $datos[10], $datos[14], $datos[13], $datos[18], $datos[19])) {
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

            /* El PPS es el promedio de calificaciones semestrales
            es decir el promedio de todas las calificaciones obtenidas por el 
            estudiante durante el mismo periodo.
            
            Por otro lado el PPA es el promedio general de todos los cursos dados
            en todo en tiempo que haya estado, es decir, el promedio de todos los cursos
            realizados es decir no nulos.
            
            Nota: Ambas cosas solo se pueden calcular con calificaciones no nulas,
            es decir que no sean iguales a "x", cabe destacar que en notas
            el vacio se deja como "", dado que acepta nulos, por lo que para ese caso debe ser distinto de "",
            en el caso de la calificación se deja como "x" dado que no acepta nulos, entonces
            para ese caso debe ser distinto de "x".
            
            Nota 2: El archivo notas tiene estas columnas:
            RUN,"Número de estudiante","sigla curso","Periodo curso",Calificación,Nota */

            $suma_calificaciones = 0;
            $suma_notas = 0;
            $contador = 0;
            $periodo_actual = "";
            $promedio_semestre = 0;
            $promedio_total = 0;
            $cantidad_semestres = 0;

            foreach ($notas as $nota) {
                if ($nota[0] == $run) {
                    if ($periodo_actual != $nota[3]) {
                        if ($contador != 0) {
                            $promedio_semestre = $suma_notas / $contador;
                            echo "PPS: $promedio_semestre\n";
                            $promedio_total += $promedio_semestre;
                            $cantidad_semestres++;
                        }
                        $periodo_actual = $nota[3];
                        $suma_calificaciones = 0;
                        $suma_notas = 0;
                        $contador = 0;
                    }
                    echo "Periodo: $nota[3], Sigla: $nota[2], Curso: $nota[2], Nota: $nota[5], Calificación: $nota[4]\n";
                    if ($nota[4] != "x" && $nota[5] != "") {
                        $suma_calificaciones += (float)$nota[4];
                        $suma_notas += (float)$nota[5];
                        $contador++;
                    }
                }
            }
            if ($contador != 0) {
                $promedio_semestre = $suma_notas / $contador;
                echo "PPS: $promedio_semestre\n";
                $promedio_total += $promedio_semestre;
            }
            $promedio_total /= $cantidad_semestres;
            echo "PPA: $promedio_total\n";
            echo "Consulta realizada con éxito\n";
        }

        # Lista de curso
        elseif ($tipo_consulta == 2) {
            echo "Ingrese el periodo del curso: ";
            $periodo = trim(fgets(STDIN));
            echo "Ingrese la sigla del curso: ";
            $sigla = trim(fgets(STDIN));

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

            /* Nota: El archivo notas tiene estas columnas:
            RUN,"Número de estudiante","sigla curso","Periodo curso",Calificación,Nota */

            foreach ($notas as $nota) {
                if ($nota[2] == $sigla && $nota[3] == $periodo) {
                    echo "Cohorte: $nota[1], Nombre completo: $nota[0], RUN: $nota[0], Número de estudiante: $nota[1]\n";
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

        $tablas = ['persona.csv', 'estudiante.csv', 'profesor.csv', 'administrativo.csv', 'cursos.csv', 'notas.csv'];

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
                # Subvalue es un string que contiene la linea completa
                # de datos, se debe separar cada dato por ";"
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
