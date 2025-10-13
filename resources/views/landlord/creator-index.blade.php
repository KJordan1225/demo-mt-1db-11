{{-- resources/views/landlord/tenants/index.blade.php --}}
@extends('layouts.landlord') {{-- change to your layout --}}

@section('title', 'Tenants')

@section('content')
<div class="container py-4">
    <div class="row">
    {{-- Top bar: Add Tenant --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <a href="{{ route('tenants.create') }}" class="btn btn-primary">
                + Add Tenant
            </a

            {{-- Optional: search/filter slot, flash msgs, etc. --}}
            @if(session('status'))
                <div class="alert alert-success mb-0">{{ session('status') }}</div>
            @endif
        </div>
    </div>
    

    {{-- Mass delete form wraps the table so we can submit selected rows --}}
    <form id="massDeleteForm" method="POST" action="{{ route('tenants.massDestroy') }}" class="mb-4">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
        {{-- Alternatively, use @csrf and @method('DELETE') --}}
        @csrf
        @method('DELETE')

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 140px;" class="text-nowrap">
                                    <div class="d-flex align-items-center gap-2">
                                        <button id="deleteSelectedBtn" type="button" class="btn btn-sm btn-danger" disabled>
                                            Delete
                                        </button>
                                        <div class="form-check m-0">
                                            <input class="form-check-input" type="checkbox" id="checkAll"
                                                    style="border:2px solid #000 !important;">
                                            <label class="form-check-label small" for="checkAll">All</label>
                                        </div>
                                    </div>
                                </th>
                                <th>ID</th>
                                <th>Slug</th>
                                <th>Display Name</th>
                                <th>Created</th>
                                <th class="text-end" style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tenants as $t)
                                <tr>
                                    <td>
                                        <div class="form-check m-0">
                                            <input class="form-check-input row-check" type="checkbox"
                                                    name="ids[]" value="{{ $t->id }}" id="chk-{{ $t->id }}"
                                                    style="border:2px solid #000 !important;">
                                            <label class="form-check-label visually-hidden" for="chk-{{ $t->id }}">Select {{ $t->id }}</label>
                                        </div>
                                    </td>
                                    <td><code>{{ $t->id }}</code></td>
                                    <td>{{ $t->slug ?? $t->data['slug'] ?? $t->id }}</td>
                                    <td>{{ $t['display_name'] ?? '—' }}</td>
                                    <td>{{ optional($t->created_at)->format('Y-m-d') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('tenants.edit', $t->id) }}" class="btn btn-sm btn-outline-primary">
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        No tenants found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if(method_exists($tenants, 'links'))
            <div class="card-footer">
                {{ $tenants->links() }}
            </div>
            @endif
        </div>
    </form>
</div>
@endsection

@push('scripts')
    {{-- SweetAlert2 (CDN). If you already bundle it, remove this line. --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    (function () {
        const form = document.getElementById('massDeleteForm');
        const deleteBtn = document.getElementById('deleteSelectedBtn');
        const checkAll = document.getElementById('checkAll');
        const rowChecks = Array.from(document.querySelectorAll('.row-check'));

        function updateDeleteBtnState() {
            const anyChecked = rowChecks.some(c => c.checked);
            deleteBtn.disabled = !anyChecked;
        }

        // Check-all behavior
        checkAll?.addEventListener('change', function () {
            rowChecks.forEach(c => { c.checked = checkAll.checked; });
            updateDeleteBtnState();
        });

        // Per-row checkbox behavior
        rowChecks.forEach(c => c.addEventListener('change', function () {
            if (!this.checked && checkAll.checked) {
                checkAll.checked = false;
            }
            updateDeleteBtnState();
        }));

        // Delete confirmation
        deleteBtn?.addEventListener('click', function () {
            // If no rows selected, do nothing
            if (deleteBtn.disabled) return;

            Swal.fire({
                title: 'Delete selected tenants?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    })();
    </script>
@endpush
