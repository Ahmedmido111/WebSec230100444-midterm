@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Purchase History</div>

                <div class="card-body">
                    @if($purchases->isEmpty())
                        <p>No purchases found.</p>
                    @else
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchases as $purchase)
                                    <tr>
                                        <td>{{ $purchase->product->name }}</td>
                                        <td>${{ number_format($purchase->price_at_purchase, 2) }}</td>
                                        <td>{{ $purchase->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 