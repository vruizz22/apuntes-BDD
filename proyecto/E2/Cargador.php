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
            "Personas", "Estudiantes", 
            "Academicos", "Administativos", 
            "Cursos", "Cursos_Equivalencias", 
            "Cursos_Prerequisitos", "Cursos_Minimos", 
            "Planes_Estudio", "Carreras", 
            "Programacion_Academica", "Facultad", 
            "Departamento", "Nota", "Avance_Academico"
        );
    }

    
    public function CrearTablas()
    {
        // Eliminar las tablas si existen
        foreach ($this->tablas as $tabla) {
            $result = pg_query($this->conn, "DROP TABLE IF EXISTS {$tabla}");
            if (!$result) {
                die("Error en la eliminación de la tabla: " .
                    pg_last_error());
            }
        }

        // Creamos las tablas
        $Personas = "CREATE TABLE Personas(
            RUN int NOT NULL,
            DV int NOT NULL,
            Nombres varchar(100) NOT NULL,
            Apellidos varchar(100) NOT NULL,
            Correos varchar(100) NOT NULL,
            Telefonos varchar(100) NOT NULL,
            PRIMARY KEY (RUN, DV)
        )";

        $Estudiantes = "CREATE TABLE Estudiantes(
            RUN int NOT NULL,
            DV int NOT NULL,
            Causal_de_bloqueo varchar(100),
            Bloqueo boolean NOT NULL,
            Numero_de_estudiante int NOT NULL,
            Cohorte varchar(100) NOT NULL,
            FOREIGN KEY (RUN, DV) REFERENCES Personas(RUN, DV),
            PRIMARY KEY (RUN, DV, Numero_de_estudiante)
        )";

        $Academicos = "CREATE TABLE Academicos(
            RUN int NOT NULL,
            DV int NOT NULL,
            Estamento varchar(100),
            Grado_academico varchar(100),
            Contrato varchar(100),
            Jerarquia varchar(100),
            Jornada varchar(100),
            FOREIGN KEY (RUN, DV) REFERENCES Personas(RUN, DV),
            PRIMARY KEY (RUN, DV)
        )";

        $Administativos = "CREATE TABLE Administrativos(
            RUN int NOT NULL,
            DV int NOT NULL,
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
            Nombre varchar(100) NOT NULL,
            Nivel int NOT NULL,
            Ciclo varchar(100),
            Tipo varchar(100),
            Oportunidad varchar(100),
            Duración varchar(1),
            PRIMARY KEY (Sigla, Seccion)
        )";

        $Cursos_Equivalencias = "CREATE TABLE Cursos_Equivalencias(
            Sigla_curso varchar(100) NOT NULL,
            Sigla_equivalente varchar(100) NOT NULL,
            Ciclo varchar(100),
            PRIMARY KEY (Sigla_curso, Sigla_equivalente),
            FOREIGN KEY (Sigla_curso) REFERENCES Cursos(Sigla_curso)
        )";

        $Cursos_Prerequisitos = "CREATE TABLE Cursos_Prerequisitos(
            Sigla_curso varchar(100) NOT NULL,
            Sigla_prerequisito varchar(100) NOT NULL,
            Ciclo varchar(100),
            PRIMARY KEY (Sigla_curso, Sigla_prerequisito),
            FOREIGN KEY (Sigla_curso) REFERENCES Cursos(Sigla_curso)
        )";

        $Cursos_Minimos = "CREATE TABLE Cursos_Minimos(
            Sigla_curso varchar(100) NOT NULL,
            Sigla_minimo varchar(100) NOT NULL,
            Ciclo varchar(100),
            Nombre varchar(100),
            Tipo varchar(100),
            Nivel int NOT NULL,
            PRIMARY KEY (Sigla_curso, Sigla_minimo),
            FOREIGN KEY (Sigla_curso) REFERENCES Cursos(Sigla_curso)
        )";

        $Planes_Estudio = "CREATE TABLE Planes_Estudio(
            Código_Plan varchar(100) PRIMARY KEY UNIQUE NOT NULL,
            Inicio_Vigencia DATE NOT NULL,
            Jornada varchar(100) NOT NULL,
            Modalidad varchar(100) NOT NULL,
            Sede varchar(100) NOT NULL,
            Plan varchar(100) NOT NULL
            Facultad varchar(100) NOT NULL
        )";

        $Carreras = "CREATE TABLE Carreras(
            Nombre varchar(100) NOT NULL,
            Código_Plan varchar(100) UNIQUE NOT NULL,
            RUT int NOT NULL,
            DV int NOT NULL,
            Numero_de_estudiante int NOT NULL,
            PRIMARY KEY (Nombre, Código_Plan, RUT, DV, Numero_de_estudiante),
            FOREIGN KEY (Código_Plan) REFERENCES Planes_Estudio(Código_Plan),
            FOREIGN KEY (RUT, DV, Numero_de_estudiante) REFERENCES Estudiantes(RUT, DV, Numero_de_estudiante)
        )";

        $Programacion_Academica = "CREATE TABLE Programacion_Academica(
            Sigla_curso varchar(100) NOT NULL,
            Seccion_curso int NOT NULL,
            Código_Plan varchar(100) NOT NULL,
            RUN int NOT NULL,
            DV int NOT NULL,
            Numero_de_estudiante int NOT NULL,
            Periodo_Oferta varchar(100) NOT NULL,
            Cupos int NOT NULL,
            Sala varchar(100) NOT NULL,
            Hora_Inicio TIME NOT NULL,
            Hora_Fin TIME NOT NULL,
            Fecha_Inicio DATE NOT NULL,
            Fecha_Fin DATE NOT NULL,
            Modulo_Horario varchar(100) NOT NULL,
            Inscritos int NOT NULL,
            PRIMARY KEY (Sigla_curso, Seccion_curso, Código_Plan, RUN, DV, Numero_de_estudiante, Periodo_Oferta),
            FOREIGN KEY (Sigla_curso, Seccion_curso) REFERENCES Cursos(Sigla_curso, Seccion_curso),
            FOREIGN KEY (Código_Plan) REFERENCES Planes_Estudio(Código_Plan),
            FOREIGN KEY (RUN, DV, Numero_de_estudiante) REFERENCES Estudiantes(RUN, Numero_de_estudiante)
        )";

        $Facultad = "CREATE TABLE Facultad(
            id SERIAL PRIMARY KEY,
            Nombre varchar(100) NOT NULL
        )";

        $Departamento = "CREATE TABLE Departamento(
            Nombre varchar(100) NOT NULL,
            Código varchar(100) NOT NULL,
            id_facultad int NOT NULL,
            FOREIGN KEY (id_facultad) REFERENCES Facultad(id),
            PRIMARY KEY (Nombre, Código, id_facultad)
        )";

        $Nota = "CREATE TABLE Nota(
            Sigla_curso varchar(100) NOT NULL,
            Seccion_curso int NOT NULL,
            Numero_de_estudiante int NOT NULL,
            RUN int NOT NULL,
            DV int NOT NULL,
            Nota float,
            Descripción varchar(100) ,
            Resultado varchar(100) DEFAULT 'Curso Vigente en el período académico',
            Calificación varchar(100),
            PRIMARY KEY (Sigla_curso, Seccion_curso, Código_Plan, RUN, DV, Numero_de_estudiante),
            FOREIGN KEY (Sigla_curso, Seccion_curso) REFERENCES Cursos(Sigla_curso, Seccion_curso),
            FOREIGN KEY (RUN, DV, Numero_de_estudiante) REFERENCES Estudiantes(RUN, DV, Numero_de_estudiante)
        )";

        $Avance_Academico = "CREATE TABLE Avance_Academico(
            Sigla_curso varchar(100) NOT NULL,
            RUN int NOT NULL,
            DV int NOT NULL,
            Numero_de_estudiante int NOT NULL,
            Periodo_Oferta varchar(100) NOT NULL,
            Nota float,
            Descripción varchar(100) NOT NULL,
            Ultima_Carga varchar(100),
            Ultimo_Logro varchar(100) NOT NULL,
            PRIMARY KEY (Sigla_curso, RUN, DV, Numero_de_estudiante, Ultimo_Logro),
            FOREIGN KEY (Sigla_curso) REFERENCES Cursos(Sigla_curso),
            FOREIGN KEY (RUN, DV, Numero_de_estudiante) REFERENCES Estudiantes(RUN, DV, Numero_de_estudiante)
        )";

        // Guardamos todas las tablas en un array
        $tablas = array(
            $Personas, $Estudiantes, 
            $Academicos, $Administativos, 
            $Cursos, $Cursos_Equivalencias, 
            $Cursos_Prerequisitos, $Cursos_Minimos, 
            $Planes_Estudio, $Carreras, 
            $Programacion_Academica, $Facultad, 
            $Departamento, $Nota, $Avance_Academico
        );
        
        // Ejecutamos la query para todas las tablas
        foreach ($tablas as $tabla) {
            $result = pg_query($this->conn, $tabla);
            if (!$result) {
                die("Error en la creación de la tabla: " .
                    pg_last_error());
            }
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
            END IF;
            RETURN NEW;
        END;
        $$ LANGUAGE plpgsql;";

        $result = pg_query($this->conn, $funcion_actualizar_nota);
        if (!$result) {
            die("Error en la creación de la función: " . pg_last_error());
        }
        
        // Crear el trigger para la tabla Nota
        $trigger_actualizar_nota = "CREATE TRIGGER trigger_actualizar_nota
        BEFORE INSERT OR UPDATE ON Nota
        FOR EACH ROW
        EXECUTE FUNCTION actualizar_nota();";

        $result = pg_query($this->conn, $trigger_actualizar_nota);
        if (!$result) {
            die("Error en la creación del trigger: " . pg_last_error());
        }
    }

    public function CargarDatos()
    {
        // Obtenemos los datos de las tablas

        /* Se tienen los csv: Asignaturas, Docentes_planificados
        Estudiantes, Notas, Planeacion, Planes, Prerequisitos, Planes_Magia, Planes_Hechiceria
        Malla_Hechiceria, Malla_Magia. 

        Columnas Asignaturas: Plan,Asignatura id,Asignatura,Nivel,Prerequisito(ciclo)
        Columnas Docentes_planificados: RUN,Nombre,Apellido P,telefono,email personal,email  institucional,DEDICACIÓN,CONTRATO,DIURNO,VESPERTINO,SEDE,CARRERA,GRADO ACADÉMICO,JERARQUÍA ,CARGO,ESTAMENTO
        Columnas Estudiantes: Código Plan,Carrera,Cohorte,Número de alumno,Bloqueo,Causal Bloqueo,RUN,DV,Nombres,Unnamed: 9,Primer Apellido,Segundo Apellido,Logro,Fecha Logro,Última Carga
        Columas Notas: Código Plan,Plan,Cohorte,Sede,RUN,DV,Nombres,Apellido Paterno,Apellido Materno,Número de alumno,Periodo Asignatura,Código Asignatura,Asignatura,Convocatoria,Calificación,Nota
        Columnas Planeación: Periodo,Sede,Facultad  ,Código Depto,Departamento,Id Asignatura,Asignatura,Sección,Duración,Jornada,Cupo,Inscritos,Día,Hora Inicio,Hora Fin,Fecha Inicio,Fecha Fin,Lugar,Edificio,Profesor Principal,RUN,Nombre Docente,1er Apellido Docente,2so Apellido Docente,Jerarquización
        Columnas Planes: Código Plan,Facultad,Carrera,Plan,Jornada,Sede,Grado,Modalidad,Inicio Vigencia
        Columnas Prerequisitos: Plan,Asignatura id,Asignatura,Nivel,Prerequisitos,Prerequisitos.1
        Columnas Planes Magia: Planes Vigentes
        Columnas Planes Hechiceria: Planes Vigentes
        Columnas Malla Magia: 1,2,3,4,5,6,7,8,9,10 
        Columnas Malla Hechiceria: 1,2,3,4,5,6,7,8,9,10 

        Obtenemos los datos de los csv estrategicamente 
        para insertar estos datos a las tablas anteriormente creadas */

        $nombre_archivos = array(
            'Asignaturas', 'Docentes_planificados',
            'Estudiantes', 'Notas', 'Planeacion',
            'Planes', 'Prerequisitos', 'Planes_Magia',
            'Planes_Hechiceria', 'Malla_Hechiceria', 'Malla_Magia'
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

        // Insertamos datos

        /* Guardamos los resultados de inserccion de cada
        tabla en un array y verificamos para todas si la
        insercción fue correcta */

        // Cerrar la conexión
        pg_close($this->conn);
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

