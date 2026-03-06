@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create New User</h2>

    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

                <div class="mb-3">
            <label>Username</label>
            <input type="Username" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="Email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-control" required>
                <option value="social_worker">Social Worker</option>
                <option value="carer">Carer</option>
                <option value="young_person">Young Person</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Password (optional)</label>
            <input type="text" name="password" class="form-control" placeholder="Leave blank for auto-generated">
        </div>

        <button type="submit" class="btn btn-success">Create User</button>
    </form>
</div>
@endsection
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
        </ul>
    </div>
@endif
