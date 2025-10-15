@extends('layouts.landlord')

@section('content')
<style>
    /* Brand-aware theming (falls back if not provided) */
    :root{
        --brand-primary: {{ e($branding['primary_color'] ?? '#6C2BD9') }};
        --brand-accent:  {{ e($branding['accent_color']  ?? '#F59E0B') }};
        --brand-bg:      {{ e($branding['bg_color']      ?? '#F8FAFC') }};
        --brand-text:    {{ e($branding['text_color']    ?? '#0F172A') }};
    }

    .contact-hero {
        background: linear-gradient(135deg, var(--brand-bg), #ffffff);
        border-radius: 1rem;
    }
    .shadow-smooth { box-shadow: 0 8px 24px rgba(17, 24, 39, .08); }
    .badge-accent { background: var(--brand-accent); color: #111; }

    /* Floating helper chips */
    .chip {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        border: 1px solid #e5e7eb;
        border-radius: 999px;
        padding: .35rem .7rem;
        font-size: .8rem;
        background: #fff;
    }

    .img-frame {
        border: 2px solid #e5e7eb;
        border-radius: .75rem;
    }

    /* Mobile niceties */
    @media (max-width: 576px) {
        .contact-hero { border-radius: .75rem; }
        .card-body { padding: 1rem !important; }
        .display-5 { font-size: 1.9rem; }
    }
</style>

<div class="container py-4 py-md-5">

    <!-- Header / Hero -->
    <div class="contact-hero p-4 p-md-5 shadow-smooth mb-4 mb-md-5">
        <div class="row align-items-center g-4">
            <div class="col-12 col-md-6 text-center text-md-start">
                <h1 class="display-5 fw-bold mb-2">Contact Site Admin</h1>
                <p class="lead text-muted mb-3">
                    Have a question, idea, or issue? Drop us a message and we’ll get back to you.
                    Attach a file if it helps explain—screenshots welcome!
                </p>

                <div class="d-flex flex-wrap gap-2">
                    <span class="chip"><span>⚡</span> Fast responses</span>
                    <span class="chip"><span>🛡️</span> Secure</span>
                    <span class="badge badge-accent rounded-pill">We’re here to help</span>
                </div>
            </div>

            <div class="col-12 col-md-6 text-center">
                <img
                    class="img-fluid img-frame"
                    src="https://picsum.photos/seed/contact-admin/640/360"
                    alt="Contact support illustration"
                    loading="lazy"
                >
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Contact Form -->
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-3 p-md-4">
                    {{-- Flash success/error (optional) --}}
                    @if(session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    <form
                        method="POST"
                        action="{{ route('guest.contact.send') ?? '#' }}"
                        enctype="multipart/form-data"
                        class="needs-validation"
                        novalidate
                    >
                        @csrf

                        {{-- Subject --}}
                        <div class="mb-3">
                            <label for="subject" class="form-label fw-semibold">Subject</label>
                            <input
                                type="text"
                                id="subject"
                                name="subject"
                                class="form-control border border-2 @error('subject') is-invalid border-danger @else border-secondary @enderror"
                                value="{{ old('subject') }}"
                                required
                                maxlength="150"
                                placeholder="Briefly summarize your request"
                            >
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="form-text">Max 150 characters.</div>
                            @enderror
                        </div>

                        {{-- Content --}}
                        <div class="mb-3">
                            <label for="content" class="form-label fw-semibold">Content</label>
                            <textarea
                                id="content"
                                name="content"
                                rows="5"
                                class="form-control border border-2 @error('content') is-invalid border-danger @else border-secondary @enderror"
                                required
                                placeholder="Tell us what’s going on. Include steps to reproduce, links, or context."
                            >{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="form-text">Be as specific as possible so we can help quickly.</div>
                            @enderror
                        </div>

                        {{-- Import file button + textbox --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold d-block">Attachment (optional)</label>

                            <div class="input-group">
                                <button type="button" class="btn btn-outline-secondary" id="pickFileBtn">
                                    📎 Import file
                                </button>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="fileNameBox"
                                    placeholder="No file selected"
                                    readonly
                                >
                            </div>

                            {{-- Hidden file input that the button triggers --}}
                            <input
                                type="file"
                                class="d-none"
                                id="attachment"
                                name="attachment"
                                accept="image/*,.pdf,.txt,.zip,.rar,.7z,.doc,.docx,.xls,.xlsx,.csv"
                            >

                            <div class="form-text mt-1">
                                Accepted: images, PDF, text, ZIP, Office docs. Max size may apply.
                            </div>

                            @error('attachment')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Actions --}}
                        <div class="d-grid d-sm-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                ✉️ Send
                            </button>
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Helpful Sidebar -->
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body p-3 p-md-4">
                    <h5 class="mb-2">Tips for a quick resolution</h5>
                    <ul class="small text-muted mb-0">
                        <li>Include your browser/OS if reporting a bug.</li>
                        <li>Attach screenshots or a short video, if possible.</li>
                        <li>Tell us what you expected vs. what happened.</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm mt-3 mt-md-4">
                <div class="card-body p-3 p-md-4">
                    <h6 class="text-uppercase small text-muted mb-2">Need immediate help?</h6>
                    <p class="small mb-3">
                        Check our quick start guide or see common fixes in our help center.
                    </p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('guest.about') ?? '#' }}" class="btn btn-light border">Quick Start Guide</a>
                        <a href="{{ route('guest.contact') ?? '#' }}" class="btn btn-light border">Help Center</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Minimal JS to connect "Import file" button to the hidden file input --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const pickBtn = document.getElementById('pickFileBtn');
        const fileInput = document.getElementById('attachment');
        const fileBox  = document.getElementById('fileNameBox');

        if (pickBtn && fileInput && fileBox) {
            pickBtn.addEventListener('click', () => fileInput.click());
            fileInput.addEventListener('change', () => {
                fileBox.value = fileInput.files?.length ? fileInput.files[0].name : 'No file selected';
            });
        }

        // Simple Bootstrap validation styling
        const form = document.querySelector('form.needs-validation');
        if (form) {
            form.addEventListener('submit', function (e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        }
    });
</script>
@endsection
