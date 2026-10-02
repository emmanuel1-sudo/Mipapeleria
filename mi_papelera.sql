CREATE DATABASE IF NOT EXISTS mi_papeleria;

USE mi_papeleria;



CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(60) NOT NULL,
    descripcion TEXT
);

INSERT INTO categorias (nombre, descripcion) VALUES
('Cuadernos', 'Cuadernos escolares y universitarios'),
('Lapiceros', 'Lapiceros de diferentes colores'),
('Carpetas', 'Carpetas para documentos'),
('Colores', 'Colores y materiales para dibujo'),
('Papelería', 'Otros productos de papelería');




CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL,
    categoria_id INT NOT NULL,
    marca VARCHAR(50),
    
    FOREIGN KEY (categoria_id)
    REFERENCES categorias(id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
);

INSERT INTO productos
(nombre, precio, stock, categoria_id, marca)
VALUES
('Cuaderno 100 hojas', 8500, 20, 1, 'Norma'),
('Lapicero azul', 1800, 50, 2, 'Kilométrico'),
('Carpeta plástica', 3500, 30, 3, 'Norma'),
('Caja de colores', 12000, 15, 4, 'Prismacolor'),
('Resma de papel', 18000, 10, 5, 'Reprograf');



CREATE TABLE ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL,

    FOREIGN KEY (producto_id)
    REFERENCES productos(id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
);

INSERT INTO ventas
(producto_id, cantidad, total)
VALUES
(1, 2, 17000),
(2, 5, 9000),
(3, 1, 3500),
(4, 2, 24000),
(5, 1, 18000);


-- ==========================================
-- TABLA PROVEEDORES
-- ==========================================

CREATE TABLE proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL,
    telefono VARCHAR(30) NOT NULL,
    correo VARCHAR(100) NOT NULL
);

INSERT INTO proveedores
(nombre, telefono, correo)
VALUES
('Distribuciones Norma', '3001111111', 'norma@gmail.com'),
('Papeles Colombia', '3002222222', 'papeles@gmail.com'),
('Útiles Escolares SAS', '3003333333', 'utiles@gmail.com'),
('Distribuciones ABC', '3004444444', 'abc@gmail.com'),
('Papelería Central', '3005555555', 'central@gmail.com');