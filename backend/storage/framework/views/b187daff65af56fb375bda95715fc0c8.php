<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>PID</th>
            <th>Class</th>
            <th>Method</th>
            <th>Status</th>
            <th>Priority</th>
            <th>Started At</th>
            <th>Available At</th>
            <th>Delay</th>
            <th>Retries</th>
            <th>Max Retries</th>
            <th>Timeout</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($job->id); ?></td>
            <td><?php echo e($job->pid); ?></td>
            <td><?php echo e($job->classname); ?></td>
            <td><?php echo e($job->method); ?></td>
            <td><?php echo e($job->status); ?></td>
            <td><?php echo e($job->priority); ?></td>
            <td><?php echo e($job->started_at); ?></td>
            <td><?php echo e($job->available_at); ?></td>
            <td><?php echo e($job->delay); ?> seconds</td>
            <td><?php echo e($job->retries); ?></td>
            <td><?php echo e($job->max_retries); ?></td>
            <td><?php echo e($job->timeout); ?></td>
            <td>
                <?php if($job->status == \App\Enums\JobStatus::RUNNING): ?>
                <form action="<?php echo e(route('backgroundJobs.kill', $job->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit">Kill</button>
                </form>
                <?php endif; ?>
                <form action="<?php echo e(route('backgroundJobs.delete', $job->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit">Cancel</button>
                </form>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table><?php /**PATH /var/www/backend/resources/views/jobs/background_jobs_table.blade.php ENDPATH**/ ?>