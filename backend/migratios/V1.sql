CREATE TABLE contacts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(100),
  country_code VARCHAR(10),
  phone VARCHAR(20),
  service VARCHAR(100),
  message TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users_orizzonttebi (
  id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


INSERT INTO users_orizzonttebi (name, email, password)
VALUES ('Cristian', 'cristiano@email.com', '202508');
