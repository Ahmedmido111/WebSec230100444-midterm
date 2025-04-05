@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>{{ $product->id ? 'Edit Product' : 'Add New Product' }}</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('products_save', $product->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="code" class="form-label">Code:</label>
                            <input type="text" class="form-control" name="code" value="{{ old('code', $product->code) }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Name:</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name', $product->name) }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="model" class="form-label">Model:</label>
                            <input type="text" class="form-control" name="model" value="{{ old('model', $product->model) }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="price" class="form-label">Price:</label>
                            <input type="number" step="0.01" class="form-control" name="price" value="{{ old('price', $product->price) }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="stock" class="form-label">Stock:</label>
                            <input type="number" class="form-control" name="stock" value="{{ old('stock', $product->stock) }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Description:</label>
                            <textarea class="form-control" name="description" rows="3" required>{{ old('description', $product->description) }}</textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="photo" class="form-label">Product Photo:</label>
                            @if($product->photo)
                                <div class="mb-2">
                                    <img src="{{ $product->photo_url }}" alt="{{ $product->name }}" class="img-thumbnail" style="max-height: 200px;">
                                </div>
                            @endif
                            <input type="file" class="form-control" name="photo" accept="image/*">
                            @if($product->photo)
                                <div class="form-check mt-2">
                                    <input type="checkbox" class="form-check-input" name="remove_photo" id="remove_photo">
                                    <label class="form-check-label" for="remove_photo">Remove current photo</label>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Save Product</button>
                            <a href="{{ route('products_list') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
