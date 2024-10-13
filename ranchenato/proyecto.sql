/*crear base de datos*/
create database proyectobar;

drop database proyectobar;

/*ver base de datos existente*/
show databases;

/*usa la bd*/
use proyectobar;

/*crear tabla de rol*/
create table rol (
CodRol int primary key auto_increment,
NomRol varchar(20) not null
);

/*crear tabla de usuario*/
create table usuario (
CodUsu int primary key auto_increment,
NomUsu varchar(20) not null,
ApeUsu varchar(20) not null,
TelUsu char(12) not null,
CorUsu varchar(50) not null,
ConUsu varchar(30) not null,
EstUsu char(10) not null,
CodRol int, foreign key (CodRol) references rol (CodRol)	
);



/*crear tabla de empleado*/
create table empleado (
CodEmp int primary key auto_increment,
DocEmp int unique not null,
NomEmp varchar(20) not null,
ApeEmp varchar(20) not null,
DirEmp varchar(50) not null,
TelEmp char(12) not null,
ConEmp varchar(30) not null,
CodRol int, foreign key (CodRol) references rol (CodRol)
);

/*crear tabla de producto*/
create table Producto (
CodPro int primary key auto_increment,
CatPro varchar(20) not null,
NomPro varchar(20) not null,
DesPro varchar(100) not null,
PrePro float not null,
StoPro smallint not null,
FotPro varchar(600) not null
); 

/*crear tabla de pedido*/
create table Pedido (
CodPed int primary key auto_increment,
FecPed date not null,
TotPed float not null,
EstPed char(15) not null,
CodUsu int, foreign key (CodUsu) references usuario (CodUsu)
);

/*crear tabla de detalle pedido*/
create table detallePedido (
CodDpe int primary key auto_increment,
CodPro int, foreign key (CodPro) references Producto (CodPro),
CanPro int not null,
PrePro float not null,
SutPed float not null,
CodPed int, foreign key (CodPed) references Pedido (CodPed)
);

drop table detallePedido;

/*crear tabla de domicilio*/
create table domicilio (
CodDom int primary key auto_increment,
EstDom char(15) not null,
DesDom varchar(50) not null,
DirDom varchar(50) not null,
CodPed int, foreign key (CodPed) references Pedido (CodPed),
CodUsu int, foreign key (CodUsu) references usuario (CodUsu)
);

drop table domicilio;

create table contacto(
CodCon int primary key auto_increment,
NomCon varchar(50),
ApeCon varchar(50),
DocCon varchar(10),
TelCon varchar(10),
CorCon varchar(50),
asuCon varchar(100),
DesCon text
);

CREATE TABLE Carrito (
Id INT AUTO_INCREMENT PRIMARY KEY,
CodPro INT NOT NULL,
CodUsu INT NOT NULL,
Cantidad INT NOT NULL,
FOREIGN KEY (CodPro) REFERENCES Producto(CodPro),
FOREIGN KEY (CodUsu) REFERENCES Usuario(CodUsu)
);


insert into Rol (CodRol, NomRol) values (1, 'Cliente');
insert into Rol (CodRol, NomRol) values (2, 'Administrador');
insert into Rol (CodRol, NomRol) values (3, 'Domiciliario');


-- Procedimiento para insertar un nuevo rol
DELIMITER $$
CREATE PROCEDURE InsertarRol(
    IN p_CodRol INT,
    IN p_NomRol VARCHAR(20)
)
BEGIN
    INSERT INTO rol(CodRol, NomRol) VALUES (p_CodRol, p_NomRol);
END$$
DELIMITER ;

-- Procedimiento para insertar un nuevo usuario
DELIMITER $$
CREATE PROCEDURE InsertarUsuario(
    IN p_CodUsu INT,
    IN p_NomUsu VARCHAR(20),
    IN p_ApeUsu VARCHAR(20),
    IN p_TelUsu CHAR(12),
    IN p_CorUsu VARCHAR(50),
    IN p_ConUsu VARCHAR(30),
    IN p_EstUsu CHAR(10),
    IN p_CodRol INT
)
BEGIN
    INSERT INTO usuario(CodUsu, NomUsu, ApeUsu, TelUsu, CorUsu, ConUsu, EstUsu, CodRol)
    VALUES (p_CodUsu, p_NomUsu, p_ApeUsu, p_TelUsu, p_CorUsu, p_ConUsu, p_EstUsu, p_CodRol);
END$$
DELIMITER ;

-- Procedimiento para insertar un nuevo empleado
DELIMITER $$
CREATE PROCEDURE InsertarEmpleado(
    IN p_CodEmp INT,
    IN p_DocEmp INT,
    IN p_NomEmp VARCHAR(20),
    IN p_ApeEmp VARCHAR(20),
    IN p_DirEmp VARCHAR(50),
    IN p_TelEmp CHAR(12),
    IN p_ConEmp VARCHAR(30),
    IN p_CodRol INT
)
BEGIN
    INSERT INTO empleado(CodEmp, DocEmp, NomEmp, ApeEmp, DirEmp, TelEmp, ConEmp, CodRol)
    VALUES (p_CodEmp, p_DocEmp, p_NomEmp, p_ApeEmp, p_DirEmp, p_TelEmp, p_ConEmp, p_CodRol);
END$$
DELIMITER ;

-- Procedimiento para insertar un nuevo producto
DELIMITER $$
CREATE PROCEDURE InsertarProducto(
    IN p_CodPro INT,
    IN p_CatPro VARCHAR(20),
    IN p_NomPro VARCHAR(20),
    IN p_DesPro VARCHAR(100),
    IN p_PrePro FLOAT,
    IN p_StoPro SMALLINT
)
BEGIN
    INSERT INTO Producto(CodPro, CatPro, NomPro, DesPro, PrePro, StoPro)
    VALUES (p_CodPro, p_CatPro, p_NomPro, p_DesPro, p_PrePro, p_StoPro);
END$$
DELIMITER ;

-- Procedimiento para insertar un nuevo pedido
DELIMITER $$
CREATE PROCEDURE InsertarPedido(
    IN p_CodPed INT,
    IN p_FecPed DATE,
    IN p_TotPed FLOAT,
    IN p_EstPed CHAR(15),
    IN p_CodUsu INT
)
BEGIN
    INSERT INTO Pedido(CodPed, FecPed, TotPed, EstPed, CodUsu)
    VALUES (p_CodPed, p_FecPed, p_TotPed, p_EstPed, p_CodUsu);
END$$
DELIMITER ;

-- Procedimiento para insertar un nuevo detalle de pedido
DELIMITER $$
CREATE PROCEDURE InsertarDetallePedido(
    IN p_CodDpe INT,
    IN p_CodPro INT,
    IN p_CanPro INT,
    IN p_PrePro FLOAT,
    IN p_SutPed FLOAT,
    IN p_CodPed INT
)
BEGIN
    INSERT INTO detallePedido(CodDpe, CodPro, CanPro, PrePro, SutPed, CodPed)
    VALUES (p_CodDpe, p_CodPro, p_CanPro, p_PrePro, p_SutPed, p_CodPed);
END$$
DELIMITER ;

-- Procedimiento para insertar un nuevo domicilio
DELIMITER $$
CREATE PROCEDURE InsertarDomicilio(
    IN p_CodDom INT,
    IN p_EstDom CHAR(15),
    IN p_DesDom VARCHAR(50),
    IN p_DirDom VARCHAR(50),
    IN p_CodPed INT,
    IN p_CodEmp INT
)
BEGIN
    INSERT INTO domicilio(CodDom, EstDom, DesDom, DirDom, CodPed, CodEmp)
    VALUES (p_CodDom, p_EstDom, p_DesDom, p_DirDom, p_CodPed, p_CodEmp);
END$$
DELIMITER ;

-- Llamada al procedimiento para insertar un nuevo rol
CALL InsertarRol(2, 'Administrador');

-- Llamada al procedimiento para insertar un nuevo usuario
CALL InsertarUsuario(4, 'Juan', 'Perez', '1234567890', 'juan@example.com', '123', 'Activo', 1);

-- Llamada al procedimiento para insertar un nuevo empleado
CALL InsertarEmpleado(1, 1234567890, 'Carlos', 'Gonzalez', 'Calle 123', '0987654321', 'password456', 2);

-- Llamada al procedimiento para insertar un nuevo producto
CALL InsertarProducto(1, 'Electrónica', 'Smartphone', 'Teléfono inteligente', 499.99, 100);

-- Llamada al procedimiento para insertar un nuevo pedido
CALL InsertarPedido(1, '2024-04-28', 699.99, 'En Proceso', 1);

-- Llamada al procedimiento para insertar un nuevo detalle de pedido
CALL InsertarDetallePedido(1, 1, 2, 499.99, 999.98, 1);

-- Llamada al procedimiento para insertar un nuevo domicilio
CALL InsertarDomicilio(1, 'Entregado', 'Casa', 'Calle 123', 1, 1);
