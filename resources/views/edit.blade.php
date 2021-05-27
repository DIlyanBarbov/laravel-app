@extends('layouts.app')

@section('content')
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>{{ $message }}</strong>
        </div>
    @endif
    @if ($message = Session::get('errors'))
        <div class="alert alert-danger alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>{{ $message }}</strong>
        </div>
    @endif
    <div class="container mt-4">
        <form action="{{route('edit')}}" method="post" enctype="multipart/form-data" name="user-form">
            <label for="name">Enter new username below:</label><br>
            <input type="text" name="name" id="name">
            <button type="submit" name="submit" class="btn btn-primary">
                Edit username
            </button>
        </form>
    </div>
    <div class="container mt-4">
        <form action="{{route('edit')}}" method="post" enctype="multipart/form-data" name="user-form">
            <label for="email">Enter new email below:</label><br>
            <input type="text" name="email" id="email">
            <button type="submit" name="submit" class="btn btn-primary">
                Edit email
            </button>
        </form>
    </div>
    <div class="container mt-4">
        <form action="{{route('upload')}}" method="post" enctype="multipart/form-data" name="file-form">
            <input type="file" name="file" id="chooseFile">
            <button type="submit" name="submit" class="btn btn-primary">
                Upload Picture
            </button>
        </form>
    </div>
@endsection
