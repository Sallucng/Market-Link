@extends('layouts.admin')

@section('title', 'Manage Farmers Markets — Admin MarketLink')

@section('content')
<div class="py-2">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success text-decoration-none">Backoffice</a></li>
            <li class="breadcrumb-item active" aria-current="page">Farmers Markets</li>
        </ol>
    </nav>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <span class="badge bg-primary text-white px-3 py-1 rounded-pill mb-1">Market Locations</span>
            <h2 class="heading-serif fw-bold text-dark mb-0">Manage Farmers Markets</h2>
            <small class="text-muted">Configure operating days, hours, and map coordinates for physical market venues (SRS §1.6)</small>
        </div>

        <a href="{{ route('admin.markets.create') }}" class="btn btn-brand rounded-pill px-4">
            <i class="bi bi-plus-circle me-1"></i> Add New Market
        </a>
    </div>

    <div class="card card-custom p-4 bg-white border-0 shadow-sm mb-4">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>Market Name & Venue</th>
                        <th>City</th>
                        <th>Operating Schedule</th>
                        <th>Geolocation Coordinates</th>
                        <th class="text-center">Active Farmers</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($markets as $m)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $m->image_url }}" alt="{{ $m->name }}" class="rounded-3 shadow-sm" style="width: 48px; height: 48px; object-fit: cover;">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $m->name }}</div>
                                        <small class="text-muted"><i class="bi bi-geo-alt me-1 text-danger"></i>{{ $m->address }}</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $m->city }}</span></td>
                            <td class="small">
                                <div class="fw-semibold text-dark">{{ is_array($m->operating_days) ? implode(', ', $m->operating_days) : (string)$m->operating_days }}</div>
                                <div class="text-muted">{{ $m->timings }}</div>
                            </td>
                            <td class="small text-muted font-monospace">
                                <a href="https://www.openstreetmap.org/?mlat={{ $m->latitude }}&mlon={{ $m->longitude }}#map=16/{{ $m->latitude }}/{{ $m->longitude }}" target="_blank" class="text-decoration-none text-success">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>{{ $m->latitude }}, {{ $m->longitude }}
                                </a>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-brand-light text-success border border-success border-opacity-25 rounded-pill px-3">
                                    {{ $m->farmers_count }} stalls
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('admin.markets.edit', $m->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                        <i class="bi bi-pencil me-1"></i> Edit
                                    </a>

                                    <form action="{{ route('admin.markets.destroy', $m->id) }}" method="POST" onsubmit="return confirm('Delete this market? Stalls linked to this market will need re-assignment.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $markets->links() }}
        </div>
    </div>
</div>
@endsection
