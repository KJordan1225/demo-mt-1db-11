<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Choose a Tenant</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; margin: 2rem; }
        .wrap { max-width: 720px; margin: auto; }
        h1 { margin-bottom: 1rem; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: .75rem; margin-top: 1rem; }
        .card { border: 1px solid #e5e7eb; border-radius: .75rem; padding: .9rem; }
        .muted { color: #6b7280; font-size: .9rem; }
        .row { display: flex; gap: .5rem; margin-top: .75rem; }
        .btn { display:inline-block; padding:.5rem .75rem; border-radius:.5rem; text-decoration:none; border:1px solid #e5e7eb; }
        .btn-primary { background:#111827; color:#fff; border-color:#111827; }
        .input { width:100%; padding:.5rem .6rem; border:1px solid #e5e7eb; border-radius:.5rem; }
        .error { color: #b91c1c; margin-top: .25rem; font-size: .9rem; }
        form { margin-top: 1rem; }
        .or { text-align:center; color:#9ca3af; margin:1rem 0; }
    </style>
</head>
<body>
<div class="wrap">
    <h1>Choose a Tenant</h1>
    <p class="muted">Pick an existing tenant below, or type a tenant ID to go to its login.</p>

    {{-- Quick search/entry --}}
    <form method="POST" action="{{ route('tenant.switch') }}">
        @csrf
        <label for="tenant" class="muted">Enter tenant ID (e.g., <code>alpha</code>)</label>
        <div class="row">
            <input id="tenant" name="tenant" class="input" placeholder="alpha" value="{{ old('tenant') }}">
            <button class="btn btn-primary" type="submit">Go to login</button>
        </div>
        @error('tenant') <div class="error">{{ $message }}</div> @enderror
    </form>

    <div class="or">— or —</div>

    {{-- Cards of known tenants --}}
    <div class="grid">
        @forelse($tenants as $t)
            @php
                // Optional: show a friendly name if stored in $t->data['name']
                $label = data_get($t->data, 'name') ?: $t->id;
            @endphp
            <div class="card">
                <div><strong>{{ $label }}</strong></div>
                <div class="muted">ID: {{ $t->id }}</div>
                <div class="row">
                    {{-- These route() calls will include {tenant} param explicitly
                         since we’re on a central page (not under tenant.defaults) --}}
                    <a class="btn btn-primary"
                       href="{{ route('tenant.login', ['tenant' => $t->id]) }}">Log in</a>
                    <a class="btn"
                       href="{{ route('tenant.register', ['tenant' => $t->id]) }}">Register</a>
                    <a class="btn"
                       href="{{ route('tenant.dashboard', ['tenant' => $t->id]) }}">Dashboard</a>
                </div>
            </div>
        @empty
            <p class="muted">No tenants found. Create one in Tinker:</p>
            <pre>Tenant::firstOrCreate(['id' => 'alpha']);</pre>
        @endforelse
    </div>

    {{-- Optional: landlord shortcuts if you also have landlord auth --}}
    <hr style="margin:2rem 0; border-color:#f3f4f6;">
    <h2>Landlord</h2>
    <div class="row">
        <a class="btn btn-primary" href="{{ route('login') }}">Landlord Login</a>
        <a class="btn" href="{{ route('register') }}">Landlord Register</a>
        <a class="btn" href="{{ route('landlord.dashboard') }}">Landlord Dashboard</a>
    </div>
</div>
</body>
</html>
