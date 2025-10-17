<!-- resources/views/auth/register-tenant.blade.php -->
<form method="POST" action="{{ url('/register/'.$tenant->id) }}">
    @csrf
    <!-- Name -->
    <input type="text" name="name" required>

    <!-- Email -->
    <input type="email" name="email" required>

    <!-- Password -->
    <input type="password" name="password" required>

    <!-- Confirm Password -->
    <input type="password" name="password_confirmation" required>

    <button type="submit">Register as Tenant</button>
</form>
