# Task Management API - Demo Script

## Quick Demo Instructions

### 1. Start the Application
```bash
php artisan serve
```

### 2. Open Web Interface
- Go to: http://localhost:8000
- You should see a beautiful gradient interface

### 3. Test the Features

#### Create Tasks
1. Fill in the "Task Name" field (e.g., "Learn Laravel")
2. Add a description (optional)
3. Set status (Pending/Completed)
4. Click "Create Task"

#### Manage Tasks
- **Edit**: Click the edit button to modify a task
- **Toggle Status**: Mark as complete/pending
- **Delete**: Remove unwanted tasks
- **Filter**: Use dropdown to show specific task types

#### View Statistics
- Watch the dashboard cards update in real-time
- Total tasks, completed, and pending counts

### 4. Test API Endpoints (Optional)

#### Get all tasks
```bash
curl -H "Authorization: Bearer testtoken123" http://localhost:8000/api/tasks
```

#### Create a task via API
```bash
curl -X POST http://localhost:8000/api/tasks \
  -H "Authorization: Bearer testtoken123" \
  -H "Content-Type: application/json" \
  -d '{"task_name":"API Task","description":"Created via API","status":false}'
```

#### Update a task
```bash
curl -X PUT http://localhost:8000/api/tasks/1 \
  -H "Authorization: Bearer testtoken123" \
  -H "Content-Type: application/json" \
  -d '{"task_name":"Updated Task","description":"Updated via API","status":true}'
```

#### Delete a task
```bash
curl -X DELETE http://localhost:8000/api/tasks/1 \
  -H "Authorization: Bearer testtoken123"
```

### 5. Test Error Scenarios

#### Try without authentication
```bash
curl http://localhost:8000/api/tasks
# Should return: {"message":"Unauthorized"}
```

#### Try invalid data
```bash
curl -X POST http://localhost:8000/api/tasks \
  -H "Authorization: Bearer testtoken123" \
  -H "Content-Type: application/json" \
  -d '{"description":"No task name"}'
# Should return validation error
```

### 6. Mobile Testing
- Open http://localhost:8000 on your phone
- The interface is fully responsive
- All features work on mobile devices

## Expected Results

✅ **Web Interface**: Modern, responsive task management interface
✅ **Real-time Updates**: Statistics update automatically
✅ **API Endpoints**: All CRUD operations working
✅ **Authentication**: Token-based security
✅ **Error Handling**: Graceful error messages
✅ **Mobile Support**: Works on all devices

## Troubleshooting

If something doesn't work:
1. Check Laravel server is running: `php artisan serve`
2. Check database exists: `ls database/database.sqlite`
3. Clear caches: `php artisan config:clear && php artisan route:clear`
4. Check browser console for JavaScript errors
5. Check Laravel logs: `tail -f storage/logs/laravel.log`
