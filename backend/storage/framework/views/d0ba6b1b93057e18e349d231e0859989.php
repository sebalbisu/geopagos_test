<!DOCTYPE html>
<html>

<head>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title>Background Jobs</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class='mt-5 ml-5 mr-5'>
    <h1>Background Jobs</h1>

    <form action="<?php echo e(route('backgroundJobs.addTests')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <button type="submit">Add Test Jobs</button>
    </form>

    <button id="toggle-refresh" class="btn btn-secondary mt-3">Disable Auto Refresh</button>


    <div id="backgroundJobs">
        <?php echo $__env->make('jobs.background_jobs_table', ['jobs' => $jobs], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>


    <script>
        $(document).ready(function() {
            let autoRefresh = true;
            let intervalId = setInterval(function() {
                if (autoRefresh) {
                    $('#backgroundJobs').load('/background-jobs?only-table=1');
                }
            }, 300);

            $('#toggle-refresh').click(function() {
                autoRefresh = !autoRefresh;
                $(this).text(autoRefresh ? 'Disable Auto Refresh' : 'Enable Auto Refresh');
            });
        });
    </script>
</body>

</html><?php /**PATH /var/www/backend/resources/views/jobs/index.blade.php ENDPATH**/ ?>