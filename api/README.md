# Database Query
```sql
CREATE DATABASE IF NOT EXISTS online_library;

USE online_library;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  email VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS borrowed_books (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  book_id VARCHAR(255) NOT NULL,
  book_title VARCHAR(255) NOT NULL,
  book_author VARCHAR(255),
  book_cover VARCHAR(500),
  book_preview VARCHAR(500),
  borrow_date DATE NOT NULL,
  expire_date DATE NOT NULL,
  returned TINYINT(1) DEFAULT 0,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX (user_id),
  INDEX (book_id),
  INDEX (returned)
);

```
