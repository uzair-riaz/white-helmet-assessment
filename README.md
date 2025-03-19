# 📋 Task Management API

A modern RESTful API for task management built with Laravel. This application allows users to create, manage tasks, and collaborate through comments.

## ✨ Features

- 🔐 **User Authentication**: Register, login, and profile management
- ✅ **Task Management**: Create, read, update, and delete tasks
- 🔄 **Task Status Tracking**: Track tasks as pending, in-progress, or completed
- 📅 **Due Date Management**: Set and track due dates for tasks
- 👥 **Task Assignment**: Assign tasks to other users
- 💬 **Comment System**: Add comments to tasks for collaboration
- 🚀 **API Architecture**: Clean RESTful API with proper status codes and responses

## 🚀 Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/uzair-riaz/white-helmet-assessment
   cd white-helmet-assessment
   ```

2. **Environment Setup**
   ```bash
   cp .env.example .env
   ```

3. **Start Docker Containers**
   ```bash
   docker-compose up -d
   ```

4. **Access the API**
   The API will be accessible at `http://localhost:8000/api`

## 🔌 API Endpoints

### Authentication
- `POST /api/register` - Register a new user
- `POST /api/login` - Log in a user
- `POST /api/logout` - Log out a user (requires authentication)
- `GET /api/profile` - Get user profile (requires authentication)

### Tasks
- `GET /api/tasks` - List all tasks
- `POST /api/tasks` - Create a new task
- `GET /api/tasks/{id}` - Get task details
- `PUT /api/tasks/{id}` - Update a task
- `DELETE /api/tasks/{id}` - Delete a task
- `GET /api/users` - Get users for task assignment

### Comments
- `GET /api/tasks/{taskId}/comments` - List comments for a task
- `POST /api/tasks/{taskId}/comments` - Add a comment to a task
- `PUT /api/tasks/{taskId}/comments/{id}` - Update a comment
- `DELETE /api/tasks/{taskId}/comments/{id}` - Delete a comment

## 🔒 Security

The API uses Laravel Sanctum for token-based authentication. All protected routes require a valid authentication token.