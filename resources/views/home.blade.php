@extends('layouts.app')

@section('content')
    <div class="container">
        @php
            use App\Http\Controllers\UserController;
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
                        return action([UserController::class, 'edit'], ['id' => $data['id']]);
                    },
                    'view' => static function ($data){
                        return action([UserController::class, 'viewPictures']);
                    }

                ]
            ]
        ],
    ];
        echo @grid_view($gridData)
        @endphp
    </div>
@endsection
