# AI Task Manager API Documentation

## Authentication

The API uses **Bearer Token Authentication**. You can use either:
1. **Laravel Sanctum** (recommended for production)
2. **API Token** (simpler setup)

### Getting an API Token

#### Option 1: Using Tinker (Development)
```bash
php artisan tinker
$user = \App\Models\User::first();
$token = $user->createToken('api-token')->plainTextToken;
echo $token;
```

#### Option 2: Create via Database
```bash
php artisan tinker
$user = \App\Models\User::find(1);
$token = $user->createToken('api-token')->plainTextToken;
```

Copy the token and use it in all API requests.

## API Endpoints

### 1. Get All Tasks
```http
GET /api/tasks
Authorization: Bearer YOUR_TOKEN
Accept: application/json
```

**Query Parameters:**
- `status` - Filter by status (pending, in_progress, completed)
- `priority` - Filter by priority (low, medium, high)

**Response:** `200 OK`
```json
[
  {
    "id": 1,
    "title": "Task Title",
    "description": "Task description",
    "priority": "high",
    "status": "pending",
    "due_date": "2026-02-01",
    "assigned_to": {
      "id": 1,
      "name": "User Name",
      "email": "user@example.com"
    },
    "ai_summary": "AI generated summary",
    "ai_priority": "high",
    "completed_at": null,
    "created_at": "2026-01-21 10:00:00",
    "updated_at": "2026-01-21 10:00:00"
  }
]
```

### 2. Get Task by ID
```http
GET /api/tasks/{id}
Authorization: Bearer YOUR_TOKEN
Accept: application/json
```

**Response:** `200 OK` (same as above single object)

### 3. Create Task
```http
POST /api/tasks
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
Accept: application/json

{
  "title": "New Task",
  "description": "Task description here",
  "priority": "high",
  "due_date": "2026-02-01",
  "assigned_to": 1
}
```

**Validation Rules:**
- `title` - Required, max 255 characters
- `description` - Required, max 5000 characters
- `priority` - Required, one of: `low`, `medium`, `high`
- `due_date` - Required, must be in future (format: YYYY-MM-DD)
- `assigned_to` - Required, must be valid user ID

**Response:** `201 Created`
```json
{
  "id": 2,
  "title": "New Task",
  "description": "Task description here",
  "priority": "high",
  "status": "pending",
  "due_date": "2026-02-01",
  "assigned_to": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "ai_summary": "AI generated summary",
  "ai_priority": "high",
  "completed_at": null,
  "created_at": "2026-01-21 12:00:00",
  "updated_at": "2026-01-21 12:00:00"
}
```

### 4. Update Task Status
```http
PATCH /api/tasks/{id}/status
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
Accept: application/json

{
  "status": "completed"
}
```

**Valid Status Values:**
- `pending` - Task is pending
- `in_progress` - Task is in progress
- `completed` - Task is completed

**Response:** `200 OK` (full task object)

### 5. Get AI Summary
```http
GET /api/tasks/{id}/ai-summary
Authorization: Bearer YOUR_TOKEN
Accept: application/json
```

**Response:** `200 OK`
```json
{
  "id": 1,
  "title": "Task Title",
  "ai_summary": "AI generated summary text",
  "ai_priority": "high",
  "generated_at": "2026-01-21 10:00:00"
}
```

### 6. Delete Task
```http
DELETE /api/tasks/{id}
Authorization: Bearer YOUR_TOKEN
Accept: application/json
```

**Response:** `204 No Content` (empty body)

## Error Responses

### 401 Unauthorized
```json
{
  "message": "Unauthenticated"
}
```

### 403 Forbidden
```json
{
  "message": "This action is unauthorized"
}
```

### 404 Not Found
```json
{
  "message": "No query results found for model [App\\Models\\Task]"
}
```

### 422 Validation Error
```json
{
  "message": "The given data was invalid",
  "errors": {
    "title": [
      "The title field is required"
    ],
    "priority": [
      "The priority must be one of: low, medium, high"
    ]
  }
}
```

### 500 Server Error
```json
{
  "error": "Failed to retrieve tasks",
  "message": "Error details here"
}
```

## Testing with Postman

1. **Import Collection:** Import `AI_Task_Manager_API.postman_collection.json`
2. **Set Bearer Token:**
   - In Postman, go to the collection settings
   - Under "Authorization" tab, select "Bearer Token"
   - Enter your API token
3. **Test Endpoints:** Run each request

## Setting Up Laravel Sanctum (Optional but Recommended)

If you want to use Sanctum for production:

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

Then update `.env`:
```
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1
SESSION_DOMAIN=localhost
```

## Rate Limiting

Currently, no rate limiting is applied. For production, add to `app/Http/Kernel.php`:
```php
'api' => [
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    'throttle:60,1',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

## CORS (Cross-Origin)

CORS is configured in `config/cors.php`. Update for your frontend domain:
```php
'allowed_origins' => ['http://localhost:3000'],
```

## Examples

### cURL Examples

```bash
# Get all tasks
curl -X GET "http://127.0.0.1:8000/api/tasks" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"

# Create task
curl -X POST "http://127.0.0.1:8000/api/tasks" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "New Task",
    "description": "Description",
    "priority": "high",
    "due_date": "2026-02-01",
    "assigned_to": 1
  }'

# Update status
curl -X PATCH "http://127.0.0.1:8000/api/tasks/1/status" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"status": "completed"}'
```

### JavaScript/Fetch Examples

```javascript
// Get all tasks
const token = 'YOUR_TOKEN_HERE';

fetch('http://127.0.0.1:8000/api/tasks', {
  method: 'GET',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
})
.then(response => response.json())
.then(data => console.log(data));

// Create task
fetch('http://127.0.0.1:8000/api/tasks', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    title: 'New Task',
    description: 'Task description',
    priority: 'high',
    due_date: '2026-02-01',
    assigned_to: 1
  })
})
.then(response => response.json())
.then(data => console.log(data));
```

## Troubleshooting

### "Auth guard [sanctum] is not defined"
- Use `auth:api` instead of `auth:sanctum` in routes
- Or install and configure Sanctum (see above)

### 401 Unauthorized
- Check if token is correct
- Verify token hasn't expired
- Ensure `Authorization` header is properly formatted

### 422 Validation Error
- Check all required fields are present
- Verify data types and formats
- Review error message for specific field issues

### 404 Not Found
- Verify task ID exists
- Check the URL path is correct
- Ensure you're using the right HTTP method
