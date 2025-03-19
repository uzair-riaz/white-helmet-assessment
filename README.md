<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## API-Only Laravel Application

This is an API-only Laravel application with no frontend components. It's designed to serve as a backend API service that can be consumed by any frontend application, mobile app, or other services.

## Docker Setup

This project is dockerized for easy local development. Docker and Docker Compose are required to run the application using the provided configuration.

### Prerequisites

- Docker
- Docker Compose

### Getting Started

1. Clone the repository
2. Copy the example environment file:
   ```
   copy .env.example .env
   ```
3. Build and start the Docker containers:
   ```
   docker-compose up -d
   ```
4. Generate an application key:
   ```
   docker-compose exec app php artisan key:generate
   ```
5. Run database migrations:
   ```
   docker-compose exec app php artisan migrate
   ```

### Docker Utility Script

A PowerShell utility script is included for common Docker operations:

```
# Run in PowerShell
.\docker.ps1 up           # Start the Docker containers
.\docker.ps1 down         # Stop the Docker containers
.\docker.ps1 migrate      # Run database migrations
.\docker.ps1 fresh        # Drop all tables and re-run migrations
.\docker.ps1 seed         # Seed the database
.\docker.ps1 artisan      # Run Artisan commands
.\docker.ps1 composer     # Run Composer commands
.\docker.ps1 bash         # Access the container's bash shell
.\docker.ps1 logs         # View container logs
```

### Accessing the API

The API is accessible at `http://localhost:8000/api`

## Features

- RESTful API endpoints
- JSON responses
- API authentication using Laravel Sanctum
- API routing system
- Database-driven functionality

## API Documentation

API endpoints can be found in the `routes/api.php` file.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Task Management API

A Laravel-based API for managing tasks, with features for user authentication, task assignment, commenting, and notifications.

## Features

- User authentication and authorization
- Create, read, update, and delete tasks
- Assign tasks to other users
- Add comments to tasks
- Email notifications for new comments
- Advanced queue management for asynchronous processing
- Caching for improved performance
- Comprehensive test suite

## Requirements

- PHP 8.1+
- Composer
- MySQL or compatible database
- Redis (for queues and caching)
- SMTP server for sending emails

## Installation

1. Clone the repository:
   ```
   git clone <repository-url>
   cd task-management-api
   ```

2. Install dependencies:
   ```
   composer install
   ```

3. Copy environment file and set up your configuration:
   ```
   cp .env.example .env
   ```

4. Update the `.env` file with your database, Redis, and mail settings:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=task_management
   DB_USERNAME=root
   DB_PASSWORD=

   CACHE_DRIVER=redis
   QUEUE_CONNECTION=redis
   REDIS_HOST=127.0.0.1
   REDIS_PASSWORD=null
   REDIS_PORT=6379

   MAIL_MAILER=smtp
   MAIL_HOST=smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=your_username
   MAIL_PASSWORD=your_password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=noreply@taskmanagement.com
   MAIL_FROM_NAME="${APP_NAME}"
   ```

5. Generate an application key:
   ```
   php artisan key:generate
   ```

6. Run database migrations:
   ```
   php artisan migrate
   ```

7. Start the development server:
   ```
   php artisan serve
   ```

8. Run the queue worker for background processing:
   ```
   php artisan queue:work
   ```

## Docker Setup

Alternatively, you can use Docker for easier setup:

1. Build and start the Docker containers:
   ```
   docker-compose up -d
   ```

2. Generate an application key:
   ```
   docker-compose exec app php artisan key:generate
   ```

3. Run database migrations:
   ```
   docker-compose exec app php artisan migrate
   ```

## API Endpoints

### Authentication
- `POST /api/register` - Register a new user
- `POST /api/login` - Login and get access token
- `POST /api/logout` - Logout (requires authentication)
- `GET /api/profile` - Get authenticated user profile

### Tasks
- `GET /api/tasks` - List all tasks (owned and assigned)
- `POST /api/tasks` - Create a new task
- `GET /api/tasks/{id}` - Get a specific task
- `PUT /api/tasks/{id}` - Update a task
- `DELETE /api/tasks/{id}` - Delete a task
- `GET /api/users` - Get list of users for assignment

### Comments
- `GET /api/tasks/{taskId}/comments` - List all comments for a task
- `POST /api/tasks/{taskId}/comments` - Add a comment to a task
- `PUT /api/tasks/{taskId}/comments/{id}` - Update a comment
- `DELETE /api/tasks/{taskId}/comments/{id}` - Delete a comment

## Testing

Run the tests using PHPUnit:
```
php artisan test
```

## Design Patterns

The application makes use of several design patterns and Laravel best practices:

1. **Repository Pattern** - Abstracting data access
2. **Service Layer** - Business logic separated from controllers
3. **Queue Jobs** - Handling background processes
4. **Notifications** - Sending email notifications
5. **Caching** - Improving performance with efficient caching

## Cache Strategy

The application implements strategic caching to improve performance:

- Task listings are cached for 60 seconds
- Individual task details with comments are cached
- Comments for a task are cached
- Cache invalidation occurs when tasks or comments are modified

## License

This project is licensed under the MIT License.
