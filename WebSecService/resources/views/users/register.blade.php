@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-center">
  <div class="card m-4 col-sm-6">
    <div class="card-body">
      <h2 class="card-title text-center mb-4">Register</h2>
      <form action="{{ route('do_register') }}" method="post">
        @csrf
        <div class="form-group">
          @foreach($errors->all() as $error)
            <div class="alert alert-danger">
              <strong>Error!</strong> {{$error}}
            </div>
          @endforeach
        </div>
        <div class="form-group mb-3">
          <label for="name" class="form-label">Name:</label>
          <input type="text" class="form-control" placeholder="Enter your name" name="name" required>
        </div>
        <div class="form-group mb-3">
          <label for="email" class="form-label">Email:</label>
          <input type="email" class="form-control" placeholder="Enter your email" name="email" required>
        </div>
        <div class="form-group mb-3">
          <label for="password" class="form-label">Password:</label>
          <input type="password" class="form-control" placeholder="Enter your password" name="password" required>
        </div>
        <div class="form-group mb-3">
          <label for="password_confirmation" class="form-label">Password Confirmation:</label>
          <input type="password" class="form-control" placeholder="Confirm your password" name="password_confirmation" required>
        </div>
        <div class="form-group mb-3">
          <button type="submit" class="btn btn-primary w-100">Register</button>
        </div>
        <div class="text-center">
          <p>Already have an account? <a href="{{ route('login') }}">Login here</a></p>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
