@extends('layouts.app')

@section('content')
<div class="container mt-4">
<form action="{{route('/upload')}}" method="post" enctype="multipart/form-data">
    <div class="custom-file">
        <input type="file" name="file" class="custom-file-input" id="chooseFile">
        <label class="custom-file-label" for="chooseFile">Select file</label>
    </div>

    <button type="submit" name="submit" class="btn btn-primary mt-4">
        Upload Profile Picture
    </button>
</form>
</div>
@endsection
