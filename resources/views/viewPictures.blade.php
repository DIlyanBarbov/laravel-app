@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        @foreach ($pictures as $picture)
            <div class="col-xs-4"><img width="50%" src="{{ $picture }}"></div>
            <br>
        @endforeach
    </div>
@endsection
