@extends('layouts.app')

@section('content')
    <div class="container">
        @php
            $gridData = [
        'dataProvider' => $dataProvider,
        'useFilters' => false,
        'columnFields' => [
            'name',
            'email',
            [
                'class' => Itstructure\GridView\Columns\ActionColumn::class, // Required
                'actionTypes' => [
                    'edit' => static function ($data){
                    return action([\App\Http\Controllers\UserController::class, 'edit'], ['id' => $data['id']]);
                    }
                ]
            ]
        ],
    ];
        echo @grid_view($gridData)
        @endphp
    </div>
@endsection
