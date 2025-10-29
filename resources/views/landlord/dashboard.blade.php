@extends('layouts.landlord')

@section('content')
<style>
    :root{
        --brand-primary: {{ e($branding['primary_color'] ?? '#6C2BD9') }};
        --brand-accent:  {{ e($branding['accent_color']  ?? '#F59E0B') }};
        --brand-bg:      {{ e($branding['bg_color']      ?? '#0F172A0D') }};
        --brand-text:    {{ e($branding['text_color']    ?? '#0F172A') }};
    }
    .dash-hero {
        background: linear-gradient(135deg, var(--brand-bg), #ffffff);
        border-radius: 1rem;
    }
    .kpi-card {
        border-radius: .9rem;
        transition: transform .12s ease, box-shadow .12s ease;
    }
    .kpi-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(17,24,39,.08); }
    .kpi-icon {
        width: 44px; height: 44px; display: grid; place-items: center;
        border-radius: .75rem; background: var(--brand-primary); color: #fff;
    }
    .btn-ghost {
        background: #fff; border: 1px solid #e5e7eb;
    }
    .stat-bar { height: 8px; background: #e5e7eb; border-radius: 999px; overflow: hidden; }
    .stat-fill { height: 100%; background: var(--brand-primary); }
    .table thead th { white-space: nowrap; }
</style>

<div class="container py-4 py-md-5">

    <!-- Hero -->
    <div class="dash-hero p-4 p-md-5 mb-4 shadow-sm">
        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
            <div>
                <h1 class="h3 h1-md fw-bold mb-1">Welcome back{{ isset($user) ? ', '.$user->name : '' }} 👋</h1>
                <p class="text-muted mb-0">Here’s a snapshot of what’s happening across your platform.</p>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('landlord.subscriptions.index') }}" class="btn btn-primary">
                    View Subscriptions
                </a>
                <a href="{{ route('tenants.index') }}" class="btn btn-ghost">
                    Manage Tenants
                </a>
                <a href="{{ route('tenants.create') }}" class="btn btn-outline-primary">
                    + Add Tenant
                </a>
            </div>
        </div>
    </div>

    <!-- KPIs -->
    <div class="row g-3 g-md-4">
        <div class="col-6 col-md-3">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="kpi-icon">📈</div>
                    <div>
                        <div class="text-muted small">Monthly Revenue</div>
                        <div class="h5 mb-0">${{ number_format($metrics['mrr'] ?? 0) }}</div>
                        <div class="small text-success mt-1">+{{ $metrics['mrr_change'] ?? 0 }}% vs last month</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="kpi-icon">👥</div>
                    <div>
                        <div class="text-muted small">Active Subscribers</div>
                        <div class="h5 mb-0">{{ $metrics['active_subscribers'] ?? 0 }}</div>
                        <div class="small text-success mt-1">+{{ $metrics['subs_change'] ?? 0 }} this week</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="kpi-icon">🏷️</div>
                    <div>
                        <div class="text-muted small">Creators Onboarded</div>
                        <div class="h5 mb-0">{{ $metrics['creators'] ?? 0 }}</div>
                        <div class="small text-muted mt-1">{{ $metrics['creators_pending'] ?? 0 }} pending</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="kpi-icon">💳</div>
                    <div>
                        <div class="text-muted small">Payouts Ready</div>
                        <div class="h5 mb-0">${{ number_format($metrics['payouts_ready'] ?? 0) }}</div>
                        <div class="small text-muted mt-1">Next run: {{ $metrics['next_payout_date'] ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Health -->
    <div class="row g-3 g-md-4 mt-1">
        <div class="col-12 col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <strong>Quick Actions</strong>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="fw-semibold">Onboard Creator</div>
                                        <div class="small text-muted">Start Stripe Connect onboarding.</div>
                                    </div>
                                    <span>⚡</span>
                                </div>
                                <a href="{{ route('tenants.index') }}" class="btn btn-sm btn-primary mt-3 w-100">Get Started</a>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="fw-semibold">Create Price</div>
                                        <div class="small text-muted">Add/Update a creator’s monthly plan.</div>
                                    </div>
                                    <span>🏷️</span>
                                </div>
                                <a href="{{ route('landlord.subscriptions.index') }}" class="btn btn-sm btn-outline-primary mt-3 w-100">Manage Prices</a>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="border rounded p-3">
                                <div class="fw-semibold mb-2">Platform Health</div>
                                <div class="d-flex flex-column gap-2">
                                    <div>
                                        <div class="small d-flex justify-content-between">
                                            <span>API Uptime</span><span>{{ $health['uptime'] ?? '99.9%' }}</span>
                                        </div>
                                        <div class="stat-bar"><div class="stat-fill" style="width: {{ $health['uptime_percent'] ?? 99 }}%"></div></div>
                                    </div>
                                    <div>
                                        <div class="small d-flex justify-content-between">
                                            <span>Checkout Success</span><span>{{ $health['checkout_success'] ?? '98%' }}</span>
                                        </div>
                                        <div class="stat-bar"><div class="stat-fill" style="width: {{ $health['checkout_success_percent'] ?? 98 }}%"></div></div>
                                    </div>
                                    <div>
                                        <div class="small d-flex justify-content-between">
                                            <span>Payouts Cleared</span><span>{{ $health['payouts_cleared'] ?? '92%' }}</span>
                                        </div>
                                        <div class="stat-bar"><div class="stat-fill" style="width: {{ $health['payouts_cleared_percent'] ?? 92 }}%"></div></div>
                                    </div>
                                </div>
                                <div class="small text-muted mt-2">Updated {{ now()->format('M j, g:i A') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-12 col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex align-items-center justify-content-between">
                    <strong>Recent Activity</strong>
                    <a href="{{ route('landlord.subscriptions.index') }}" class="small">View all</a>
                </div>
                <div class="card-body">
                    @forelse(($activity ?? []) as $item)
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <div class="kpi-icon" style="width:38px;height:38px;">🔔</div>
                            <div>
                                <div class="fw-semibold">{{ $item['title'] ?? 'Event' }}</div>
                                <div class="small text-muted">{{ $item['time'] ?? 'Just now' }}</div>
                                <div class="small">{{ $item['desc'] ?? '' }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted small">No recent activity.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Tables -->
    <div class="row g-3 g-md-4 mt-1">
        <div class="col-12 col-xl-7">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <strong>Latest Subscriptions (Landlord Scope)</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Plan</th>
                                    <th>Status</th>
                                    <th>Started</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($subscriptions ?? []) as $sub)
                                    <tr>
                                        <td>{{ $sub->user->name ?? '—' }}</td>
                                        <td>{{ $sub->stripe_price ?? '—' }}</td>
                                        <td>
                                            <span class="badge bg-{{ ($sub->status ?? $sub->stripe_status) === 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($sub->status ?? $sub->stripe_status ?? 'n/a') }}
                                            </span>
                                        </td>
                                        <td>{{ optional($sub->created_at)->format('M j, Y') }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('landlord.subscriptions.index') }}" class="btn btn-sm btn-outline-secondary">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-muted small p-3">No subscriptions yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white small text-muted">
                    Showing {{ isset($subscriptions) ? $subscriptions->count() : 0 }} records
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <strong>Creators Requiring Attention</strong>
                </div>
                <div class="card-body">
                    @forelse(($creators_needing_action ?? []) as $creator)
                        <div class="d-flex justify-content-between align-items-center border rounded p-3 mb-2">
                            <div>
                                <div class="fw-semibold">{{ $creator->display_name }}</div>
                                <div class="small text-muted">
                                    {{ $creator->stripe_account_id ? 'Stripe Connected' : 'Not Connected' }}
                                    @if(empty($creator->stripe_price_id)) · <span class="text-danger">No price set</span> @endif
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ url("/{$creator->id}/connect/onboarding") }}" class="btn btn-sm btn-primary">Onboard</a>
                                <a href="{{ route('landlord.subscriptions.index') }}" class="btn btn-sm btn-outline-secondary">Set Price</a>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted small">All caught up. 🎉</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
