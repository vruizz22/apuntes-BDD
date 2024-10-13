<?php
class Corrector
{
    public $conn;
    public $data_dir;
    public $asignaturas_file;
    public $docentes_file;
    public $estudiantes_file;
    public $errores_generales;

    public function __construct($env_string)
    {
        // Inicializar conexión
        $this->conn = pg_connect($env_string);

        // Verificar la conexión
        if (!$this->conn) {
            die("Error en la conexión con la base de datos: " .
                pg_last_error());
        }

        // La verdad no estoy muy seguro si esta ruta sea la correcta xdddd pero es la que sale en Filezilla
        $this->data_dir = '/home/grupo15/Sites/data';

        //Archivos a corregir: 
        //y guardar los datos erroneos en otro .csv (Idealmente creo yo deberiamos guardar todos los datos 
        //errados en un mismo .csv para no tener que hacer 10 nuevos .csv)

        $this->asignaturas_file = $this->data_dir . '/Asignaturas.csv';
        $this->docentes_file = $this->data_dir . '/Docentes_planificados.csv';
        $this->estudiantes_file = $this->data_dir . '/estudiantes.csv';


        // Archivo para registrar todos los errores
        $this->errores_generales = $this->data_dir . '/errores.csv';

        // Inicializar el archivo de errores y lo vaciamos si ya existe 
        file_put_contents($this->errores_generales, '');
    }

    // Función para procesar el archivo estudiantes.csv
    public function procesarEstudiantes()
    {
        $archivo_origen = 'estudiantes.csv';

        // Abrir el archivo de estudiantes con la codificación correcta
        if (($handle = fopen($this->estudiantes_file, 'r')) !== false) {
            // Leer la primera línea (encabezados)
            $headers = fgetcsv($handle, 1000, ",");

            // Convertir los encabezados a UTF-8
            $headers = array_map('convertirEncoding', $headers);

            // Iteramos sobre cada línea del archivo
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                // Convertir cada campo a UTF-8
                $data = array_map('convertirEncoding', $data);

                // Mapear los datos con los encabezados
                $fila = array_combine($headers, $data);

                // Extracción y limpieza de datos
                $codigo_plan = trim($fila['Código Plan']);
                $carrera = trim($fila['Carrera']);
                $cohorte = trim($fila['Cohorte']);
                $numero_alumno = trim($fila['Número de alumno']);
                $bloqueo = trim($fila['Bloqueo']);
                $causal_bloqueo = trim($fila['Causal Bloqueo']);
                $run = trim($fila['RUN']);
                $dv = strtoupper(trim($fila['DV']));
                $nombre1 = trim($fila['Nombre_1']);
                $nombre2 = trim($fila['Nombre_2']);
                $apellido_paterno = trim($fila['Primer Apellido']);
                $apellido_materno = trim($fila['Segundo Apellido']);
                $logro = trim($fila['Logro']);
                $fecha_logro = trim($fila['Fecha Logro']);
                $ultima_carga = trim($fila['Última Carga']);

                // Validaciones y correcciones
                $errores = [];

                // Ahora validamos el RUN (y el DV? No se)
                if (empty($run) || empty($dv)) {
                    $errores[] = "RUN o DV vacío";
                } else {
                    if (!$this->validarRUN($run)) {
                        $errores[] = "RUN inválido";
                    }
                }

                if (empty($run) || empty($dv)) {
                    $errores[] = "RUN o DV vacío";
                } else {
                    if (!$this->validarRUN($run, $dv)) {
                        $errores[] = "RUN inválido";
                    }
                }

                // Validar Número de alumno
                if (!is_numeric($numero_alumno)) {
                    $errores[] = "Número de alumno inválido";
                }

                // Validar Bloqueo, deberia bastar con eso?
                if ($bloqueo != 'S' && $bloqueo != 'N') {
                    $errores[] = "Bloqueo debe ser 'S' o 'N'";
                }

                // Si hay errores, registrar y continuar con la siguiente fila
                if (!empty($errores)) {
                    $this->registrarError($data, implode("; ", $errores), $archivo_origen);
                    continue;
                }

                // AHORA podemos insertar esos valores en la tabla de PERSONA

                $query_persona = "INSERT INTO Personas (RUN, DV, Nombres, Apellidos, Correos, Telefonos)
                                VALUES ($1, $2, $3, $4, NULL, NULL)
                                ON CONFLICT (RUN, DV) DO NOTHING";
                $params_persona = [
                    intval($run),
                    $dv,
                    $nombres_completos,
                    $apellidos_completos
                ];
                $result_persona = pg_query_params($this->conn, $query_persona, $params_persona);
                if (!$result_persona) {
                    $this->registrarError($this->errores_generales, $data, "Error al insertar en Personas: " . 
                    pg_last_error($this->conn), $archivo_origen);
                    continue;
                }

                // Insertar en la tabla Estudiantes
                $query_estudiante = "INSERT INTO Estudiantes (RUN, DV, Causal_de_bloqueo, Bloqueo, Numero_de_estudiante, Cohorte, Nombre_Carrera)
                                    VALUES ($1, $2, $3, $4, $5, $6, $7)
                                    ON CONFLICT (RUN, DV, Numero_de_estudiante) DO NOTHING";
                $params_estudiante = [
                    intval($run),
                    $dv,
                    $causal_bloqueo,
                    $bloqueo,
                    intval($numero_alumno),
                    $cohorte,
                    $carrera
                ];
                $result_estudiante = pg_query_params($this->conn, $query_estudiante, $params_estudiante);
                if (!$result_estudiante) {
                    $this->registrarError($this->errores_generales, $data, "Error al insertar en Estudiantes: " . 
                    pg_last_error($this->conn), $archivo_origen);
                    continue;
                }
            }
            fclose($handle);
        } else {
            die("Error al abrir el archivo estudiantes.csv");
        }
    }

    // Función para registrar errores en un archivo CSV único
    private function registrarError($datos, $mensaje, $archivo_origen)
    {
        $fp = fopen($this->errores_generales, 'a');
        if ($fp) {
            // Agregar el mensaje de error y el archivo de origen al final de los datos
            $datos[] = $mensaje;
            $datos[] = $archivo_origen;
            fputcsv($fp, $datos);
            fclose($fp);
        }
    }

    //Función para validar RUN
    private function validarRUN($run)
    {
        // Verificar que el RUN sea un número entero positivo y con una longitud correcta
        return is_numeric($run) && $run > 0 && strlen($run) >= 7 && strlen($run) <= 8;
    }

    //Función para validar Nombre
    private function validarNombre($nombre)
    {
        // Expresión regular que permite letras, espacios, guiones y apóstrofes
        return preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ' -]+$/u", $nombre);
    }

    //Función para corregir telefonos: 
    private function corregirTelefono($telefono)
    {
        // Eliminar caracteres no numéricos
        $telefono = preg_replace('/\D/', '', $telefono);
        // Si el teléfono tiene 8 dígitos, agregar un '9' al inicio
        if (strlen($telefono) == 8) {
            $telefono = '9' . $telefono;
        }
        return $telefono;
    }

    //Función para manejar el ENCODING de los caracteres 
    private function convertirEncoding($cadena)
    {
        // Detectar la codificación de la cadena
        $encoding = mb_detect_encoding($cadena, 'UTF-8, ISO-8859-1', true);
        // Convertir a UTF-8 
        if ($encoding != 'UTF-8') {
            $cadena = mb_convert_encoding($cadena, 'UTF-8', $encoding);
        }
        return $cadena;
    }

    private function closeConn()
    {
        // Cerrar la conexión a la base de datos
        pg_close($this->conn);
        echo "Procesamiento completado.\n";
    }
}
