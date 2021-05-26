@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <form action="{{route('upload')}}" method="post" enctype="multipart/form-data" name="file-form">
            <input type="file" name="file" id="chooseFile">
            <button type="submit" name="submit" class="btn btn-primary">
                Upload Picture
            </button>
        </form>
    </div>
@endsection
