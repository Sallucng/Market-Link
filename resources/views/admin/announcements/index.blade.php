@extends('layouts.admin')

@section('title', 'Platform Announcements — Admin MarketLink')

@section('content')
<div class="py-2">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success text-decoration-none">Backoffice</a></li>
            <li class="breadcrumb-item active" aria-current="page">Announcements</li>
        </ol>
    </nav>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <span class="badge bg-primary text-white px-3 py-1 rounded-pill mb-1">System Communications</span>
            <h2 class="heading-serif fw-bold text-dark mb-0">Platform-Wide Announcements</h2>
            <small class="text-muted">Broadcast weather advisories, holiday schedules, and market updates to shoppers (SRS §1.6)</small>
        </div>
    </div>

    <div class="row g-4">
        <!-- Publish Form -->
        <div class="col-lg-4">
            <div class="card card-custom p-4 bg-white border-0 shadow-sm">
                <h5 class="heading-serif fw-bold text-dark mb-3">Broadcast Alert</h5>

                <form action="{{ route('admin.announcements.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Announcement Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Saturday Strawberry Festival" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Badge Type</label>
                        <select name="badge_type" class="form-select">
                            <option value="info">Info (Blue)</option>
                            <option value="success">Success / Event (Green)</option>
                            <option value="warning">Warning / Schedule Alert (Yellow)</option>
                            <option value="danger">Urgent / Weather Closure (Red)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-dark">Notice Content <span class="text-danger">*</span></label>
                        <textarea name="content" rows="4" class="form-control" placeholder="Details visible to all platform shoppers on the homepage..." required></textarea>
                    </div>

                    <button type="submit" class="btn btn-brand w-100 rounded-pill">Publish Announcement</button>
                </form>
            </div>
        </div>

        <!-- Announcements Table -->
        <div class="col-lg-8">
            <div class="card card-custom p-4 bg-white border-0 shadow-sm">
                <h5 class="heading-serif fw-bold text-dark mb-3">Published Notices</h5>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Announcement</th>
                                <th>Type</th>
                                <th>Published By</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($announcements as $ann)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $ann->title }}</div>
                                        <small class="text-muted text-truncate d-inline-block" style="max-width: 280px;">{{ $ann->content }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $ann->badge_type }} text-uppercase" style="font-size: 0.72rem;">
                                            {{ $ann->badge_type }}
                                        </span>
                                    </td>
                                    <td class="small text-muted">{{ $ann->creator->name ?? 'Admin' }}</td>
                                    <td>
                                        <form action="{{ route('admin.announcements.toggle', $ann->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $ann->is_active ? 'btn-success' : 'btn-secondary' }} rounded-pill" style="font-size: 0.72rem;">
                                                {{ $ann->is_active ? 'Active' : 'Inactive' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.announcements.destroy', $ann->id) }}" method="POST" onsubmit="return confirm('Delete this announcement?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $announcements->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
