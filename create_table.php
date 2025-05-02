<?php
include 'includes/db.php'; // Make sure to include the database connection

// SQL to create the users table (for storing users)
$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,         -- Auto increment user ID
    username VARCHAR(255) NOT NULL UNIQUE,      -- Username should be unique
    password VARCHAR(255) NOT NULL              -- Password will store hashed password
)";

// SQL to create the posts table (for storing posts)
$sql_posts = "CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,         -- Auto increment post ID
    title VARCHAR(255) NOT NULL,                -- Post title
    content TEXT NOT NULL,                      -- Post content (could be large)
    author VARCHAR(255) NOT NULL,               -- Author name (reference to users)
    image VARCHAR(255),                         -- Image URL or path
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Created at timestamp
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP -- Updated at timestamp
)";

// Execute the query to create the `users` table
if ($conn->query($sql_users) === TRUE) {
    echo "Table 'users' created successfully.<br>";
} else {
    echo "Error creating 'users' table: " . $conn->error . "<br>";
}

// Execute the query to create the `posts` table
if ($conn->query($sql_posts) === TRUE) {
    echo "Table 'posts' created successfully.<br>";
} else {
    echo "Error creating 'posts' table: " . $conn->error . "<br>";
}

$conn->close();
?>
