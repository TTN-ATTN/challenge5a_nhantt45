CREATE DATABASE app_db;

USE app_db;

CREATE TABLE
    users (
        id INT auto_increment PRIMARY KEY,
        username VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        phone_number VARCHAR(20) NOT NULL UNIQUE,
        avatar VARCHAR(255), -- path to avatar image
        role VARCHAR(20) NOT NULL CHECK (role IN ('teacher', 'student')) DEFAULT 'student',
        session_token VARCHAR(255) DEFAULT NULL,
        updated_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

CREATE TABLE
    notifications (
        id INT auto_increment PRIMARY KEY,
        sender_id INT NOT NULL,
        receiver_id INT NOT NULL,
        message text NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (sender_id) REFERENCES users (id) ON DELETE cascade,
        FOREIGN KEY (receiver_id) REFERENCES users (id) ON DELETE cascade
    );

CREATE TABLE
    assignments (
        id INT auto_increment PRIMARY KEY,
        teacher_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        file_path VARCHAR(255) NOT NULL,
        deadline DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (teacher_id) REFERENCES users (id) ON DELETE cascade
    );

CREATE TABLE
    submissions (
        id INT auto_increment PRIMARY KEY,
        assignment_id INT NOT NULL,
        student_id INT NOT NULL,
        file_path VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        score FLOAT (4, 2),
        FOREIGN KEY (assignment_id) REFERENCES assignments (id) ON DELETE cascade,
        FOREIGN KEY (student_id) REFERENCES users (id) ON DELETE cascade
    );

CREATE TABLE
    challenges (
        id INT auto_increment PRIMARY KEY,
        teacher_id INT NOT NULL,
        hint text NOT NULL,
        chall_url VARCHAR(255) NOT NULL, -- path to txt file
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (teacher_id) REFERENCES users (id) ON DELETE cascade
    );

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);