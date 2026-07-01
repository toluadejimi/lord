<div class="card mb-4">
    <div class="card-header">System Maintenance</div>
    <div class="card-body">
        <p class="text-muted small mb-3">Run <code>php artisan migrate</code> and clear caches after deployments or config changes.</p>

        @if(session('maintenance_output'))
            <pre class="small bg-light p-3 rounded mb-3 mb-0" style="max-height:220px;overflow:auto;">{{ session('maintenance_output') }}</pre>
        @endif

        <div class="mt-3">
            <form method="post" action="{{ url('admin/maintenance/migrate') }}" class="d-inline"
                  onsubmit="return confirm('Run database migrations now?');">
                @csrf
                <button type="submit" class="btn btn-warning btn-sm">
                    <i class="fas fa-database"></i> Run Migrations
                </button>
            </form>
            <form method="post" action="{{ url('admin/maintenance/clear-cache') }}" class="d-inline ml-2"
                  onsubmit="return confirm('Clear application cache, config, routes, and views?');">
                @csrf
                <button type="submit" class="btn btn-secondary btn-sm">
                    <i class="fas fa-broom"></i> Clear Cache
                </button>
            </form>
        </div>
    </div>
</div>
