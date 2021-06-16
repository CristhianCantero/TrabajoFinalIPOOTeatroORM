-- CREACION DE TABLAS DE LA BASE DE DATOS

create table if not exists teatro(
    idTeatro bigint(11) NOT NULL AUTO_INCREMENT,
    nombre varchar(50) NOT NULL, 
    direccion varchar(100) NOT NULL,
    ciudad varchar(50) NOT NULL,
    PRIMARY KEY(idTeatro)
);

create table if not exists actividad(
    idActividad bigint(11) NOT NULL AUTO_INCREMENT,
    idTeatro bigint(11) NOT NULL,
    nombre varchar(100) NOT NULL,
    horaInicio varchar(6) NOT NULL,
    fecha date NOT NULL,
    duracionActividad int(4) NOT NULL,
    precio float NOT NULL,
    PRIMARY KEY(idActividad),
    FOREIGN KEY(idTeatro) REFERENCES teatro(idTeatro)
    ON UPDATE CASCADE ON DELETE CASCADE
);

create table if not exists cine(
    idActividad bigint(11) NOT NULL,
    genero varchar(50) NOT NULL,
    paisOrigen varchar(50) NOT NULL,
    PRIMARY KEY(idActividad), 
    FOREIGN KEY(idActividad) REFERENCES actividad(idActividad)
    ON UPDATE CASCADE ON DELETE CASCADE
);

create table if not exists musical(
    idActividad bigint(11) NOT NULL,
    director varchar(50) NOT NULL,
    cantidadPersonasEscena int(5) NOT NULL,
    PRIMARY KEY(idActividad), 
    FOREIGN KEY(idActividad) REFERENCES actividad(idActividad)
    ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE table if not exists obrateatro(
    idActividad bigint(11) NOT NULL,
    PRIMARY KEY(idActividad),
    FOREIGN KEY(idActividad) REFERENCES actividad(idActividad)
    ON UPDATE CASCADE ON DELETE CASCADE
);

-- POBLACION DE LA BASE DE DATOS INICIAL

-- INSERT INTO `teatro` (`idTeatro`, `nombre`, `direccion`, `ciudad`) VALUES
-- (1, 'Luna Park', 'Av. Eduardo Madero 470', 'Buenos Aires Capital'),
-- (2, 'Teatro Colon', 'Cerrito 628', 'Ciudad Autonoma de Buenos Aires');

-- INSERT INTO `actividad` (`idActividad`, `idTeatro`, `nombre`, `horaInicio`, `fecha`, `duracionActividad`, `precio`) VALUES
-- (1, 1, 'Spider-Man: Homecoming', '15:00', '2021-06-14', 110, 500),
-- (2, 1, 'La Bella y La Bestia', '16:51', '2021-06-14', 40, 350),
-- (3, 1, 'Hamlet', '17:00', '2021-06-16', 50, 500),
-- (4, 1, 'Hamlet 2 - La Venganza', '17:00', '2021-06-17', 56, 450);

-- INSERT INTO `cine` (`idCine`, `idActividad`, `genero`, `paisOrigen`) VALUES
-- (1, 1, 'Accion - Ciencia Ficcion', 'Estados Unidos');

-- INSERT INTO `musical` (`idMusical`, `idActividad`, `director`, `cantidadPersonasEscena`) VALUES
-- (1, 2, 'Cristhian', 20);

-- INSERT INTO `obrateatro` (`idObraTeatro`, `idActividad`) VALUES
-- (1, 3),
-- (2, 4);
