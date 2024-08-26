CREATE TABLE actor (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100)
);

CREATE TABLE pelicula (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100)
);

CREATE TABLE actor_pelicula (
    id_actor INT,
    id_pelicula INT,
    FOREIGN KEY (id_actor) REFERENCES actor(id),
    FOREIGN KEY (id_pelicula) REFERENCES pelicula(id)
);

-- Insertar datos en las tablas
INSERT INTO actor (nombre) VALUES ('Leonardo DiCaprio'), ('Matthew McConaughey'), ('Daniel Radcliffe'), ('Jessica Chastain');

INSERT INTO pelicula (nombre) VALUES ('Interstellar'), ('The Revenant'), ('Harry Potter'), ('The Theory of Everything'), ('Inception');

INSERT INTO actor_pelicula (id_actor, id_pelicula) VALUES (1, 2), (2, 1), (4, 1), (3, 3), (1, 5);

-- Consultas
SELECT nombre FROM actores;
SELECT nombre, calificacion FROM peliculas;
SELECT nombre, calificacion FROM peliculas WHERE calificacion < 8.5;
SELECT nombre FROM peliculas WHERE director = 'C. Nolan';
SELECT id_actor FROM actuo_en WHERE id_pelicula = 1;
SELECT actores.id as id, actores.nombre AS actor, peliculas.nombre AS pelicula
FROM actores
JOIN actuo_en ON actores.id = actuo_en.id_actor
JOIN peliculas ON actuo_en.id_pelicula = peliculas.id;