

<?php $__env->startSection('content'); ?>
<div class="container mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Tasks</h1>
        <?php if(auth()->check() && auth()->user()->isAdmin()): ?>
            <a href="<?php echo e(route('tasks.create')); ?>" class="bg-blue-500 hover:bg-blue-700 text-black font-bold py-2 px-4 rounded">
                + New Task
            </a>
        <?php endif; ?>
    </div>

    <?php if($message = Session::get('success')): ?>
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            <?php echo e($message); ?>

        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="mb-6 bg-gray-100 p-4 rounded">
        <form method="GET" action="<?php echo e(route('tasks.index')); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-bold mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded">
                    <option value="">All Status</option>
                    <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>Pending</option>
                    <option value="in_progress" <?php echo e(request('status') === 'in_progress' ? 'selected' : ''); ?>>In Progress</option>
                    <option value="completed" <?php echo e(request('status') === 'completed' ? 'selected' : ''); ?>>Completed</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">Priority</label>
                <select name="priority" class="w-full px-3 py-2 border border-gray-300 rounded">
                    <option value="">All Priority</option>
                    <option value="low" <?php echo e(request('priority') === 'low' ? 'selected' : ''); ?>>Low</option>
                    <option value="medium" <?php echo e(request('priority') === 'medium' ? 'selected' : ''); ?>>Medium</option>
                    <option value="high" <?php echo e(request('priority') === 'high' ? 'selected' : ''); ?>>High</option>
                </select>
            </div>
            <?php if(auth()->user()->isAdmin() && $users): ?>
                <div>
                    <label class="block text-sm font-bold mb-1">Assigned To</label>
                    <select name="user_id" class="w-full px-3 py-2 border border-gray-300 rounded">
                        <option value="">All Users</option>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>" <?php echo e(request('user_id') == $user->id ? 'selected' : ''); ?>>
                                <?php echo e($user->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            <?php endif; ?>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Tasks Table -->
    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="w-full">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left">Title</th>
                    <th class="px-6 py-3 text-left">Assigned To</th>
                    <th class="px-6 py-3 text-left">Priority</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Due Date</th>
                    <th class="px-6 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold"><?php echo e($task->title); ?></td>
                        <td class="px-6 py-4"><?php echo e($task->user->name); ?></td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded text-black text-sm font-bold
                                <?php echo e($task->priority->value === 'high' ? 'bg-red-500' : ($task->priority->value === 'medium' ? 'bg-yellow-500' : 'bg-green-500')); ?>">
                                <?php echo e(ucfirst($task->priority->value)); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded text-white text-sm font-bold
                                <?php echo e($task->status->value === 'completed' ? 'bg-green-500' : ($task->status->value === 'in_progress' ? 'bg-blue-500' : 'bg-gray-500')); ?>">
                                <?php echo e(str_replace('_', ' ', ucfirst($task->status->value))); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4"><?php echo e($task->due_date->format('M d, Y')); ?></td>
                        <td class="px-6 py-4 flex gap-2">
                            <a href="<?php echo e(route('tasks.show', $task)); ?>" class="bg-blue-500 hover:bg-blue-700 text-white py-1 px-3 rounded text-sm">
                                View
                            </a>
                            <?php if(auth()->check() && auth()->user()->isAdmin()): ?>
                                <a href="<?php echo e(route('tasks.edit', $task)); ?>" class="bg-yellow-500 hover:bg-yellow-700 text-black py-1 px-3 rounded text-sm">
                                    Edit
                                </a>
                                <form action="<?php echo e(route('tasks.destroy', $task)); ?>" method="POST" onsubmit="return confirm('Delete this task?');" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-black py-1 px-3 rounded text-sm">
                                        Delete
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No tasks found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        <?php echo e($tasks->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\ai-task-manager\resources\views/tasks/index.blade.php ENDPATH**/ ?>