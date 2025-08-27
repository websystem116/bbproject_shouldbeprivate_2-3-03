@extends("layouts.app")
@push('css')
<style>
    .inline-flex {
        display: flex;
        flex-direction:row;
        align-items: center;
        justify-content:flex-start;
        gap: 10px;
    }
    .curriculum_list{
        margin:0;
        padding:0;
        width: 100%;
    }
    .curriculum_list li{
        list-style: none;
        display:flex;
        flex-direction:row;
        align-items:center;
        justify-content:flex-start;
        margin-bottom:10px;
    }
    .curriculum_list li input[type=text]{
        width: calc(100% - 100px);
        margin-right:10px;
    }
</style>
@endpush
@push('scripts')
<script>
    $(document).on('click', '.add_item', function() {
        $('.curriculum_list').append('<li>\
                                        <input type="text" name="curriculum_name[]" class="form-control" placeholder="例：数学">\
                                        <button type="button" class="btn btn-danger btn-xs delete_item" title="Delete Item">\
                                            <span class="glyphicon glyphicon-trash"></span>\
                                                削除\
                                        </button>\
                                    </li>');
    });

    $(document).on('click', '.delete_item', function() {
        if(confirm("削除しますか")){
            $(this).parent().remove();
        }
    });
    

</script>
@endpush
@section("content")
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">教科マスター編集 #{{ $curriculum->id }}</div>
                <div class="panel-body">

                    <a href="{{ $url_for_back }}" title="Back">
                        <button class="btn btn-warning btn-xs">戻る</button>
                    </a>

                    <br />
                    <br />

                    @if ($errors->any())
                    <ul class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif

                    <form method="POST" action="{{route('curriculum.update',$curriculum->id)}}" class="form-horizontal">
                        {{ csrf_field() }}
                        {{ method_field("PUT") }}

                        <div class="form-group">
                            <label for="from_grade" class="col-md-3 control-label">
                                対象学年（共通）:
                                <span class="text-danger">※</span>
                            </label>
                            <div class="col-md-8 inline-flex">
                                {{ Form::select('from_grade', config('const.grade_type'),$curriculum->from_grade ?? null,['placeholder' => '開始学年', 'class' => 'form-control']) }}
                                〜
                                {{ Form::select('to_grade', config('const.grade_type'),$curriculum->to_grade ?? null,['placeholder' => '終了学年', 'class' => 'form-control']) }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">
                                教科名:
                            </label>
                            <div class="col-md-8">
                                <ul class="curriculum_list">
                                    @foreach($curriculum_names as $item)
                                    <li>
                                        <input type="text" name="curriculum_name_{{ $item->id }}" class="form-control" placeholder="例：数学" value="{{ $item->name }}">
                                        <button type="button" class="btn btn-danger btn-xs delete_item" title="Delete Item">
                                            <span class="glyphicon glyphicon-trash"></span>
                                                削除
                                        </button>
                                    </li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn btn-default btn-sm add_item">
                                    <span class="glyphicon glyphicon-plus"></span>教科名を追加
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-offset-4 col-md-4">
                                <input class="btn btn-primary" type="submit" value="更新">
                            </div>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection