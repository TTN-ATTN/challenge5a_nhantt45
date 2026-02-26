CREATE DATABASE app_db;

USE app_db;

CREATE TABLE
    users (
        id INT auto_increment PRIMARY KEY,
        username VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone_number VARCHAR(20) NOT NULL,
        role VARCHAR(20) NOT NULL CHECK (role IN ('teacher', 'student')) DEFAULT 'student',
        updated_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )
CREATE TABLE
    avatars (
        id INT auto_increment PRIMARY KEY,
        user_id INT NOT NULL,
        image_url VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE cascade
    )
CREATE TABLE
    notifications (
        id INT auto_increment PRIMARY KEY,
        sender_id INT NOT NULL,
        receiver_id INT NOT NULL,
        message text NOT NULL,
        updated_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (sender_id) REFERENCES users (id) ON DELETE cascade,
        FOREIGN KEY (receiver_id) REFERENCES users (id) ON DELETE cascade
    )
CREATE TABLE
    assignments (
        id INT auto_increment PRIMARY KEY,
        teacher_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        url VARCHAR(255) NOT NULL, -- can be a URL or a file uploaded to the server
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        deadline TIMESTAMP NOT NULL,
        FOREIGN KEY (teacher_id) REFERENCES users (id) ON DELETE cascade
    )
    -- TODO: only teacher can see the list of the submissions
CREATE TABLE
    submissions (
        id INT auto_increment PRIMARY KEY,
        assignment_id INT NOT NULL,
        student_id INT NOT NULL,
        url VARCHAR(255) NOT NULL, -- can be a URL or a file uploaded to the server
        grade FLOAT (2, 2),
        submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (assignment_id) REFERENCES assignments (id) ON DELETE cascade,
        FOREIGN KEY (student_id) REFERENCES users (id) ON DELETE cascade
    )
CREATE TABLE
    challenges (
        id INT auto_increment PRIMARY KEY,
        hint text NOT NULL,
        url VARCHAR(255) NOT NULL, -- path to txt file
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )