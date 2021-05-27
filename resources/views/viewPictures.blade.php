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
        @foreach ($pictures as $picture)
            <div class="col-xs-4"><img width="50%" src="{{ $picture['url'] }}"></div>
            <a href="{{route('deletePicture', ['id' => $picture['id']])}}" onclick="event.preventDefault();
                                                     document.getElementById('delete-picture-form').submit();">Delete Picture</a>
            <form id="delete-picture-form" action="{{ route('deletePicture',['id' => $picture['id']]) }}" method="POST" class="d-none">
                @csrf
            </form>
            <br>
        @endforeach
    </div>
@endsection
