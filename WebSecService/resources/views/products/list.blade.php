@extends('layouts.app')
@section('title', 'Test Page')
@section('content')
<div class="row mt-2">
    <div class="col col-10">
        <h1>Products</h1>
    </div>
    <div class="col col-2">
        @can('edit_products')
        <a href="{{route('products_edit')}}" class="btn btn-success form-control">Add Product</a>
        @endcan
    </div>
</div>

<form>
    <div class="row mb-4">
        <div class="col col-sm-2">
            <input name="keywords" type="text" class="form-control" placeholder="Search Keywords" value="{{ request()->keywords }}" />
        </div>
        <div class="col col-sm-2">
            <input name="min_price" type="number" step="0.01" class="form-control" placeholder="Min Price" value="{{ request()->min_price }}"/>
        </div>
        <div class="col col-sm-2">
            <input name="max_price" type="number" step="0.01" class="form-control" placeholder="Max Price" value="{{ request()->max_price }}"/>
        </div>
        <div class="col col-sm-2">
            <select name="order_by" class="form-select">
                <option value="" {{ request()->order_by==""?"selected":"" }} disabled>Order By</option>
                <option value="name" {{ request()->order_by=="name"?"selected":"" }}>Name</option>
                <option value="price" {{ request()->order_by=="price"?"selected":"" }}>Price</option>
            </select>
        </div>
        <div class="col col-sm-2">
            <select name="order_direction" class="form-select">
                <option value="" {{ request()->order_direction==""?"selected":"" }} disabled>Order Direction</option>
                <option value="ASC" {{ request()->order_direction=="ASC"?"selected":"" }}>ASC</option>
                <option value="DESC" {{ request()->order_direction=="DESC"?"selected":"" }}>DESC</option>
            </select>
        </div>
        <div class="col col-sm-1">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
        <div class="col col-sm-1">
            <button type="reset" class="btn btn-danger">Reset</button>
        </div>
    </div>
</form>

<div class="row">
    @foreach($products as $product)
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <img src="{{ $product->photo_url }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                    <h4 class="card-title">{{$product->name}}</h4>
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">Model:</th>
                            <td>{{$product->model}}</td>
                        </tr>
                        <tr>
                            <th>Code:</th>
                            <td>{{$product->code}}</td>
                        </tr>
                        <tr>
                            <th>Price:</th>
                            <td>${{ number_format($product->price, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Stock:</th>
                            <td>{{ $product->stock }} units</td>
                        </tr>
                    </table>
                    <p class="card-text">{{$product->description}}</p>
                    
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        @if(auth()->user()->hasRole('customer'))
                            @if($product->stock > 0)
                                <form action="{{ route('purchases.store', $product) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success">Purchase</button>
                                </form>
                            @else
                                <button class="btn btn-secondary" disabled>Out of Stock</button>
                            @endif
                        @endif
                        
                        @can('edit_products')
                            <div>
                                <a href="{{route('products_edit', $product->id)}}" class="btn btn-primary">Edit</a>
                                <a href="{{route('products_delete', $product->id)}}" class="btn btn-danger" 
                                   onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if($products->isEmpty())
    <div class="alert alert-info">
        No products found.
    </div>
@endif
@endsection