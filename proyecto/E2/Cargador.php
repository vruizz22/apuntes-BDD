<?php
class Cargador
{
    public $conn;
    public $tablas; # Array de tablas en formato de string

    public function __construct($env_string)
    {
        // Inicializar conexión
        $this->conn = pg_connect($env_string);

        // Verificar la conexión
        if (!$this->conn) {
            die("Error en la conexión con la base de datos: " .
                pg_last_error());
        }

        // Crear array de tablas en formato de string
        $this->tablas = array(
            "Personas",
            "Estudiantes",
            "Academicos",
            "Administrativos",
            "Cursos",
            "Cursos_Equivalencias",
            "Cursos_Prerequisitos",
            "Cursos_Minimos",
            "Planes_Estudio",
            "Programacion_Academica",
            "Departamento",
            "Nota",
            "Avance_Academico"
        );
    }

    public function CrearTablas()
    {
        // Eliminar las tablas si existen
        foreach ($this->tablas as $tabla) {
            $result = pg_query($this->conn, "DROP TABLE IF EXISTS {$tabla} CASCADE");
            if (!$result) {
                die("Error en la eliminación de la tabla: " .
                    pg_last_error());
            }
        }

        // Creamos las tablas
        $Personas = "CREATE TABLE Personas(
            RUN int NOT NULL,
            DV varchar(2) NOT NULL,
            Nombre_1 varchar(100) NOT NULL,
            Nombre_2 varchar(100),
            Apellido_1 varchar(100) NOT NULL,
            Apellido_2 varchar(100),
            Correos varchar(100),
            Telefonos varchar(100),
            PRIMARY KEY (RUN, DV)
        )";

        $Estudiantes = "CREATE TABLE Estudiantes(
            RUN int NOT NULL,
            DV varchar(2) NOT NULL,
            Causal_de_bloqueo TEXT,
            Bloqueo varchar(1) NOT NULL,
            Numero_de_estudiante int NOT NULL,
            Cohorte varchar(100) NOT NULL,
            Nombre_Carrera varchar(100) NOT NULL,
            FOREIGN KEY (RUN, DV) REFERENCES Personas(RUN, DV),                                                         
            PRIMARY KEY (RUN, DV, Numero_de_estudiante)
        )";

        $Academicos = "CREATE TABLE Academicos(
            RUN int NOT NULL,
            DV varchar(2) NOT NULL,
            Estamento varchar(100),
            Grado_academico varchar(100),
            Contrato varchar(100),
            Jerarquia varchar(100),
            Jornada varchar(100),
            FOREIGN KEY (RUN, DV) REFERENCES Personas(RUN, DV),
            PRIMARY KEY (RUN, DV)
        )";

        $Administrativos = "CREATE TABLE Administrativos(
            RUN int NOT NULL,
            DV varchar(2) NOT NULL,
            Estamento varchar(100),
            Grado_academico varchar(100),
            Contrato varchar(100),
            Cargo varchar(100),
            FOREIGN KEY (RUN, DV) REFERENCES Personas(RUN, DV),
            PRIMARY KEY (RUN, DV)
        )";

        $Cursos = "CREATE TABLE Cursos(
            Sigla_curso varchar(100) NOT NULL,
            Seccion_curso int NOT NULL,
            Periodo_curso varchar(100) NOT NULL,
            Nombre varchar(100) NOT NULL,
            Nivel int NOT NULL,
            Ciclo varchar(100),
            Tipo varchar(100),
            Oportunidad varchar(3),
            Duración varchar(1) NOT NULL,
            Nombre_Departamento varchar(100) NOT NULL,
            Código_Departamento varchar(100) NOT NULL,
            RUN_Academico int,
            DV_Academico varchar(2),
            Nombre_Academico varchar(100) DEFAULT 'POR DESIGNAR',
            Apellido1_Academico varchar(100),
            Apellido2_Academico varchar(100),
            Principal varchar(1),
            FOREIGN KEY (Nombre_Departamento, Código_Departamento) REFERENCES Departamento(Nombre, Código),
            FOREIGN KEY (RUN_Academico, DV_Academico) REFERENCES Academicos(RUN, DV),
            PRIMARY KEY (Sigla_curso, Seccion_curso, Periodo_curso)
        )";

        $Cursos_Equivalencias = "CREATE TABLE Cursos_Equivalencias(
            Sigla_curso varchar(100) NOT NULL,
            Sigla_equivalente int NOT NULL,
            Ciclo varchar(100),
            PRIMARY KEY (Sigla_curso, Sigla_equivalente),
            FOREIGN KEY (Sigla_curso) REFERENCES Cursos(Sigla_curso)
        )";

        $Cursos_Prerequisitos = "CREATE TABLE Cursos_Prerequisitos(
            Sigla_curso varchar(100) NOT NULL,
            Sigla_prerequisito int NOT NULL,
            Ciclo varchar(100),
            PRIMARY KEY (Sigla_curso, Sigla_prerequisito),
            FOREIGN KEY (Sigla_curso) REFERENCES Cursos(Sigla_curso)
        )";

        $Cursos_Minimos = "CREATE TABLE Cursos_Minimos(
            Sigla_curso varchar(100) NOT NULL,
            Sigla_minimo int NOT NULL,
            Ciclo varchar(100),
            Nombre varchar(100),
            Tipo varchar(100),
            Nivel float,
            PRIMARY KEY (Sigla_curso, Sigla_minimo),
            FOREIGN KEY (Sigla_curso) REFERENCES Cursos(Sigla_curso)
        )";

        $Planes_Estudio = "CREATE TABLE Planes_Estudio(
            Código_Plan varchar(100) PRIMARY KEY UNIQUE NOT NULL,
            Inicio_Vigencia DATE NOT NULL,
            Jornada varchar(100) NOT NULL,
            Modalidad varchar(100) NOT NULL,
            Sede varchar(100) NOT NULL,
            Plan varchar(100) NOT NULL,
            Nombre_Facultad varchar(100) NOT NULL,
            Grado varchar(100) NOT NULL,
            Nombre_Carrera varchar(100) NOT NULL
        )";

        $Programacion_Academica = "CREATE TABLE Programacion_Academica(
            Sigla_curso varchar(100) NOT NULL,
            Seccion_curso int NOT NULL,
            Código_Plan varchar(100) NOT NULL,
            Periodo_Oferta varchar(100) NOT NULL,
            Cupos int NOT NULL,
            Sala varchar(100) NOT NULL,
            Hora_Inicio TIME NOT NULL,
            Hora_Fin TIME NOT NULL,
            Fecha_Inicio DATE NOT NULL,
            Fecha_Fin DATE NOT NULL,
            Inscritos int NOT NULL,
            PRIMARY KEY (Sigla_curso, Seccion_curso, Código_Plan, Periodo_Oferta),
            FOREIGN KEY (Sigla_curso, Seccion_curso, Periodo_Oferta) REFERENCES Nota(Sigla_curso, Seccion_curso, Periodo_Curso),
            FOREIGN KEY (Código_Plan) REFERENCES Planes_Estudio(Código_Plan)
        )";

        $Departamento = "CREATE TABLE Departamento(
            Nombre varchar(100) NOT NULL,
            Código varchar(100) NOT NULL,
            Nombre_Facultad varchar(100) NOT NULL,
            PRIMARY KEY (Nombre, Código)
        )";

        $Nota = "CREATE TABLE Nota(
            Sigla_curso varchar(100) NOT NULL,
            Seccion_curso int NOT NULL,
            Periodo_curso varchar(100) NOT NULL,
            Numero_de_estudiante int NOT NULL,
            RUN int NOT NULL,
            DV varchar(2) NOT NULL,
            Nota float,
            Descripción varchar(100),
            Resultado varchar(100),
            Calificación varchar(100),
            PRIMARY KEY (Sigla_curso, Seccion_curso, Periodo_curso, RUN, DV, Numero_de_estudiante),
            FOREIGN KEY (Sigla_curso, Seccion_curso, Periodo_curso) REFERECES Cursos(Sigla_curso, Seccion_curso, Periodo_curso),
            FOREIGN KEY (RUN, DV, Numero_de_estudiante) REFERENCES Estudiantes(RUN, DV, Numero_de_estudiante)
        )";

        $Avance_Academico = "CREATE TABLE Avance_Academico(
            Sigla_curso varchar(100) NOT NULL,
            Seccion_curso int NOT NULL,
            Periodo_curso varchar(100) NOT NULL,
            RUN int NOT NULL,
            DV varchar(2) NOT NULL,
            Numero_de_estudiante int NOT NULL,
            Periodo_Oferta varchar(100) NOT NULL,
            Nota float,
            Descripción varchar(100),
            Resultado varchar(100),
            Calificación varchar(100),
            Ultima_Carga varchar(100),
            Ultimo_Logro varchar(100) NOT NULL,
            PRIMARY KEY (Sigla_curso, Seccion_curso, Periodo_curso, RUN, DV, Numero_de_estudiante, Ultimo_Logro),
            FOREIGN KEY (Sigla_curso, Seccion_curso, Periodo_curso) REFERENCES Cursos(Sigla_curso, Seccion_curso, Periodo_curso),
            FOREIGN KEY (RUN, DV, Numero_de_estudiante) REFERENCES Estudiantes(RUN, DV, Numero_de_estudiante)
        )";

        // Guardamos todas las tablas en un array
        $tablas = array(
            $Personas,
            $Planes_Estudio,
            $Estudiantes,
            $Academicos,
            $Administrativos,
            $Departamento,
            $Cursos,
            $Cursos_Equivalencias,
            $Cursos_Prerequisitos,
            $Cursos_Minimos,
            $Programacion_Academica,
            $Nota,
            $Avance_Academico
        );

        // Ejecutamos la query para todas las tablas
        foreach ($tablas as $tabla) {
            $result = pg_query($this->conn, $tabla);
            if (!$result) {
                die("Error en la creación de la tabla: " .
                    pg_last_error());
            }
        }

        // Creamos todos los triggers
        $this->CrearTriggers();
    }

    public function CargarDatos()
    {
        $nombre_archivos = array(
            'Asignaturas',
            'Docentes_planificados',
            'Estudiantes',
            'Notas',
            'Planeacion',
            'Planes',
            'Prerequisitos',
            'Planes_Magia',
            'Planes_Hechiceria',
            'Malla_Hechiceria',
            'Malla_Magia'
        );

        $ruta_base = __DIR__ . DIRECTORY_SEPARATOR . 'data';
        $ruta_datos = array_map(function ($nombre) use ($ruta_base) {
            return $ruta_base . DIRECTORY_SEPARATOR . $nombre . '.csv';
        }, $nombre_archivos);

        // Leer los datos de los csv
        $datos_array = array_map(function ($ruta) {
            return $this->LeerArchivo($ruta);
        }, $ruta_datos);

        // Combinar los nombres de los archivos con los datos leídos
        # Diccionario que contiene los datos de los archivos asociados a su nombre
        $datos = array_combine($nombre_archivos, $datos_array);

        // Crear tablas temporales
        $this->CrearTablasTemporales();

        // Insertar datos en las tablas temporales
        $this->InsertarDatosTemporales($datos);

        // Insertar datos en las tablas finales
        // Insertar datos en la tabla Personas
        $query = "INSERT INTO Personas (RUN, DV, Nombre_1, Nombre_2, Apellido_1, Apellido_2, Correos, Telefonos)
            SELECT DISTINCT
                COALESCE(TempEstudiantes.RUN, TempDocentesPlanificados.RUN, TempNotas.RUN) AS RUN,
                COALESCE(TempEstudiantes.DV, TempNotas.DV) AS DV,
                COALESCE(TempEstudiantes.Nombre_1, TempDocentesPlanificados.Nombre, TempNotas.Nombres) AS Nombre_1,
                TempEstudiantes.Nombre_2 AS Nombre_2,
                COALESCE(TempEstudiantes.Primer_Apellido, TempDocentesPlanificados.Apellido_P, TempNotas.Apellido_Paterno) AS Apellido_1,
                COALESCE(TempEstudiantes.Segundo_Apellido, TempNotas.Apellido_Materno) AS Apellido_2,
                COALESCE(TempDocentesPlanificados.Email_personal, TempDocentesPlanificados.Email_institucional) AS Correos,
                TempDocentesPlanificados.Telefono AS Telefonos
            FROM
                TempEstudiantes
            LEFT JOIN TempDocentesPlanificados
                ON TempEstudiantes.RUN = TempDocentesPlanificados.RUN
            LEFT JOIN TempNotas
                ON TempEstudiantes.RUN = TempNotas.RUN
        ";
        $this->InsertarDatosFinales($query, 'Personas');

        // Insertar datos en la tabla Estudiantes
        $query = "INSERT INTO Estudiantes (RUN, DV, Causal_de_bloqueo, Bloqueo, Numero_de_estudiante, Cohorte, Nombre_Carrera)
            SELECT
                COALESCE(TempEstudiantes.RUN, TempNotas.RUN) AS RUN,
                COALESCE(TempEstudiantes.DV, TempNotas.DV) AS DV,
                TempEstudiantes.Causal_Bloqueo AS Causal_de_bloqueo,
                TempEstudiantes.Bloqueo AS Bloqueo,
                COALESCE(TempEstudiantes.Numero_de_alumno, TempNotas.Numero_de_alumno) AS Numero_de_estudiante,
                COALESCE(TempEstudiantes.Cohorte, TempNotas.Cohorte) AS Cohorte,
                COALESCE(TempEstudiantes.Carrera, TempNotas.Plan) AS Nombre_Carrera
            FROM
                TempEstudiantes
            LEFT JOIN TempNotas
                ON TempEstudiantes.RUN = TempNotas.RUN
        ";
        $this->InsertarDatosFinales($query, 'Estudiantes');

        // Insertar datos en la tabla Academicos
        $query = "INSERT INTO Academicos (RUN, DV, Estamento, Grado_academico, Contrato, Jerarquia, Jornada)
            SELECT DISTINCT
                COALESCE(TempDocentesPlanificados.RUN, TempNotas.RUN, TempEstudiantes.RUN) AS RUN,
                COALESCE(TempNotas.DV, TempEstudiantes.DV) AS DV,
                TempDocentesPlanificados.Estamento AS Estamento,
                TempDocentesPlanificados.Grado_academico AS Grado_academico,
                TempDocentesPlanificados.Contrato AS Contrato,
                TempDocentesPlanificados.Jerarquia AS Jerarquia,
                COALESCE(TempDocentesPlanificados.Diurno, TempDocentesPlanificados.Vespertino) AS Jornada
            FROM
                TempDocentesPlanificados
            LEFT JOIN TempNotas
                ON TempDocentesPlanificados.RUN = TempNotas.RUN
            LEFT JOIN TempEstudiantes
                ON TempDocentesPlanificados.RUN = TempEstudiantes.RUN
            WHERE TempDocentesPlanificados.Estamento = 'Académico'
        ";
        $this->InsertarDatosFinales($query, 'Academicos');

        // Insertar datos en la tabla Administrativos
        $query = "INSERT INTO Administrativos (RUN, DV, Estamento, Grado_academico, Contrato, Cargo)
            SELECT DISTINCT
                COALESCE(TempDocentesPlanificados.RUN, TempNotas.RUN, TempEstudiantes.RUN) AS RUN,
                COALESCE(TempNotas.DV, TempEstudiantes.DV) AS DV,
                TempDocentesPlanificados.Estamento AS Estamento,
                TempDocentesPlanificados.Grado_academico AS Grado_academico,
                TempDocentesPlanificados.Contrato AS Contrato,
                TempDocentesPlanificados.Cargo AS Cargo
            FROM
                TempDocentesPlanificados
            LEFT JOIN TempNotas
                ON TempDocentesPlanificados.RUN = TempNotas.RUN
            LEFT JOIN TempEstudiantes
                ON TempDocentesPlanificados.RUN = TempEstudiantes.RUN
            WHERE TempDocentesPlanificados.Estamento = 'Administrativo'
        ";
        $this->InsertarDatosFinales($query, 'Administrativos');

        // Insertar datos en la tabla Cursos
        $query = "INSERT INTO Cursos (Sigla_curso, Seccion_curso, Periodo_curso, Nombre, Nivel, Ciclo, Tipo, Oportunidad, Duración, Nombre_Departamento, Código_Departamento, RUN_Academico, DV_Academico, Nombre_Academico, Apellido1_Academico, Apellido2_Academico, Principal)
            SELECT
                TempAsignaturas.Asignatura_id AS Sigla_curso,
                TempPlaneacion.Seccion AS Seccion_curso,
                TempPlaneacion.Periodo AS Periodo_curso,
                TempAsignaturas.Asignatura AS Nombre,
                TempAsignaturas.Nivel AS Nivel,
                TempAsignaturas.Ciclo AS Ciclo,
                '' AS Tipo, -- Valor desconocido.
                TempNotas.Convocatoria AS Oportunidad,
                TempPlaneacion.Duracion AS Duración,
                TempPlaneacion.Departamento AS Nombre_Departamento,
                TempPlaneacion.Codigo_Depto AS Código_Departamento,
                TempDocentesPlanificados.RUN AS RUN_Academico,
                'X' AS DV_Academico, -- Valor desconocido.
                TempDocentesPlanificados.Nombre AS Nombre_Academico,
                COALESCE(TempDocentesPlanificados.Apellido_P, 'DESCONOCIDO') AS Apellido1_Academico,
                COALESCE(TempDocentesPlanificados.Apellido_M, 'DESCONOCIDO') AS Apellido2_Academico,
                TempPlaneacion.Profesor_Principal AS Principal
            FROM
                TempAsignaturas
            JOIN TempPlaneacion ON TempAsignaturas.Asignatura_id = TempPlaneacion.Id_Asignatura
            LEFT JOIN TempNotas ON TempAsignaturas.Asignatura_id = TempNotas.Codigo_Asignatura
            LEFT JOIN TempDocentesPlanificados ON TempPlaneacion.RUN = TempDocentesPlanificados.RUN
        ";
        $this->InsertarDatosFinales($query, 'Cursos');

        // Insertar datos en la tabla Cursos_Equivalencias
        $query = "INSERT INTO Cursos_Equivalencias (Sigla_curso, Sigla_equivalente, Ciclo)
            SELECT
                -- Vamos a llamar 2 veces a la tabla TempAsignaturas, una para el curso y otra para el equivalente
                A1.Asignatura_id AS Sigla_curso,
                A2.Asignatura_id AS Sigla_equivalente,
                A1.Ciclo AS Ciclo
            FROM
                TempAsignaturas A1
            JOIN TempAsignaturas A2 
                -- Extraemos el código de asignatura eliminando el plan
                ON SUBSTRING(A1.Asignatura_id, LENGTH(A1.Plan) + 1) = SUBSTRING(A2.Asignatura_id, LENGTH(A2.Plan) + 1)
                -- Nos aseguramos que los planes sean distintos
                AND A1.Plan != A2.Plan
        ";
        $this->InsertarDatosFinales($query, 'Cursos_Equivalencias');

        // Insertar datos en la tabla Cursos_Prerequisitos
        $query = "INSERT INTO Cursos_Prerequisitos (Sigla_curso, Sigla_prerequisito, Ciclo)
            SELECT
                TempAsignaturas.Asignatura_id AS Sigla_curso,
                TempPrerequisitos.Prerequisitos AS Sigla_prerequisito,
                TempAsignaturas.Ciclo AS Ciclo
            FROM
                TempAsignaturas
            JOIN TempPrerequisitos ON TempAsignaturas.Asignatura_id = TempPrerequisitos.Asignatura_id
        ";
        $this->InsertarDatosFinales($query, 'Cursos_Prerequisitos');

        // Insertar datos en la tabla Cursos_Minimos
        $query = "INSERT INTO Cursos_Minimos (Sigla_curso, Sigla_minimo, Ciclo, Nombre, Tipo, Nivel)
            SELECT
                TempAsignaturas.Asignatura_id AS Sigla_curso,
                TempMallaMagia.Col1 AS Sigla_minimo,
                -- Buscamos el nombre en base alguna sigla que contenga MallaMagia.Col1
                -- Agregamos el plan al codigo de la asignatura para encontrar el nombre
                (SELECT Asignatura FROM TempAsignaturas WHERE Plan || TempMallaMagia.Col1 = Asignatura_id LIMIT 1) AS Nombre,
                '' AS Tipo, -- Valor desconocido.
                -- Buscamos el nivel en base alguna sigla que contenga MallaMagia.Col1
                -- Agregamos el plan al codigo de la asignatura para encontrar el nivel
                (SELECT Nivel FROM TempAsignaturas WHERE Plan || TempMallaMagia.Col1 = Asignatura_id LIMIT 1) AS Nivel
            FROM
                TempAsignaturas
            JOIN TempMallaMagia ON TempPrequisitos.Prerequisitos = TempMallaMagia.Col1
        ";
        $this->InsertarDatosFinales($query, 'Cursos_Minimos');

        // Insertar datos en la tabla Planes_Estudio
        $query = "INSERT INTO Planes_Estudio (Código_Plan, Inicio_Vigencia, Jornada, Modalidad, Sede, Plan, Nombre_Facultad, Grado, Nombre_Carrera)
            SELECT
                TempPlanes.Codigo_Plan AS Código_Plan,
                TempPlanes.Inicio_Vigencia AS Inicio_Vigencia,
                TempPlanes.Jornada AS Jornada,
                TempPlanes.Modalidad AS Modalidad,
                TempPlanes.Sede AS Sede,
                TempPlanes.Plan AS Plan,
                TempPlanes.Facultad AS Nombre_Facultad,
                TempPlanes.Grado AS Grado,
                TempPlanes.Carrera AS Nombre_Carrera
            FROM TempPlanes
        ";
        $this->InsertarDatosFinales($query, 'Planes_Estudio');

        // Insertar datos en la tabla Programacion_Academica
        $query = "INSERT INTO Programacion_Academica (Sigla_curso, Seccion_curso, Código_Plan, Periodo_Oferta, Cupos, Sala, Hora_Inicio, Hora_Fin, Fecha_Inicio, Fecha_Fin, Inscritos)
            SELECT
                TempAsignaturas.Asignatura_id AS Sigla_curso,
                TempPlaneacion.Seccion AS Seccion_curso,
                TempPlaneacion.Codigo_Depto AS Código_Plan,
                TempPlaneacion.Periodo AS Periodo_Oferta,
                TempPlaneacion.Cupo AS Cupos,
                TempPlaneacion.Lugar AS Sala,
                TempPlaneacion.Hora_Inicio AS Hora_Inicio,
                TempPlaneacion.Hora_Fin AS Hora_Fin,
                TempPlaneacion.Fecha_Inicio AS Fecha_Inicio,
                TempPlaneacion.Fecha_Fin AS Fecha_Fin,
                TempPlaneacion.Inscritos AS Inscritos
            FROM
                TempAsignaturas
            JOIN TempPlaneacion ON TempAsignaturas.Asignatura_id = TempPlaneacion.Id_Asignatura
        ";
        $this->InsertarDatosFinales($query, 'Programacion_Academica');

        // Insertar datos en la tabla Departamento
        $query = "INSERT INTO Departamento (Nombre, Código, Nombre_Facultad)
            SELECT DISTINCT
                TempPlaneacion.Departamento AS Nombre,
                TempPlaneacion.Codigo_Depto AS Código,
                TempPlaneacion.Facultad AS Nombre_Facultad
            FROM TempPlaneacion
        ";
        $this->InsertarDatosFinales($query, 'Departamento');

        // Insertar datos en la tabla Nota
        $query = "INSERT INTO Nota (Sigla_curso, Seccion_curso, Periodo_curso, Numero_de_estudiante, RUN, DV, Nota, Descripción, Resultado, Calificación)
            SELECT
                TempAsignaturas.Asignatura_id AS Sigla_curso,
                TempPlaneacion.Seccion AS Seccion_curso,
                TempNotas.Periodo_Asignatura AS Periodo_curso,
                TempNotas.Numero_de_alumno AS Numero_de_estudiante,
                TempNotas.RUN AS RUN,
                TempNotas.DV AS DV,
                TempNotas.Nota AS Nota,
                '' AS Descripción, -- Valor a agregar con la función actualizar_nota
                '' AS Resultado, -- Valor a agregar con la función actualizar_nota
                TempNotas.Calificacion AS Calificación
            FROM
                TempAsignaturas
            JOIN TempPlaneacion ON TempAsignaturas.Asignatura_id = TempPlaneacion.Id_Asignatura
            JOIN TempNotas ON TempAsignaturas.Asignatura_id = TempNotas.Codigo_Asignatura
        ";
        $this->InsertarDatosFinales($query, 'Nota');

        // Insertar datos en la tabla Avance_Academico
        $query = "INSERT INTO Avance_Academico (Sigla_curso, Seccion_curso, Periodo_curso, RUN, DV, Numero_de_estudiante, Periodo_Oferta, Nota, Descripción, Ultima_Carga, Ultimo_Logro)
            SELECT
                TempAsignaturas.Asignatura_id AS Sigla_curso,
                TempPlaneacion.Seccion AS Seccion_curso,
                TempNotas.Periodo_Asignatura AS Periodo_curso,
                TempNotas.RUN AS RUN,
                TempNotas.DV AS DV,
                TempNotas.Numero_de_alumno AS Numero_de_estudiante,
                TempNotas.Periodo_Asignatura AS Periodo_Oferta,
                TempNotas.Nota AS Nota,
                '' AS Descripción, -- Valor a agregar con la función actualizar_nota
                '' AS Resultado, -- Valor a agregar con la función actualizar_nota
                TempNotas.Calificacion AS Calificación,
                TempEstudiantes.Ultima_Carga AS Ultima_Carga,
                TempEstudiantes.Logro AS Ultimo_Logro
            FROM
                TempAsignaturas
            JOIN TempPlaneacion ON TempAsignaturas.Asignatura_id = TempPlaneacion.Id_Asignatura
            JOIN TempNotas ON TempAsignaturas.Asignatura_id = TempNotas.Codigo_Asignatura
            JOIN TempEstudiantes ON TempNotas.RUN = TempEstudiantes.RUN
        ";
        $this->InsertarDatosFinales($query, 'Avance_Academico');

        // Eliminar las tablas temporales
        $this->EliminarTablasTemporales();

        // Cerrar la conexión
        pg_close($this->conn);
    }

    private function CrearTablasTemporales()
    {
        $queries = [
            "CREATE TEMP TABLE TempAsignaturas (
                Plan VARCHAR(100),
                Asignatura_id VARCHAR(100),
                Asignatura VARCHAR(100),
                Nivel VARCHAR(100),
                Ciclo VARCHAR(100)
            )",
            "CREATE TEMP TABLE TempPlaneacion (
                Periodo VARCHAR(100),
                Sede VARCHAR(100),
                Facultad VARCHAR(100),
                Codigo_Depto VARCHAR(100),
                Departamento VARCHAR(100),
                Id_Asignatura VARCHAR(100),
                Asignatura VARCHAR(100),
                Seccion VARCHAR(100),
                Duracion VARCHAR(100),
                Jornada VARCHAR(100),
                Cupo INT,
                Inscritos INT,
                Dia VARCHAR(100),
                Hora_Inicio TIME,
                Hora_Fin TIME,
                Fecha_Inicio DATE,
                Fecha_Fin DATE,
                Lugar VARCHAR(100),
                Edificio VARCHAR(100),
                Profesor_Principal VARCHAR(100),
                RUN VARCHAR(100),
                Nombre_Docente VARCHAR(100),
                Apellido_Docente_1 VARCHAR(100),
                Apellido_Docente_2 VARCHAR(100),
                Jerarquizacion VARCHAR(100)
            )",
            "CREATE TEMP TABLE TempEstudiantes (
                Codigo_Plan VARCHAR(100),
                Carrera VARCHAR(100),
                Cohorte VARCHAR(100),
                Numero_de_alumno INT,
                Bloqueo varchar(1),
                Causal_Bloqueo TEXT,
                RUN INT,
                DV VARCHAR(2),
                Nombre_1 VARCHAR(100),
                Nombre_2 VARCHAR(100),
                Primer_Apellido VARCHAR(100),
                Segundo_Apellido VARCHAR(100),
                Logro VARCHAR(100),
                Fecha_Logro DATE,
                Ultima_Carga DATE
            )",
            "CREATE TEMP TABLE TempNotas (
                Codigo_Plan VARCHAR(100),
                Plan VARCHAR(100),
                Cohorte VARCHAR(100),
                Sede VARCHAR(100),
                RUN INT,
                DV VARCHAR(2),
                Nombres VARCHAR(100),
                Apellido_Paterno VARCHAR(100),
                Apellido_Materno VARCHAR(100),
                Numero_de_alumno INT,
                Periodo_Asignatura VARCHAR(100),
                Codigo_Asignatura VARCHAR(100),
                Asignatura VARCHAR(100),
                Convocatoria VARCHAR(100),
                Calificacion VARCHAR(100),
                Nota FLOAT
            )",
            "CREATE TEMP TABLE TempDocentesPlanificados (
                RUN INT,
                Nombre VARCHAR(100),
                Apellido_P VARCHAR(100),
                Telefono INT,
                Email_personal VARCHAR(100),
                Email_institucional VARCHAR(100),
                Dedicacion VARCHAR(100),
                Contrato VARCHAR(100),
                Diurno varchar(100),
                Vespertino varchar(100),
                Sede VARCHAR(100),
                Carrera VARCHAR(100),
                Grado_academico VARCHAR(100),
                Jerarquia VARCHAR(100),
                Cargo VARCHAR(100),
                Estamento VARCHAR(100)
            )",
            "CREATE TEMP TABLE TempPlanes (
                Codigo_Plan VARCHAR(100),
                Facultad VARCHAR(100),
                Carrera VARCHAR(100),
                Plan VARCHAR(100),
                Jornada VARCHAR(100),
                Sede VARCHAR(100),
                Grado VARCHAR(100),
                Modalidad VARCHAR(100),
                Inicio_Vigencia DATE
            )",
            "CREATE TEMP TABLE TempPrerequisitos (
                Plan VARCHAR(100),
                Asignatura_id VARCHAR(100),
                Asignatura VARCHAR(100),
                Nivel float,
                Prerequisitos VARCHAR(100),
                Prerequisitos_1 int
            )",
            "CREATE TEMP TABLE TempPlanesMagia (
                Planes_Vigentes VARCHAR(100)
            )",
            "CREATE TEMP TABLE TempPlanesHechiceria (
                Planes_Vigentes VARCHAR(100)
            )",
            "CREATE TEMP TABLE TempMallaMagia (
                Col1 VARCHAR(100),
                Col2 VARCHAR(100),
                Col3 VARCHAR(100),
                Col4 VARCHAR(100),
                Col5 VARCHAR(100),
                Col6 VARCHAR(100),
                Col7 VARCHAR(100),
                Col8 VARCHAR(100),
                Col9 VARCHAR(100),
                Col10 VARCHAR(100)
            )",
            "CREATE TEMP TABLE TempMallaHechiceria (
                Col1 VARCHAR(100),
                Col2 VARCHAR(100),
                Col3 VARCHAR(100),
                Col4 VARCHAR(100),
                Col5 VARCHAR(100),
                Col6 VARCHAR(100),
                Col7 VARCHAR(100),
                Col8 VARCHAR(100),
                Col9 VARCHAR(100),
                Col10 VARCHAR(100)
            )"
        ];
        foreach ($queries as $query) {
            $result = pg_query($this->conn, $query);
            if (!$result) {
                die("Error en la creación de la tabla temporal: " . pg_last_error());
            }
        }
    }

    private function InsertarDatosTemporales($datos)
    {
        foreach ($datos['Asignaturas'] as $asignatura) {
            $query = "INSERT INTO TempAsignaturas (Plan, Asignatura_id, Asignatura, Nivel, Ciclo) VALUES (
                '{$asignatura[0]}',  -- Plan
                '{$asignatura[1]}',  -- Asignatura_id
                '{$asignatura[2]}',  -- Asignatura
                '{$asignatura[3]}',  -- Nivel
                '{$asignatura[4]}'   -- Ciclo
            )";
            $result = pg_query($this->conn, $query);
            if (!$result) {
                die("Error en la inserción de datos en la tabla temporal TempAsignaturas: " . pg_last_error());
            }
        }

        foreach ($datos['Planeacion'] as $planeacion) {
            $fechaInicio = DateTime::createFromFormat('d/m/y', $planeacion[15])->format('Y-m-d');
            $fechaFin = DateTime::createFromFormat('d/m/y', $planeacion[16])->format('Y-m-d');

            $query = "INSERT INTO TempPlaneacion (Periodo, Sede, Facultad, Codigo_Depto, Departamento, Id_Asignatura, Asignatura, Seccion, Duracion, Jornada, Cupo, Inscritos, Dia, Hora_Inicio, Hora_Fin, Fecha_Inicio, Fecha_Fin, Lugar, Edificio, Profesor_Principal, RUN, Nombre_Docente, Apellido_Docente_1, Apellido_Docente_2, Jerarquizacion) VALUES (
                '{$planeacion[0]}',  -- Periodo
                '{$planeacion[1]}',  -- Sede
                '{$planeacion[2]}',  -- Facultad
                '{$planeacion[3]}',  -- Codigo_Depto
                '{$planeacion[4]}',  -- Departamento
                '{$planeacion[5]}',  -- Id_Asignatura
                '{$planeacion[6]}',  -- Asignatura
                '{$planeacion[7]}',  -- Seccion
                '{$planeacion[8]}',  -- Duracion
                '{$planeacion[9]}',  -- Jornada
                '{$planeacion[10]}',  -- Cupo
                '{$planeacion[11]}',  -- Inscritos
                '{$planeacion[12]}',  -- Dia
                '{$planeacion[13]}',  -- Hora_Inicio
                '{$planeacion[14]}',  -- Hora_Fin
                '{$fechaInicio}',  -- Fecha_Inicio
                '{$fechaFin}',  -- Fecha_Fin
                '{$planeacion[17]}',  -- Lugar
                '{$planeacion[18]}',  -- Edificio
                '{$planeacion[19]}',  -- Profesor_Principal
                '{$planeacion[20]}',  -- RUN
                '{$planeacion[21]}',  -- Nombre_Docente
                '{$planeacion[22]}',  -- Apellido_Docente_1
                '{$planeacion[23]}',  -- Apellido_Docente_2
                '{$planeacion[24]}'   -- Jerarquizacion
            )";
            $result = pg_query($this->conn, $query);
            if (!$result) {
                die("Error en la inserción de datos en la tabla temporal TempPlaneacion: " . pg_last_error());
            }
        }

        foreach ($datos['Docentes_planificados'] as $docente) {
            $query = "INSERT INTO TempDocentesPlanificados (RUN, Nombre, Apellido_P, Telefono, Email_personal, Email_institucional, Dedicacion, Contrato, Diurno, Vespertino, Sede, Carrera, Grado_academico, Jerarquia, Cargo, Estamento) VALUES (
                " . (is_numeric($docente[0]) ? $docente[0] : "NULL") . ",  -- RUN
                '{$docente[1]}',  -- Nombre
                '{$docente[2]}',  -- Apellido_P
                " . (is_numeric($docente[3]) ? $docente[3] : "NULL") . ",  -- Telefono
                '{$docente[4]}',  -- Email_personal
                '{$docente[5]}',  -- Email_institucional
                '{$docente[6]}',  -- Dedicacion
                '{$docente[7]}',  -- Contrato
                '{$docente[8]}',  -- Diurno
                '{$docente[9]}',  -- Vespertino
                '{$docente[10]}',  -- Sede
                '{$docente[11]}',  -- Carrera
                '{$docente[12]}',  -- Grado_academico
                '{$docente[13]}',  -- Jerarquia
                '{$docente[14]}',  -- Cargo
                '{$docente[15]}'   -- Estamento
            )";
            $result = pg_query($this->conn, $query);
            if (!$result) {
                die("Error en la inserción de datos en la tabla temporal TempDocentesPlanificados: " . pg_last_error());
            }
        }

        foreach ($datos['Estudiantes'] as $estudiante) {
            $fechalogro = DateTime::createFromFormat('Y-m', $estudiante[13]);
            $fechaultimacarga = !empty($estudiante[14]) ? DateTime::createFromFormat('Y-m', $estudiante[14]) : null;

            if ($fechalogro) {
                $fechalogro = $fechalogro->format('Y-m-01'); // Primer día del mes
            } else {
                die("Error en la conversión de fechas: Fecha_Logro '{$estudiante[13]}'");
            }

            if ($fechaultimacarga) {
                $fechaultimacarga = $fechaultimacarga->format('Y-m-01'); // Primer día del mes
            } else {
                $fechaultimacarga = 'NULL'; // Establecer como NULL si está vacío
            }

            // Escapar cadenas con pg_escape_string
            $segundoApellido = pg_escape_string($this->conn, $estudiante[11]);
            $primeraApellido = pg_escape_string($this->conn, $estudiante[10]);

            $query = "INSERT INTO TempEstudiantes (Codigo_Plan, Carrera, Cohorte, Numero_de_alumno, Bloqueo, Causal_Bloqueo, RUN, DV, Nombre_1, Nombre_2, Primer_Apellido, Segundo_Apellido, Logro, Fecha_Logro, Ultima_Carga) VALUES (
                '{$estudiante[0]}',  -- Codigo_Plan
                '{$estudiante[1]}',  -- Carrera
                '{$estudiante[2]}',  -- Cohorte
                {$estudiante[3]},    -- Numero_de_alumno
                '{$estudiante[4]}',  -- Bloqueo
                '{$estudiante[5]}',  -- Causal_Bloqueo
                {$estudiante[6]},    -- RUN
                '{$estudiante[7]}',  -- DV
                '{$estudiante[8]}',  -- Nombre_1
                '{$estudiante[9]}',  -- Nombre_2
                '{$primeraApellido}', -- Primer_Apellido
                '{$segundoApellido}', -- Segundo_Apellido
                '{$estudiante[12]}', -- Logro
                '{$fechalogro}',     -- Fecha_Logro
                " . ($fechaultimacarga !== 'NULL' ? "'$fechaultimacarga'" : 'NULL') . " -- Ultima_Carga
            )";
            $result = pg_query($this->conn, $query);
            if (!$result) {
                die("Error en la inserción de datos en la tabla temporal TempEstudiantes: " . pg_last_error());
            }
        }

        foreach ($datos['Notas'] as $nota) {
            // Escapar cadenas con pg_escape_string
            $Apellido_Materno = pg_escape_string($this->conn, $nota[8]);
            $Apellido_Paterno = pg_escape_string($this->conn, $nota[7]);
            $query = "INSERT INTO TempNotas (Codigo_Plan, Plan, Cohorte, Sede, RUN, DV, Nombres, Apellido_Paterno, Apellido_Materno, Numero_de_alumno, Periodo_Asignatura, Codigo_Asignatura, Asignatura, Convocatoria, Calificacion, Nota) VALUES (
                '{$nota[0]}',  -- Codigo_Plan
                '{$nota[1]}',  -- Plan
                '{$nota[2]}',  -- Cohorte
                '{$nota[3]}',  -- Sede
                {$nota[4]},    -- RUN
                '{$nota[5]}',  -- DV
                '{$nota[6]}',  -- Nombres
                '{$Apellido_Paterno}', -- Apellido_Paterno
                '{$Apellido_Materno}', -- Apellido_Materno
                '{$nota[9]}',  -- Numero_de_alumno
                '{$nota[10]}', -- Periodo_Asignatura
                '{$nota[11]}', -- Codigo_Asignatura
                '{$nota[12]}', -- Asignatura
                '{$nota[13]}', -- Convocatoria
                '{$nota[14]}', -- Calificacion
                " . (is_numeric($nota[15]) ? $nota[15] : "NULL") . " -- Nota
            )";
            $result = pg_query($this->conn, $query);
            if (!$result) {
                die("Error en la inserción de datos en la tabla temporal TempNotas: " . pg_last_error());
            }
        }

        foreach ($datos['Planes'] as $plan) {
            $iniciovigencia = DateTime::createFromFormat('d/m/y', $plan[8])->format('Y-m-d');
            $query = "INSERT INTO TempPlanes (Codigo_Plan, Facultad, Carrera, Plan, Jornada, Sede, Grado, Modalidad, Inicio_Vigencia) VALUES (
                '{$plan[0]}',  -- Codigo_Plan
                '{$plan[1]}',  -- Facultad
                '{$plan[2]}',  -- Carrera
                '{$plan[3]}',  -- Plan
                '{$plan[4]}',  -- Jornada
                '{$plan[5]}',  -- Sede
                '{$plan[6]}',  -- Grado
                '{$plan[7]}',  -- Modalidad
                '{$iniciovigencia}' -- Inicio_Vigencia
            )";
            $result = pg_query($this->conn, $query);
            if (!$result) {
                die("Error en la inserción de datos en la tabla temporal TempPlanes: " . pg_last_error());
            }
        }

        foreach ($datos['Prerequisitos'] as $prerequisito) {
            $query = "INSERT INTO TempPrerequisitos (Plan, Asignatura_id, Asignatura, Nivel, Prerequisitos, Prerequisitos_1) VALUES (
                '{$prerequisito[0]}',  -- Plan
                '{$prerequisito[1]}',  -- Asignatura_id
                '{$prerequisito[2]}',  -- Asignatura
                " . (is_numeric($prerequisito[3]) ? $prerequisito[3] : "NULL") . ",  -- Nivel
                '{$prerequisito[4]}',  -- Prerequisitos
                " . (is_numeric($prerequisito[5]) ? $prerequisito[5] : "NULL") . " -- Prerequisitos_1
            )";
            $result = pg_query($this->conn, $query);
            if (!$result) {
                die("Error en la inserción de datos en la tabla temporal TempPrerequisitos: " . pg_last_error());
            }
        }

        foreach ($datos['Planes_Magia'] as $planMagia) {
            $query = "INSERT INTO TempPlanesMagia (Planes_Vigentes) VALUES (
                '{$planMagia[0]}'  -- Planes_Vigentes
            )";
            $result = pg_query($this->conn, $query);
            if (!$result) {
                die("Error en la inserción de datos en la tabla temporal TempPlanesMagia: " . pg_last_error());
            }
        }

        foreach ($datos['Planes_Hechiceria'] as $planHechiceria) {
            $query = "INSERT INTO TempPlanesHechiceria (Planes_Vigentes) VALUES (
                '{$planHechiceria[0]}'  -- Planes_Vigentes
            )";
            $result = pg_query($this->conn, $query);
            if (!$result) {
                die("Error en la inserción de datos en la tabla temporal TempPlanesHechiceria: " . pg_last_error());
            }
        }

        foreach ($datos['Malla_Magia'] as $mallaMagia) {
            $query = "INSERT INTO TempMallaMagia (Col1, Col2, Col3, Col4, Col5, Col6, Col7, Col8, Col9, Col10) VALUES (
                '{$mallaMagia[0]}',  -- Col1
                '{$mallaMagia[1]}',  -- Col2
                '{$mallaMagia[2]}',  -- Col3
                '{$mallaMagia[3]}',  -- Col4
                '{$mallaMagia[4]}',  -- Col5
                '{$mallaMagia[5]}',  -- Col6
                '{$mallaMagia[6]}',  -- Col7
                '{$mallaMagia[7]}',  -- Col8
                '{$mallaMagia[8]}',  -- Col9
                '{$mallaMagia[9]}'   -- Col10
            )";
            $result = pg_query($this->conn, $query);
            if (!$result) {
                die("Error en la inserción de datos en la tabla temporal TempMallaMagia: " . pg_last_error());
            }
        }

        foreach ($datos['Malla_Hechiceria'] as $mallaHechiceria) {
            $query = "INSERT INTO TempMallaHechiceria (Col1, Col2, Col3, Col4, Col5, Col6, Col7, Col8, Col9, Col10) VALUES (
                '{$mallaHechiceria[0]}',  -- Col1
                '{$mallaHechiceria[1]}',  -- Col2
                '{$mallaHechiceria[2]}',  -- Col3
                '{$mallaHechiceria[3]}',  -- Col4
                '{$mallaHechiceria[4]}',  -- Col5
                '{$mallaHechiceria[5]}',  -- Col6
                '{$mallaHechiceria[6]}',  -- Col7
                '{$mallaHechiceria[7]}',  -- Col8
                '{$mallaHechiceria[8]}',  -- Col9
                '{$mallaHechiceria[9]}'   -- Col10
            )";
            $result = pg_query($this->conn, $query);
            if (!$result) {
                die("Error en la inserción de datos en la tabla temporal TempMallaHechiceria: " . pg_last_error());
            }
        }
    }

    private function InsertarDatosFinales($query, $nombre_tabla)
    {
        $result = pg_query($this->conn, $query);
        if (!$result) {
            die("Error en la inserción de datos en la tabla $nombre_tabla: " . pg_last_error());
        }
    }

    private function EliminarTablasTemporales()
    {
        $queries = [
            "DROP TABLE TempAsignaturas",
            "DROP TABLE TempPlaneacion",
            "DROP TABLE TempEstudiantes",
            "DROP TABLE TempNotas",
            "DROP TABLE TempDocentesPlanificados",
            "DROP TABLE TempPlanes",
            "DROP TABLE TempPrerequisitos",
            "DROP TABLE TempPlanesMagia",
            "DROP TABLE TempPlanesHechiceria",
            "DROP TABLE TempMallaMagia",
            "DROP TABLE TempMallaHechiceria"
        ];
        foreach ($queries as $query) {
            $result = pg_query($this->conn, $query);
            if (!$result) {
                die("Error en la eliminación de la tabla temporal: " . pg_last_error());
            }
        }
    }

    private function CrearTriggers()
    {
        // Eliminar los triggers si existen
        $queries = [
            "DROP FUNCTION IF EXISTS before_insert_cursos_func()",
            "DROP TRIGGER IF EXISTS before_insert_cursos ON Cursos",
            "DROP FUNCTION IF EXISTS actualizar_nota()",
            "DROP TRIGGER IF EXISTS trigger_actualizar_nota ON Nota",
            "DROP TRIGGER IF EXISTS trigger_actualizar_nota_avance ON Avance_Academico"
        ];
        foreach ($queries as $query) {
            $result = pg_query($this->conn, $query);
            if (!$result) {
                die("Error en la eliminación de triggers: " . pg_last_error());
            }
        }

        // Crear la función para actualizar la tabla Cursos
        $funcion_actualizar_cursos = "CREATE OR REPLACE FUNCTION before_insert_cursos_func()
        RETURNS TRIGGER AS $$
        BEGIN
            IF NEW.RUN_Academico IS NULL THEN
                NEW.RUN_Academico = NEW.Código_Departamento;
            END IF;
            RETURN NEW;
        END;
        $$ LANGUAGE plpgsql;";

        $result = pg_query($this->conn, $funcion_actualizar_cursos);
        if (!$result) {
            die("Error en la creación de la funcion_actualizar_cursos: " . pg_last_error());
        }

        // Crear el trigger para la tabla Cursos
        $trigger_cursos = "CREATE TRIGGER before_insert_cursos
        BEFORE INSERT ON Cursos
        FOR EACH ROW
        EXECUTE FUNCTION before_insert_cursos_func();";

        $result = pg_query($this->conn, $trigger_cursos);
        if (!$result) {
            die("Error en la creación del trigger(cursos): " . pg_last_error());
        }

        // Crear la función para actualizar la tabla Nota
        $funcion_actualizar_nota = "CREATE OR REPLACE FUNCTION actualizar_nota()
        RETURNS TRIGGER AS $$
        BEGIN
            IF NEW.Calificación IS NULL THEN
                NEW.Resultado := 'Curso Vigente en el período académico';
                NEW.Descripción := 'curso vigente';
            ELSIF NEW.Calificación = 'P' THEN
                NEW.Resultado := 'Nota Pendiente';
                NEW.Descripción := 'Curso incompleto';
            ELSIF NEW.Calificación = 'NP' THEN
                NEW.Resultado := 'No se Presenta';
                NEW.Descripción := 'Reprobatorio';
            ELSIF NEW.Calificación = 'EX' THEN
                NEW.Resultado := 'Eximido';
                NEW.Descripción := 'Aprobatorio';
            ELSIF NEW.Calificación = 'A' THEN
                NEW.Resultado := 'Aprobado';
                NEW.Descripción := 'Aprobatorio';
            ELSIF NEW.Calificación = 'R' THEN
                NEW.Resultado := 'Reprobado';
                NEW.Descripción := 'Reprobatorio';
            ELSIF NEW.Calificación = 'SO' THEN
                NEW.Resultado := 'Sobresaliente';
                NEW.Descripción := 'Aprobatorio';
            ELSIF NEW.Calificación = 'MB' THEN
                NEW.Resultado := 'Muy Bueno';
                NEW.Descripción := 'Aprobatorio';
            ELSIF NEW.Calificación = 'B' THEN
                NEW.Resultado := 'Bueno';
                NEW.Descripción := 'Aprobatorio';
            ELSIF NEW.Calificación = 'SU' THEN
                NEW.Resultado := 'Suficiente';
                NEW.Descripción := 'Aprobatorio';
            ELSIF NEW.Calificación = 'I' THEN
                NEW.Resultado := 'Insuficiente';
                NEW.Descripción := 'Reprobatorio';
            ELSIF NEW.Calificación = 'M' THEN
                NEW.Resultado := 'Malo';
                NEW.Descripción := 'Reprobatorio';
            END IF;
            RETURN NEW;
        END;
        $$ LANGUAGE plpgsql;";

        $result = pg_query($this->conn, $funcion_actualizar_nota);
        if (!$result) {
            die("Error en la creación de la funcion_actualizar_nota: " . pg_last_error());
        }

        // Crear el trigger para la tabla Nota
        $trigger_actualizar_nota = "CREATE TRIGGER trigger_actualizar_nota
        BEFORE INSERT OR UPDATE ON Nota
        FOR EACH ROW
        EXECUTE FUNCTION actualizar_nota();";

        $result = pg_query($this->conn, $trigger_actualizar_nota);
        if (!$result) {
            die("Error en la creación del trigger(nota): " . pg_last_error());
        }

        // Crear el trigger para la tabla Avance_Academico
        $trigger_actualizar_nota_avance = "CREATE TRIGGER trigger_actualizar_nota_avance
        BEFORE INSERT OR UPDATE ON Avance_Academico
        FOR EACH ROW
        EXECUTE FUNCTION actualizar_nota();";

        $result = pg_query($this->conn, $trigger_actualizar_nota_avance);
        if (!$result) {
            die("Error en la creación del trigger (avance): " . pg_last_error());
        }
    }

    private function LeerArchivo($archivo)
    {
        /* LeerArchivo recibe un archivo .csv
        y realiza la lectura para retornalo como array */

        $file = fopen($archivo, 'r');
        $array = [];
        $primeralinea = true;
        while (($linea = fgetcsv($file)) !== FALSE) {
            if ($primeralinea) {
                $primeralinea = false;
                continue; // Ignorar la primera línea
            }
            $array[] = $linea;
        }
        fclose($file);
        return $array;
    }
}
