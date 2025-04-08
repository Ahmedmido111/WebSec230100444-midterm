@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h2>Products</h2>
        </div>
        @if(auth()->user()->hasRole('employee'))
        <div class="col text-end">
            <a href="{{ route('products.create') }}" class="btn btn-primary">Add New Product</a>
        </div>
        @endif
    </div>

    <div class="row">
        @foreach($products as $product)
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p class="card-text">
                        <strong>Code:</strong> {{ $product->code }}<br>
                        <strong>Model:</strong> {{ $product->model }}<br>
                        <strong>Price:</strong> ${{ number_format($product->price, 2) }}<br>
                        <strong>Stock:</strong> {{ $product->stock }} units<br>
                        <strong>Description:</strong> {{ $product->description }}
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        @if(auth()->user()->hasRole('employee'))
                            <div>
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-primary">Edit</a>
                                <form action="{{ route('products.delete', $product) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                                </form>
                            </div>
                        @else
                            @if($product->stock > 0)
                                <form action="{{ route('purchases.store', $product) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success">Purchase</button>
                                </form>
                            @else
                                <button class="btn btn-secondary" disabled>Out of Stock</button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection 