CREATE TABLE user_profiles (
    user_id INT PRIMARY KEY,
    phone VARCHAR(20) DEFAULT NULL,
    birthdate DATE DEFAULT NULL,
    street VARCHAR(255) DEFAULT NULL,
    street2 VARCHAR(255) DEFAULT NULL,
    city VARCHAR(100) DEFAULT NULL,
    postal_code VARCHAR(10) DEFAULT NULL,
    country VARCHAR(100) DEFAULT 'Singapore',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
