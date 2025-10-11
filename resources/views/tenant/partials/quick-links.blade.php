<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h2 class="h5 fw-semibold mb-3">Quick Links</h2>

        <ul class="list-unstyled mb-0">
            <li class="mb-2">
                <a class="brand-link text-decoration-none"
                   href="{{ route('tenant.dashboard', ['tenant' => $branding['slug']]) }}">
                    Dashboard
                </a>
            </li>

            {{-- Example tenant-scoped links (add/remove as needed) --}}
            @auth
                <li class="mb-2">
                    <a class="text-decoration-none"
                       href="{{ route('profile.edit', ['tenant' => $branding['slug']]) }}">
                        Edit Profile
                    </a>
                </li>
            @endauth

            <li class="mb-2">
                <a class="text-decoration-none"
                   href="{{ route('tenant.dashboard', ['tenant' => $branding['slug']]) }}#branding">
                    Branding Settings
                </a>
            </li>

            {{-- Optional: tenant logout (POST) --}}
            @auth
                <li>
                    <form method="POST" action="{{ route('logout', ['tenant' => $branding['slug']]) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 align-baseline text-decoration-none">
                            Log out
                        </button>
                    </form>
                </li>
            @endauth
        </ul>
    </div>
</div>
