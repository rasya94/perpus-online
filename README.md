# BookHaven - Online Library System

A simple PHP and MySQL online library website where users can search for books using the OpenLibrary API and borrow them.

## Features

- **Book Search**: Search books by title, author, or ISBN using OpenLibrary API
- **User Authentication**: Username/password registration and login system
- **Book Borrowing**: Users can borrow up to 5 books at a time
- **Borrow Period**: 2-week borrowing period with due date tracking
- **My Books**: View all borrowed books with due dates and overdue alerts
- **User Profile**: View account information and borrowing statistics
- **GoodReads-inspired Design**: Clean white background with black text

## Requirements

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- PHP extensions: PDO, PDO_MySQL

## Installation

1. **Upload files** to your web server (e.g., `/var/www/html/library` or `htdocs/library`)

2. **Create MySQL database** and import the schema:
   ```bash
   mysql -u root -p < database.sql
   ```

3. **Configure database connection** in `config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'online_library');
   ```

4. **Set proper permissions**:
   ```bash
   chmod 755 /path/to/library
   chmod 644 /path/to/library/*.php
   ```

5. **Access the application** in your browser:
   ```
   http://localhost/library/
   ```

## Default Test Account

- **Username**: demo
- **Password**: demo123

## File Structure

```
library/
├── config.php           # Database and application configuration
├── database.sql         # MySQL database schema
├── functions.php        # Helper functions and business logic
├── style.css           # GoodReads-inspired styling
├── index.php           # Homepage with search functionality
├── book_detail.php     # Individual book page with borrow button
├── borrow.php          # Handle book borrowing
├── my_books.php        # User's borrowed books list
├── return_book.php     # Handle book returns
├── profile.php         # User profile page
├── login.php           # User login
├── register.php        # User registration
├── logout.php          # Logout handler
└── README.md           # This file
```

## Configuration

You can modify these settings in `config.php`:

- `BORROW_LIMIT`: Maximum number of books a user can borrow (default: 5)
- `BORROW_DAYS`: Number of days for the borrow period (default: 14)
- `SITE_NAME`: Website name displayed in the UI (default: BookHaven)

## API Integration

This application uses the **OpenLibrary API** to search for books:
- Search API: `https://openlibrary.org/search.json`
- Book Details API: `https://openlibrary.org/works/{id}.json`
- Cover Images: `https://covers.openlibrary.org/b/id/{cover_id}-{size}.jpg`

No API key required.

## Security Notes

- Passwords are hashed using PHP's `password_hash()` with bcrypt
- SQL injection protection using PDO prepared statements
- XSS protection using `htmlspecialchars()` for output
- Session-based authentication

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Troubleshooting

### Database connection error
- Check `config.php` credentials
- Verify MySQL service is running
- Ensure database exists and user has proper permissions

### Book search not working
- Check internet connection
- Verify PHP `allow_url_fopen` is enabled
- OpenLibrary API may be temporarily unavailable

### Images not loading
- Ensure `allow_url_fopen` is enabled in php.ini
- Check firewall settings

## License

Free to use for personal and educational purposes.

## Support

For issues or questions, please refer to the documentation or contact your system administrator.