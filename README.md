# Laravel Library Management API

A RESTful API for managing a library system built with Laravel 12. This API allows you to manage authors, books, and borrowing records.

## Features

- **Author Management**: Create, read, update, and delete authors
- **Book Management**: Create, read, update, and delete books (with soft deletes)
- **Borrowing System**: Borrow books, return books, and view borrowing history
- **Pagination**: All list endpoints support pagination
- **Validation**: Comprehensive request validation
- **Testing**: Feature tests for all modules

## Requirements

- PHP >= 8.2
- Composer
- SQLite (default) or MySQL/PostgreSQL

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/laravel-library-api-ahmedashraf.git
cd laravel-library-api-ahmedashraf
```

2. Install dependencies:
```bash
composer install
```

3. Copy the environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Configure your database in `.env`:
```env
DB_CONNECTION=sqlite
# Or for MySQL/PostgreSQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=library
# DB_USERNAME=root
# DB_PASSWORD=
```

6. Create SQLite database (if using SQLite):
```bash
touch database/database.sqlite
```

7. Run migrations:
```bash
php artisan migrate
```

8. (Optional) Seed the database:
```bash
php artisan db:seed
```

## Running the Application

Start the development server:
```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

## API Documentation

All API endpoints are prefixed with `/api`.

### Author Endpoints

#### Create Author
```http
POST /api/authors
Content-Type: application/json

{
    "name": "J.K. Rowling",
    "bio": "British author, best known for the Harry Potter series.",
    "dob": "1965-07-31"
}
```

**Response:**
```json
{
    "data": {
        "id": 1,
        "name": "J.K. Rowling",
        "bio": "British author, best known for the Harry Potter series.",
        "dob": "1965-07-31",
        "created_at": "2024-01-01 12:00:00",
        "updated_at": "2024-01-01 12:00:00"
    }
}
```

#### List Authors (Paginated)
```http
GET /api/authors?page=1
```

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "J.K. Rowling",
            "bio": "British author...",
            "dob": "1965-07-31",
            "created_at": "2024-01-01 12:00:00",
            "updated_at": "2024-01-01 12:00:00"
        }
    ],
    "links": {...},
    "meta": {...}
}
```

#### Get Single Author
```http
GET /api/authors/{id}
```

#### Update Author
```http
PUT /api/authors/{id}
Content-Type: application/json

{
    "name": "Updated Name",
    "bio": "Updated biography",
    "dob": "1990-01-01"
}
```

#### Delete Author
```http
DELETE /api/authors/{id}
```

**Note:** Authors with existing books cannot be deleted.

**Response:**
```json
{
    "message": "Author deleted successfully"
}
```

### Book Endpoints

#### Create Book
```http
POST /api/books
Content-Type: application/json

{
    "title": "Harry Potter and the Philosopher's Stone",
    "description": "The first book in the Harry Potter series.",
    "year": 1997,
    "author_id": 1
}
```

**Response:**
```json
{
    "data": {
        "id": 1,
        "title": "Harry Potter and the Philosopher's Stone",
        "description": "The first book in the Harry Potter series.",
        "year": 1997,
        "author_id": 1,
        "author": {
            "id": 1,
            "name": "J.K. Rowling",
            "bio": "...",
            "dob": "1965-07-31",
            "created_at": "...",
            "updated_at": "..."
        },
        "created_at": "2024-01-01 12:00:00",
        "updated_at": "2024-01-01 12:00:00"
    }
}
```

#### List Books (Paginated)
```http
GET /api/books?page=1
```

#### Get Single Book
```http
GET /api/books/{id}
```

#### Update Book
```http
PUT /api/books/{id}
Content-Type: application/json

{
    "title": "Updated Title",
    "description": "Updated description",
    "year": 2020,
    "author_id": 1
}
```

#### Delete Book
```http
DELETE /api/books/{id}
```

**Note:** Books are soft deleted.

### Borrowing Endpoints

#### Borrow a Book
```http
POST /api/borrow
Content-Type: application/json

{
    "user_name": "John Doe",
    "book_id": 1
}
```

**Response:**
```json
{
    "data": {
        "id": 1,
        "user_name": "John Doe",
        "book_id": 1,
        "book": {
            "id": 1,
            "title": "...",
            ...
        },
        "borrow_at": "2024-01-01 12:00:00",
        "return_at": null,
        "created_at": "2024-01-01 12:00:00",
        "updated_at": "2024-01-01 12:00:00"
    }
}
```

**Error Response (if book is already borrowed):**
```json
{
    "message": "This book is currently borrowed and cannot be borrowed again until returned."
}
```

#### Return a Book
```http
POST /api/return/{borrow_id}
```

**Response:**
```json
{
    "data": {
        "id": 1,
        "user_name": "John Doe",
        "book_id": 1,
        "borrow_at": "2024-01-01 12:00:00",
        "return_at": "2024-01-15 12:00:00",
        ...
    }
}
```

#### Get Borrow History (Paginated)
```http
GET /api/borrow/history?page=1
```

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "user_name": "John Doe",
            "book_id": 1,
            "book": {
                "id": 1,
                "title": "...",
                "author": {...}
            },
            "borrow_at": "2024-01-01 12:00:00",
            "return_at": "2024-01-15 12:00:00",
            "created_at": "...",
            "updated_at": "..."
        }
    ],
    "links": {...},
    "meta": {...}
}
```

## Validation Rules

### Author
- `name`: required, string, max 255 characters
- `bio`: optional, string
- `dob`: required, date, must be before today

### Book
- `title`: required, string, max 255 characters
- `description`: required, string
- `year`: required, integer, between 1000 and current year
- `author_id`: required, integer, must exist in authors table

### Borrow
- `user_name`: required, string, max 255 characters
- `book_id`: required, integer, must exist in books table

## Testing

Run the test suite:
```bash
php artisan test
```

Or using PHPUnit directly:
```bash
./vendor/bin/phpunit
```

### Test Coverage

The test suite includes:

- **Author Tests**: Create, list, get, update, delete, validation, and deletion prevention for authors with books
- **Book Tests**: Create with valid/invalid author, list, get, update, delete, and validation
- **Borrow Tests**: Borrow book, prevent borrowing borrowed book, return book, borrow after return, return validation, and history

## Database Schema

### authors
- `id` (bigint, primary key)
- `name` (string)
- `bio` (text, nullable)
- `dob` (date)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### books
- `id` (bigint, primary key)
- `title` (string)
- `description` (text)
- `year` (year)
- `author_id` (bigint, foreign key)
- `created_at` (timestamp)
- `updated_at` (timestamp)
- `deleted_at` (timestamp, nullable) - soft delete

### borrow_records
- `id` (bigint, primary key)
- `user_name` (string)
- `book_id` (bigint, foreign key)
- `borrow_at` (datetime)
- `return_at` (datetime, nullable)
- `created_at` (timestamp)
- `updated_at` (timestamp)

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthorController.php
│   │   ├── BookController.php
│   │   └── BorrowController.php
│   ├── Requests/
│   │   ├── StoreAuthorRequest.php
│   │   ├── UpdateAuthorRequest.php
│   │   ├── StoreBookRequest.php
│   │   ├── UpdateBookRequest.php
│   │   └── BorrowBookRequest.php
│   └── Resources/
│       ├── AuthorResource.php
│       ├── BookResource.php
│       └── BorrowRecordResource.php
├── Models/
│   ├── Author.php
│   ├── Book.php
│   └── BorrowRecord.php
└── Policies/
    └── AuthorPolicy.php

database/
├── migrations/
│   ├── create_authors_table.php
│   ├── create_books_table.php
│   └── create_borrow_records_table.php
└── factories/
    ├── AuthorFactory.php
    └── BookFactory.php

tests/
└── Feature/
    ├── AuthorTest.php
    ├── BookTest.php
    └── BorrowTest.php
```

## Technologies Used

- Laravel 12
- PHP 8.2+
- Eloquent ORM
- PHPUnit for testing

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Author

Ahmed Ashraf
