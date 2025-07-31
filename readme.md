# Task Management API

A simple RESTful API built with Laravel 5.2 for managing tasks. This API provides full CRUD operations for tasks with token-based authentication.

## 📋 Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [API Documentation](#api-documentation)
- [Authentication](#authentication)
- [Testing with Postman](#testing-with-postman)
- [Database Schema](#database-schema)
- [Error Handling](#error-handling)
- [Contributing](#contributing)

## ✨ Features

- ✅ Create, Read, Update, Delete (CRUD) operations for tasks
- 🔐 Token-based authentication
- 📊 SQLite database for easy setup
- 🚀 RESTful API design
- ✅ Input validation
- 🛡️ Error handling and meaningful error messages
- 📱 JSON responses
- 🧪 Postman-ready endpoints
- 🎨 **Beautiful web interface** with modern design
- 📊 **Real-time statistics** dashboard
- 🔄 **Live task management** without page refresh
- 📱 **Responsive design** for mobile and desktop

## 🔧 Requirements

- PHP >= 5.5.9
- Laravel 5.2
- SQLite (included with PHP)
- Composer

## 🛠️ Technology Stack

### **Backend**
- **Laravel 5.2** - PHP framework
- **SQLite** - Lightweight database
- **RESTful API** - JSON responses
- **Token-based authentication**

### **Frontend**
- **Blade Templates** - Laravel's templating engine
- **Bootstrap 5** - CSS framework
- **Font Awesome** - Icon library
- **Vanilla JavaScript** - No additional frameworks needed
- **Fetch API** - For API communication
- **CSS Grid & Flexbox** - Modern layout
- **Custom CSS** - Gradient designs and animations

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd task-management-api
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Set up environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**
   The project is pre-configured to use SQLite. The database file is located at:
   ```
   database/database.sqlite
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Start the development server**
   ```bash
   php artisan serve
   ```

   The API will be available at: `http://localhost:8000`

## 🎨 Web Interface

The project includes a beautiful, modern web interface for managing tasks:

### **Access the Web Interface**
- Open your browser and go to: `http://localhost:8000`
- No additional setup required!

### **Web Interface Features**
- 📊 **Dashboard with statistics** - View total, completed, and pending tasks
- ✨ **Create tasks** - Easy-to-use form with validation
- 📝 **Edit tasks** - Click edit button to modify any task
- ✅ **Toggle status** - Mark tasks as complete or pending with one click
- 🗑️ **Delete tasks** - Remove tasks you no longer need
- 🔍 **Filter tasks** - View all, completed, or pending tasks
- 📱 **Responsive design** - Works perfectly on mobile and desktop
- 🎨 **Modern UI** - Beautiful gradient design with smooth animations

### **How to Use the Web Interface**
1. **Create a task**: Fill out the form at the top and click "Create Task"
2. **View tasks**: All your tasks appear below with status indicators
3. **Edit a task**: Click the "Edit" button on any task card
4. **Change status**: Use "Mark Complete" or "Mark Pending" buttons
5. **Delete a task**: Click the "Delete" button (with confirmation)
6. **Filter tasks**: Use the dropdown to show specific task types

## ⚙️ Configuration

### Environment Variables

The key configuration variables in `.env`:

```env
APP_ENV=local
APP_DEBUG=true
APP_KEY=your-app-key
APP_URL=http://localhost

DB_CONNECTION=sqlite
DB_DATABASE=C:\path\to\your\project\database\database.sqlite
```

### Authentication Token

The API uses a simple token-based authentication. The default token is:
```
testtoken123
```

> **Note**: In production, implement a more secure authentication system.

## 📚 API Documentation

### Base URL
```
http://localhost:8000/api
```

### Authentication
All protected endpoints require the following header:
```
Authorization: Bearer testtoken123
```

### Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/test` | Test API connection | ❌ |
| GET | `/tasks` | Get all tasks | ✅ |
| POST | `/tasks` | Create a new task | ✅ |
| GET | `/tasks/{id}` | Get task by ID | ✅ |
| PUT | `/tasks/{id}` | Update task by ID | ✅ |
| DELETE | `/tasks/{id}` | Delete task by ID | ✅ |

### Request/Response Examples

#### GET /api/test
**Response:**
```json
{
    "message": "API is working",
    "version": "1.0"
}
```

#### GET /api/tasks
**Headers:**
```
Authorization: Bearer testtoken123
Accept: application/json
```

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "task_name": "Complete project documentation",
            "description": "Write comprehensive API documentation",
            "status": false,
            "created_at": "2025-07-31T10:30:00.000000Z",
            "updated_at": "2025-07-31T10:30:00.000000Z"
        }
    ]
}
```

#### POST /api/tasks
**Headers:**
```
Authorization: Bearer testtoken123
Accept: application/json
Content-Type: application/json
```

**Request Body:**
```json
{
    "task_name": "New Task",
    "description": "Task description (optional)",
    "status": false
}
```

**Response:**
```json
{
    "message": "Task created successfully",
    "data": {
        "id": 1,
        "task_name": "New Task",
        "description": "Task description (optional)",
        "status": false,
        "created_at": "2025-07-31T10:30:00.000000Z",
        "updated_at": "2025-07-31T10:30:00.000000Z"
    }
}
```

#### PUT /api/tasks/{id}
**Headers:**
```
Authorization: Bearer testtoken123
Accept: application/json
Content-Type: application/json
```

**Request Body:**
```json
{
    "task_name": "Updated Task Name",
    "description": "Updated description",
    "status": true
}
```

**Response:**
```json
{
    "message": "Task updated successfully",
    "data": {
        "id": 1,
        "task_name": "Updated Task Name",
        "description": "Updated description",
        "status": true,
        "created_at": "2025-07-31T10:30:00.000000Z",
        "updated_at": "2025-07-31T10:35:00.000000Z"
    }
}
```

#### DELETE /api/tasks/{id}
**Headers:**
```
Authorization: Bearer testtoken123
Accept: application/json
```

**Response:**
```json
{
    "message": "Task deleted successfully"
}
```

## 🔐 Authentication

This API uses simple token-based authentication. Include the token in the Authorization header:

```
Authorization: Bearer testtoken123
```

### Unauthorized Response
```json
{
    "message": "Unauthorized"
}
```

## 🧪 Testing with Postman

### Quick Setup

1. **Import Environment Variables**
   - Create a new environment in Postman
   - Add variables:
     - `base_url`: `http://localhost:8000/api`
     - `token`: `testtoken123`

2. **Test Sequence**
   1. GET `{{base_url}}/test` - Test connection
   2. GET `{{base_url}}/tasks` (without auth) - Should return 401
   3. GET `{{base_url}}/tasks` (with auth) - Should return empty array
   4. POST `{{base_url}}/tasks` - Create a task
   5. GET `{{base_url}}/tasks` - Should show created task
   6. PUT `{{base_url}}/tasks/1` - Update the task
   7. DELETE `{{base_url}}/tasks/1` - Delete the task

### Sample Test Data

**Create Task:**
```json
{
    "task_name": "Learn Postman",
    "description": "Complete API testing tutorial",
    "status": false
}
```

**Update Task:**
```json
{
    "task_name": "Postman Expert",
    "description": "Successfully completed API testing",
    "status": true
}
```

## 🗄️ Database Schema

### Tasks Table

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | INTEGER | NO | AUTO_INCREMENT | Primary key |
| task_name | VARCHAR(255) | NO | - | Task title |
| description | TEXT | YES | NULL | Task description |
| status | BOOLEAN | NO | false | Task completion status |
| created_at | DATETIME | YES | NULL | Creation timestamp |
| updated_at | DATETIME | YES | NULL | Last update timestamp |

## 🛠️ Error Handling

### Validation Errors (422)
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "task_name": [
            "The task name field is required."
        ]
    }
}
```

### Not Found Errors (404)
```json
{
    "message": "Task not found"
}
```

### Unauthorized Errors (401)
```json
{
    "message": "Unauthorized"
}
```

### Server Errors (500)
```json
{
    "error": "Failed to create task",
    "message": "Detailed error message"
}
```

## 📝 Validation Rules

### Creating/Updating Tasks

- `task_name`: Required, string, maximum 255 characters
- `description`: Optional, string
- `status`: Optional, boolean (defaults to false)

### Example Valid Request
```json
{
    "task_name": "Complete project",
    "description": "Finish the API documentation and testing",
    "status": false
}
```

### Example Invalid Request
```json
{
    "description": "Task without name"
}
```

**Response:**
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "task_name": ["The task name field is required."]
    }
}
```

## 🔧 Development

### Project Structure
```
task-management-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── TaskController.php (API)
│   │   │   └── WebTaskController.php (Web)
│   │   ├── Middleware/
│   │   │   └── EnsureTokenIsValid.php
│   │   ├── Kernel.php
│   │   └── routes.php (Web routes)
│   └── Task.php (Model)
├── database/
│   ├── migrations/
│   │   └── 2025_07_31_143403_create_tasks_table.php
│   └── database.sqlite
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php (Main layout)
│       └── tasks/
│           └── index.blade.php (Task management interface)
├── routes/
│   └── api.php (API routes)
└── README.md
```

### Key Files

- **TaskController.php**: Main API controller handling CRUD operations
- **WebTaskController.php**: Web controller for serving the frontend
- **EnsureTokenIsValid.php**: Authentication middleware
- **Task.php**: Eloquent model for tasks
- **api.php**: API route definitions
- **routes.php**: Web route definitions
- **app.blade.php**: Main layout template with Bootstrap and custom styling
- **index.blade.php**: Task management interface with JavaScript functionality

## 🚀 Deployment

### Production Considerations

1. **Environment**
   - Set `APP_ENV=production`
   - Set `APP_DEBUG=false`
   - Generate secure `APP_KEY`

2. **Authentication**
   - Implement proper JWT or OAuth2
   - Use secure tokens
   - Add rate limiting

3. **Database**
   - Consider MySQL/PostgreSQL for production
   - Set up proper database credentials
   - Configure database pooling

4. **Security**
   - Enable HTTPS
   - Configure CORS properly
   - Add input sanitization
   - Implement proper logging

## 🏃‍♂️ Quick Start

### **Option 1: Use the Web Interface (Recommended)**
1. **Clone and setup:**
   ```bash
   git clone <repository-url>
   cd task-management-api
   composer install
   php artisan serve
   ```

2. **Open your browser:**
   ```
   http://localhost:8000
   ```
   
3. **Start managing tasks:**
   - Create your first task using the form
   - View real-time statistics
   - Edit, complete, or delete tasks with ease

### **Option 2: Test the API Directly**
1. **Test connection:**
   ```bash
   curl http://localhost:8000/api/test
   ```
   
2. **Create a task:**
   ```bash
   curl -X POST http://localhost:8000/api/tasks \
     -H "Authorization: Bearer testtoken123" \
     -H "Content-Type: application/json" \
     -d '{"task_name":"Test Task","description":"API test","status":false}'
   ```
   
3. **Get all tasks:**
   ```bash
   curl -H "Authorization: Bearer testtoken123" \
     http://localhost:8000/api/tasks
   ```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

## 📞 Support

If you encounter any issues or have questions:

1. Check the error logs in `storage/logs/laravel.log`
2. Ensure your Laravel server is running (`php artisan serve`)
3. Verify database configuration in `.env`
4. Test with the provided Postman collection

## 🔄 Version History

- **v1.0.0** - Initial release with basic CRUD operations
  - Task management endpoints
  - Token-based authentication
  - SQLite database support
  - Comprehensive error handling

---

**Happy coding! 🚀**
