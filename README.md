# Quiz-App-24/25 Adrián Ucha DWES

## 1. **Configuración Inicial**
Vamos a crear un **docker-compose** para configurar el servidor, con una imagen de los servicios necesarios para poder 
hacer el servidor, con la app lanzada. Para ello configuraremos el siguiente docker-compose.yml

````dockerfile
version: '3.8'

services:
  web:
    image: php:8.1-apache
    container_name: php_app
    volumes:
      - ./app:/var/www/html  
    ports:
      - "8080:80"
    depends_on:
      - db

  db:
    image: mysql:8
    container_name: mysql_db
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: quiz_app
      MYSQL_USER: user
      MYSQL_PASSWORD: password
    ports:
      - "3306:3306"
    volumes:
      - db_data:/var/lib/mysql

  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    container_name: phpmyadmin
    restart: always
    ports:
      - "8081:80"
    environment:
      PMA_HOST: db
      MYSQL_ROOT_PASSWORD: root

# Guadar datos de la base datos
volumes:
  db_data:
````

Como se puede apreciar en la configuración del docker-compose, hemos configurado una carpeta en local, que es donde 
vamos a lanzar el contenido de las quizz, así directamente tener el contenido accesible, e ir modificándolo, para que 
lo utilice directamente el contenedor.

Una vez lanzado y listo podemos pasar al siguiente paso.

- Crea una base de datos con las siguientes tablas básicas:
    - **Usuarios:** `user_id`, `username`, `password` (encriptada).
    - **Cuestionarios:** `quiz_id`, `title`, `description`.
    - **Preguntas:** `question_id`, `quiz_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`.

Ahora conectándonos al phpMyAdmin, desde el puerto asígnado al docker, creamos las tablas en la base de datos:

### Configuración de tablas

Las tablas a crear son las siguientes:
````sql
-- Tabla de Usuarios
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, -- Encriptada con bcrypt
    role ENUM('student', 'instructor') NOT NULL
);

````
Con su agregado del role para las siguientes funcionalidades que sean necesarias.

````sql
-- Tabla de Cuestionarios
CREATE TABLE quizzes (
    quiz_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    created_by INT NOT NULL,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE CASCADE
);
````

````sql
-- Tabla de Preguntas
CREATE TABLE questions (
    question_id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,
    question_text TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_option CHAR(1) NOT NULL CHECK (correct_option IN ('A', 'B', 'C', 'D')),
    FOREIGN KEY (quiz_id) REFERENCES quizzes(quiz_id) ON DELETE CASCADE
);
````

Agregaremos a la base datos un par de cuestionaros de ejemplo que nos paso el profesor para realizar pruebas sobre ellos
inicialmente:

````sql
INSERT INTO quizzes (title, description, created_by) VALUES
('Matemáticas Básicas', 'Ejercicios sobre sumas, restas y multiplicaciones.', 1),
('Ciencia General', 'Preguntas sobre conceptos científicos básicos.', 1);

INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES
(1, '¿Cuánto es 5 + 3?', '6', '7', '8', '9', 'C'),
(1, '¿Cuál es el resultado de 6 × 7?', '40', '42', '45', '48', 'B');

INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES
(2, '¿Cuál es el gas más abundante en la atmósfera terrestre?', 'Oxígeno', 'Nitrógeno', 'Dióxido de carbono', 'Hidrógeno', 'B'),
(2, '¿Qué planeta es conocido como el planeta rojo?', 'Venus', 'Marte', 'Júpiter', 'Saturno', 'B');
````

## 2. Gestión de autentificación de estudiantes y profesores.

