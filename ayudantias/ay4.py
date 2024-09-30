import sqlite3

# Conectar a la base de datos (se creará si no existe)
conn = sqlite3.connect('ay4.db')
cursor = conn.cursor()

# Eliminar tablas si existen
cursor.execute("DROP TABLE IF EXISTS Buques")
cursor.execute("DROP TABLE IF EXISTS Navieras")
cursor.execute("DROP TABLE IF EXISTS Personal")
cursor.execute("DROP TABLE IF EXISTS Navegaciones")
cursor.execute("DROP TABLE IF EXISTS TrabajaEn")

# Crear tabla Buques
cursor.execute('''
CREATE TABLE Buques(
    buqueId INTEGER PRIMARY KEY,
    nombre VARCHAR(50),
    capacidad INTEGER,
    capitanId INTEGER
)
''')

# Crear tabla Navieras
cursor.execute('''
CREATE TABLE Navieras(
    NavieraId INTEGER PRIMARY KEY,
    nombre VARCHAR(50),
    pais VARCHAR(50)
)
''')

# Crear tabla Personal
cursor.execute('''
CREATE TABLE Personal(
    personalId INTEGER PRIMARY KEY,
    nombre VARCHAR(50),
    edad INTEGER,
    nacionalidad VARCHAR(50)
)
''')

# Crear tabla Navegaciones
cursor.execute('''
CREATE TABLE Navegaciones(
    ruta VARCHAR(50),
    buqueId INTEGER,
    navieraId INTEGER,
    FOREIGN KEY(buqueId) REFERENCES Buques(buqueId),
    FOREIGN KEY(navieraId) REFERENCES Navieras(navieraId),
    PRIMARY KEY(buqueId, navieraId)
)
''')

# Crear tabla TrabajaEn
cursor.execute('''
CREATE TABLE TrabajaEn(
    personalId INTEGER,
    buqueId INTEGER,
    PRIMARY KEY(personalId, buqueId),
    FOREIGN KEY(personalId) REFERENCES Personal(personalId),
    FOREIGN KEY(buqueId) REFERENCES Buques(buqueId)
)
''')

# Insertar datos en la tabla Buques
cursor.execute('''
INSERT INTO Buques (buqueId, nombre, capacidad, capitanId) VALUES
(1, 'El Inundado', 4000, 22),
(2, 'Gotas Saladas', 2010, 1),
(3, 'Falucho', 4085, 21),
(4, 'Lento', 2989, 33)
''')

# Insertar datos en la tabla Navieras
cursor.execute('''
INSERT INTO Navieras (navieraId, nombre, pais) VALUES
(1, 'Lagos del Sur', 'Chile'),
(2, 'Somarco', 'Chile'),
(3, 'Ultramar', 'Argentina'),
(4, 'Broom', 'Chile')
''')

# Insertar datos en la tabla Navegaciones
cursor.execute('''
INSERT INTO Navegaciones (buqueId, navieraId, ruta) VALUES
(1, 1, 'Chiloe Norte'),
(2, 1, 'Cruce Lago General Carrera'),
(1, 2, 'Puerto Murta Chile Chico'),
(3, 3, 'Chaiten Hornopiren')
''')

# Insertar datos en la tabla Personal
cursor.execute('''
INSERT INTO Personal (personalId, nombre, edad, nacionalidad) VALUES
(1, 'Ian McIntosh', 32, 'Escocia'),
(2, 'Christian Rutter', 24, 'Alemania'),
(3, 'Mark Boyle', 33, 'EE.UU.'),
(4, 'Victoria Kostylev', 48, 'Chile'),
(5, 'Ian Stolberg', 44, 'Suecia')
''')

# Insertar datos en la tabla TrabajaEn
cursor.execute('''
INSERT INTO TrabajaEn (personalId, buqueId) VALUES
(1, 2),
(1, 4),
(3, 2),
(4, 4),
(5, 1)
''')

# Realizar consultas

# 1. Encuentre los nombres de todo el personal con edad menor a 25, o que son de nacionalidad Chilena.
cursor.execute('''
SELECT nombre
FROM Personal
WHERE Personal.edad < 25 OR Personal.nacionalidad = 'Chilena' 
''')

# Mostrar resultados
print("Personal con edad menor a 25 o de nacionalidad Chilena")
for row in cursor.fetchall():
    print(row)

# 2.Encuentre los id’s de todo el personal que trabaja en un buque de la naviera “Lagos del Sur”.
cursor.execute(''' 
SELECT TrabajaEn.personalId
FROM TrabajaEn
JOIN Navegaciones ON TrabajaEn.buqueId = Navegaciones.buqueId
JOIN Navieras ON Navegaciones.navieraId = Navieras.navieraId
WHERE Navieras.nombre = 'Lagos del Sur'     
''')

# Mostrar resultados
print("Personal que trabaja en un buque de la naviera Lagos del Sur")
for row in cursor.fetchall():
    print(row)

"""3.
Para validar si sus datos son consistentes, asegurese de que que cada capitan 
de un bote tambien este registrado en la tabla Personal. Para esto, 
escriba la consulta que encuentre todos los capitanes que
no aparecen en la tabla de personal
"""
cursor.execute(''' 
SELECT Buques.capitanId
FROM Buques 
WHERE Buques.capitanId NOT IN (SELECT personalId FROM Personal)             
''')

# Mostrar resultados
print("Capitanes que no estan en la tabla Personal")
for row in cursor.fetchall():
    print(row)

# 4. Encuente los nombres de los buques operando para m´as de una nav
cursor.execute(''' 
SELECT Buques.nombre
FROM Buques
JOIN Navegaciones ON Buques.buqueId = Navegaciones.buqueId
JOIN Navieras ON Navegaciones.navieraId = Navieras.navieraId
GROUP BY Buques.buqueId
HAVING COUNT(Navegaciones.navieraId) > 1             
''')

# Mostrar resultados
print("Buques operando para más de una naviera")
for row in cursor.fetchall():
    print(row)

# 5. Encuentre el nombre del buque con mayor capacidad
cursor.execute(''' 
SELECT Buques.nombre
FROM Buques
WHERE Capacidad = (SELECT MAX(Capacidad) FROM Buques)
''')

# Mostar resultados
print("Buque con mayor capacidad")
for row in cursor.fetchall():
    print(row)

# 6. Encuentre los nombres de todo el personal que trabaja para dos o m´as buqeues distintos.
cursor.execute(''' 
SELECT Personal.nombre
FROM Personal
JOIN TrabajaEn ON Personal.personalId = TrabajaEn.personalId
GROUP BY Personal.personalId
HAVING COUNT(TrabajaEn.buqueId) >= 2             
''')

# Mostrar resultados
print("Personal que trabaja para dos o más buques distintos")
for row in cursor.fetchall():
    print(row)


# Confirmar los cambios y cerrar la conexión
conn.commit()
conn.close()
