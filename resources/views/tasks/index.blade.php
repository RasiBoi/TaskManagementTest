@extends('layouts.app')

@section('title', 'Task Management Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Alert Messages -->
        <div id="alertContainer"></div>
        
        <!-- Task Creation Form -->
        <div class="task-form">
            <h3 class="mb-4"><i class="fas fa-plus-circle"></i> Create New Task</h3>
            <form id="taskForm">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="taskName" class="form-label">Task Name *</label>
                            <input type="text" class="form-control" id="taskName" name="task_name" 
                                   placeholder="Enter task name..." required maxlength="255">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="taskStatus" class="form-label">Status</label>
                            <select class="form-control" id="taskStatus" name="status">
                                <option value="false">Pending</option>
                                <option value="true">Completed</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="taskDescription" class="form-label">Description</label>
                    <textarea class="form-control" id="taskDescription" name="description" rows="3"
                              placeholder="Enter task description (optional)..."></textarea>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <button type="submit" class="btn btn-primary btn-custom">
                        <i class="fas fa-save"></i> Create Task
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-custom" onclick="clearForm()">
                        <i class="fas fa-times"></i> Clear
                    </button>
                </div>
            </form>
        </div>

        <!-- Task Statistics -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-center border-0" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; border-radius: 15px;">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-list"></i> Total Tasks</h5>
                        <h2 id="totalTasks">0</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center border-0" style="background: linear-gradient(135deg, #10b981, #059669); color: white; border-radius: 15px;">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-check-circle"></i> Completed</h5>
                        <h2 id="completedTasks">0</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center border-0" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; border-radius: 15px;">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-clock"></i> Pending</h5>
                        <h2 id="pendingTasks">0</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tasks Filter and Controls -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3><i class="fas fa-clipboard-list"></i> Your Tasks</h3>
            <div class="d-flex gap-2">
                <select id="filterStatus" class="form-control" style="width: auto;">
                    <option value="all">All Tasks</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                </select>
                <button class="btn btn-outline-primary btn-custom" onclick="loadTasks()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </div>

        <!-- Loading Spinner -->
        <div id="loading" class="text-center loading">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <!-- Tasks List -->
        <div id="tasksList">
            <!-- Tasks will be loaded here -->
        </div>
    </div>
</div>

<!-- Edit Task Modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header" style="background: linear-gradient(135deg, #4f46e5, #6366f1); color: white; border-radius: 15px 15px 0 0;">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Task</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editTaskForm">
                    <input type="hidden" id="editTaskId" name="id">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="editTaskName" class="form-label">Task Name *</label>
                                <input type="text" class="form-control" id="editTaskName" name="task_name" 
                                       placeholder="Enter task name..." required maxlength="255">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="editTaskStatus" class="form-label">Status</label>
                                <select class="form-control" id="editTaskStatus" name="status">
                                    <option value="false">Pending</option>
                                    <option value="true">Completed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editTaskDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editTaskDescription" name="description" rows="3"
                                  placeholder="Enter task description..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-custom" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary btn-custom" onclick="updateTask()">
                    <i class="fas fa-save"></i> Update Task
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // API Configuration
    const API_BASE_URL = '/api';
    const API_TOKEN = 'testtoken123';
    
    // Global variables
    let allTasks = [];
    
    // Initialize the application
    document.addEventListener('DOMContentLoaded', function() {
        loadTasks();
        setupEventListeners();
    });
    
    // Setup event listeners
    function setupEventListeners() {
        // Task form submission
        document.getElementById('taskForm').addEventListener('submit', createTask);
        
        // Filter change
        document.getElementById('filterStatus').addEventListener('change', filterTasks);
    }
    
    // Show alert message
    function showAlert(message, type = 'success') {
        const alertContainer = document.getElementById('alertContainer');
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        alertContainer.appendChild(alertDiv);
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
    
    // Show loading spinner
    function showLoading() {
        document.getElementById('loading').style.display = 'block';
    }
    
    // Hide loading spinner
    function hideLoading() {
        document.getElementById('loading').style.display = 'none';
    }
    
    // Make API request
    async function apiRequest(endpoint, options = {}) {
        const url = `${API_BASE_URL}${endpoint}`;
        const defaultOptions = {
            headers: {
                'Authorization': `Bearer ${API_TOKEN}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        };
        
        const finalOptions = { ...defaultOptions, ...options };
        
        try {
            const response = await fetch(url, finalOptions);
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || `HTTP error! status: ${response.status}`);
            }
            
            return data;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }
    
    // Load all tasks
    async function loadTasks() {
        showLoading();
        try {
            const response = await apiRequest('/tasks');
            allTasks = response.data || [];
            displayTasks(allTasks);
            updateStatistics();
            showAlert('Tasks loaded successfully!', 'info');
        } catch (error) {
            showAlert(`Error loading tasks: ${error.message}`, 'danger');
            allTasks = [];
            displayTasks([]);
        } finally {
            hideLoading();
        }
    }
    
    // Display tasks
    function displayTasks(tasks) {
        const tasksList = document.getElementById('tasksList');
        
        if (tasks.length === 0) {
            tasksList.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-clipboard-list"></i>
                    <h4>No tasks found</h4>
                    <p>Create your first task to get started!</p>
                </div>
            `;
            return;
        }
        
        const tasksHtml = tasks.map(task => `
            <div class="task-card ${task.status ? 'task-completed' : ''}">
                <div class="task-title">${escapeHtml(task.task_name)}</div>
                <div class="task-description">${escapeHtml(task.description || 'No description')}</div>
                <div class="task-actions">
                    <span class="task-status ${task.status ? 'status-completed' : 'status-pending'}">
                        <i class="fas ${task.status ? 'fa-check-circle' : 'fa-clock'}"></i>
                        ${task.status ? 'Completed' : 'Pending'}
                    </span>
                    <button class="btn btn-outline-primary btn-sm btn-custom" onclick="editTask(${task.id})">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn ${task.status ? 'btn-outline-warning' : 'btn-outline-success'} btn-sm btn-custom" 
                            onclick="toggleTaskStatus(${task.id}, ${!task.status})">
                        <i class="fas ${task.status ? 'fa-undo' : 'fa-check'}"></i>
                        ${task.status ? 'Mark Pending' : 'Mark Complete'}
                    </button>
                    <button class="btn btn-outline-danger btn-sm btn-custom" onclick="deleteTask(${task.id})">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
                <div class="task-meta">
                    <span><i class="fas fa-calendar-alt"></i> Created: ${formatDate(task.created_at)}</span>
                    <span><i class="fas fa-clock"></i> Updated: ${formatDate(task.updated_at)}</span>
                </div>
            </div>
        `).join('');
        
        tasksList.innerHTML = tasksHtml;
    }
    
    // Update statistics
    function updateStatistics() {
        const total = allTasks.length;
        const completed = allTasks.filter(task => task.status).length;
        const pending = total - completed;
        
        document.getElementById('totalTasks').textContent = total;
        document.getElementById('completedTasks').textContent = completed;
        document.getElementById('pendingTasks').textContent = pending;
    }
    
    // Filter tasks
    function filterTasks() {
        const filterValue = document.getElementById('filterStatus').value;
        let filteredTasks = allTasks;
        
        if (filterValue === 'completed') {
            filteredTasks = allTasks.filter(task => task.status);
        } else if (filterValue === 'pending') {
            filteredTasks = allTasks.filter(task => !task.status);
        }
        
        displayTasks(filteredTasks);
    }
    
    // Create new task
    async function createTask(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        const taskData = {
            task_name: formData.get('task_name'),
            description: formData.get('description'),
            status: formData.get('status') === 'true'
        };
        
        try {
            showLoading();
            await apiRequest('/tasks', {
                method: 'POST',
                body: JSON.stringify(taskData)
            });
            
            showAlert('Task created successfully!', 'success');
            clearForm();
            loadTasks();
        } catch (error) {
            showAlert(`Error creating task: ${error.message}`, 'danger');
        } finally {
            hideLoading();
        }
    }
    
    // Edit task
    function editTask(taskId) {
        const task = allTasks.find(t => t.id === taskId);
        if (!task) return;
        
        document.getElementById('editTaskId').value = task.id;
        document.getElementById('editTaskName').value = task.task_name;
        document.getElementById('editTaskDescription').value = task.description || '';
        document.getElementById('editTaskStatus').value = task.status.toString();
        
        const modal = new bootstrap.Modal(document.getElementById('editTaskModal'));
        modal.show();
    }
    
    // Update task
    async function updateTask() {
        const formData = new FormData(document.getElementById('editTaskForm'));
        const taskId = formData.get('id');
        const taskData = {
            task_name: formData.get('task_name'),
            description: formData.get('description'),
            status: formData.get('status') === 'true'
        };
        
        try {
            await apiRequest(`/tasks/${taskId}`, {
                method: 'PUT',
                body: JSON.stringify(taskData)
            });
            
            showAlert('Task updated successfully!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('editTaskModal')).hide();
            loadTasks();
        } catch (error) {
            showAlert(`Error updating task: ${error.message}`, 'danger');
        }
    }
    
    // Toggle task status
    async function toggleTaskStatus(taskId, newStatus) {
        const task = allTasks.find(t => t.id === taskId);
        if (!task) return;
        
        const taskData = {
            task_name: task.task_name,
            description: task.description,
            status: newStatus
        };
        
        try {
            await apiRequest(`/tasks/${taskId}`, {
                method: 'PUT',
                body: JSON.stringify(taskData)
            });
            
            showAlert(`Task marked as ${newStatus ? 'completed' : 'pending'}!`, 'success');
            loadTasks();
        } catch (error) {
            showAlert(`Error updating task: ${error.message}`, 'danger');
        }
    }
    
    // Delete task
    async function deleteTask(taskId) {
        if (!confirm('Are you sure you want to delete this task?')) {
            return;
        }
        
        try {
            await apiRequest(`/tasks/${taskId}`, {
                method: 'DELETE'
            });
            
            showAlert('Task deleted successfully!', 'success');
            loadTasks();
        } catch (error) {
            showAlert(`Error deleting task: ${error.message}`, 'danger');
        }
    }
    
    // Clear form
    function clearForm() {
        document.getElementById('taskForm').reset();
    }
    
    // Utility functions
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    }
</script>
@endpush
