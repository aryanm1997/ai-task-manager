

<?php $__env->startSection('content'); ?>
<div class="container mx-auto py-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-3xl font-bold"><?php echo e($task->title); ?></h1>
                <p class="text-gray-600 mt-2">Assigned to: <strong><?php echo e($task->user->name); ?></strong></p>
            </div>
            <?php if(Auth::user()->isAdmin()): ?>
                <div class="flex gap-2">
                    <a href="<?php echo e(route('tasks.edit', $task)); ?>" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                        Edit
                    </a>
                    <form action="<?php echo e(route('tasks.destroy', $task)); ?>" method="POST" onsubmit="return confirm('Delete this task?');" class="inline">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Delete
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <?php if($message = Session::get('success')): ?>
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                <?php echo e($message); ?>

            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded shadow p-4">
                <p class="text-gray-600 text-sm">Priority</p>
                <p class="text-lg font-bold">
                    <span class="px-3 py-1 rounded text-black text-sm font-bold
                        <?php echo e($task->priority->value === 'high' ? 'bg-red-500' : ($task->priority->value === 'medium' ? 'bg-yellow-500' : 'bg-green-500')); ?>">
                        <?php echo e(ucfirst($task->priority->value)); ?>

                    </span>
                </p>
            </div>
            <div class="bg-white rounded shadow p-4">
                <p class="text-gray-600 text-sm">Status</p>
                <p class="text-lg font-bold">
                    <span class="px-3 py-1 rounded text-white text-sm font-bold
                        <?php echo e($task->status->value === 'completed' ? 'bg-green-500' : ($task->status->value === 'in_progress' ? 'bg-blue-500' : 'bg-gray-500')); ?>">
                        <?php echo e(str_replace('_', ' ', ucfirst($task->status->value))); ?>

                    </span>
                </p>
            </div>
            <div class="bg-white rounded shadow p-4">
                <p class="text-gray-600 text-sm">Due Date</p>
                <p class="text-lg font-bold"><?php echo e($task->due_date->format('M d, Y')); ?></p>
            </div>
        </div>

        <div class="bg-white rounded shadow p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">Description</h2>
            <p class="text-gray-700 whitespace-pre-wrap"><?php echo e($task->description); ?></p>
        </div>

        <?php if($task->ai_summary): ?>
            <div class="bg-blue-50 rounded shadow p-6 mb-6 border-l-4 border-blue-500">
                <h2 class="text-xl font-bold mb-2 flex items-center">
                    <span class="text-2xl mr-2">🤖</span> AI Analysis
                </h2>
                <div class="mb-4">
                    <p class="text-gray-600 text-sm mb-1">AI Generated Summary</p>
                    <p class="text-gray-800"><?php echo e($task->ai_summary); ?></p>
                </div>
                <?php if($task->ai_priority): ?>
                    <div>
                        <p class="text-gray-600 text-sm mb-1">AI Suggested Priority</p>
                        <p>
                            <span class="px-3 py-1 rounded text-white text-sm font-bold
                                <?php echo e($task->ai_priority->value === 'high' ? 'bg-red-500' : ($task->ai_priority->value === 'medium' ? 'bg-yellow-500' : 'bg-green-500')); ?>">
                                <?php echo e(ucfirst($task->ai_priority->value)); ?>

                            </span>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="flex gap-4">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $task)): ?>
                <a href="<?php echo e(route('tasks.edit', $task)); ?>" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit Task
                </a>
            <?php endif; ?>
            <a href="<?php echo e(route('tasks.index')); ?>" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Tasks
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\ai-task-manager\resources\views/tasks/show.blade.php ENDPATH**/ ?>