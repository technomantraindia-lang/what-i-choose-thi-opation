@extends('admin.layouts.app')
@section('title', 'Edit Page')
@section('content')
<h2 class="mb-4">Edit Page</h2>
<div class="card"><div class="card-body">
    <form action="{{ route('admin.pages.update', $page) }}" method="POST">@csrf @method('PUT')
        @include('admin.pages._form')
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div></div>
@endsection
