@extends('layouts.creator')
@section('content')
<div class="container py-4">
  <div class="alert alert-warning">Checkout canceled.</div>
  <a class="btn btn-secondary" href="{{ url('/' . $tenant->id) }}">Back to creator page</a>
</div>
@endsection
