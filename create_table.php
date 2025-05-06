<?php
include 'includes/db.php';

$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,         -- Auto increment user ID
    username VARCHAR(255) NOT NULL UNIQUE,      -- Username should be unique
    password VARCHAR(255) NOT NULL              -- Password will store hashed password
)";


$sql_posts = "CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,         
    title VARCHAR(255) NOT NULL,                
    content TEXT NOT NULL,                      
    author VARCHAR(255) NOT NULL,              
    image VARCHAR(255),                         
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP 
)";


if ($conn->query($sql_users) === TRUE) {
    echo "Table 'users' created successfully.<br>";
} else {
    echo "Error creating 'users' table: " . $conn->error . "<br>";
}

if ($conn->query($sql_posts) === TRUE) {
    echo "Table 'posts' created successfully.<br>";
} else {
    echo "Error creating 'posts' table: " . $conn->error . "<br>";
}

$conn->close();
?>
