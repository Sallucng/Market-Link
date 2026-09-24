@extends('layouts.admin')

@section('title', 'Produce Categories — Admin MarketLink')

@section('content')
<div class="py-2">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success text-decoration-none">Backoffice</a></li>
            <li class="breadcrumb-item active" aria-current="page">Produce Categories</li>
        </ol>
    </nav>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <span class="badge bg-primary text-white px-3 py-1 rounded-pill mb-1">Master Data Management</span>
            <h2 class="heading-serif fw-bold text-dark mb-0">Produce Categories Master Data</h2>
            <small class="text-muted">Manage product taxonomy used across all farmer catalogs (SRS §1.6)</small>
        </div>
    </div>

    <div class="row g-4">
        <!-- Add Category Form -->
        <div class="col-lg-4">
            <div class="card card-custom p-4 bg-white border-0 shadow-sm">
                <h5 class="heading-serif fw-bold text-dark mb-3">Add New Category</h5>

                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Microgreens & Sprouts" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Bootstrap Icon Class</label>
                        <input type="text" name="icon" class="form-control" placeholder="bi-flower1" value="bi-basket">
                        <small class="text-muted" style="font-size: 0.72rem;">e.g. bi-basket, bi-flower1, bi-apple, bi-egg</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-dark">Description</label>
                        <textarea name="description" rows="3" class="form-control" placeholder="Brief category description..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-brand w-100 rounded-pill">Create Category</button>
                </form>
            </div>
        </div>

        <!-- Categories Table -->
        <div class="col-lg-8">
            <div class="card card-custom p-4 bg-white border-0 shadow-sm">
                <h5 class="heading-serif fw-bold text-dark mb-3">Existing Categories</h5>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Category</th>
                                <th>Description</th>
                                <th class="text-center">Products</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $cat)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="p-2 bg-brand-light text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                <i class="bi {{ $cat->icon ?? 'bi-basket' }}"></i>
                                            </div>
                                            <span class="fw-bold text-dark">{{ $cat->name }}</span>
                                        </div>
                                    </td>
                                    <td class="small text-muted">{{ $cat->description ?? 'No description' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border rounded-pill">{{ $cat->products_count }} items</span>
                                    </td>
                                    <td class="text-end">
                                        @if($cat->products_count == 0)
                                            <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Delete category {{ $cat->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted small" title="Cannot delete category containing products">Protected</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
