CREATE TABLE todo (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    content TEXT NOT NULL,
    createdAt DATETIME,
    updatedAt DATETIME
);