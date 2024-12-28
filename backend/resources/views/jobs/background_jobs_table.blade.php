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
        @foreach ($jobs as $job)
        <tr>
            <td>{{ $job->id }}</td>
            <td>{{ $job->pid }}</td>
            <td>{{ $job->classname }}</td>
            <td>{{ $job->method }}</td>
            <td>{{ $job->status }}</td>
            <td>{{ $job->priority }}</td>
            <td>{{ $job->started_at }}</td>
            <td>{{ $job->available_at }}</td>
            <td>{{ $job->delay }} seconds</td>
            <td>{{ $job->retries }}</td>
            <td>{{ $job->max_retries }}</td>
            <td>{{ $job->timeout }}</td>
            <td>
                @if ($job->status == \App\Enums\JobStatus::RUNNING)
                <form action="{{ route('backgroundJobs.kill', $job->id) }}" method="POST">
                    @csrf
                    <button type="submit">Kill</button>
                </form>
                @endif
                <form action="{{ route('backgroundJobs.delete', $job->id) }}" method="POST">
                    @csrf
                    <button type="submit">Cancel</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>