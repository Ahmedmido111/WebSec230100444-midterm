@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h2>Customer Details</h2>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Name:</strong>
                        <p>{{ $customer->name }}</p>
                    </div>
                    <div class="mb-3">
                        <strong>Email:</strong>
                        <p>{{ $customer->email }}</p>
                    </div>
                    <div class="mb-3">
                        <strong>Credit Balance:</strong>
                        <p>${{ number_format($customer->credit, 2) }}</p>
                    </div>
                    @if(auth()->user()->hasRole('employee'))
                        <div class="mb-3">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCreditModal">
                                Add Credit
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->hasRole('employee'))
    <!-- Add Credit Modal -->
    <div class="modal fade" id="addCreditModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Credit to {{ $customer->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('customers.add-credit', $customer) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" min="0" step="0.01" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Credit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection 