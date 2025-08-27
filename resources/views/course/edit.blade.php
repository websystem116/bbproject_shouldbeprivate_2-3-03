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
    .course_type_list{
        margin:0;
        padding:0;
        width: 100%;
    }
    .course_type_list li{
        list-style: none;
        display:flex;
        flex-direction:row;
        align-items:center;
        justify-content:flex-start;
        margin-bottom:10px;
    }
    .course_type_list li input[type=text]{
        width: calc(100% - 100px);
        margin-right:10px;
    }
    .show_pulldown{
        white-space: nowrap;
        margin-right: 20px;
        margin-left: 5px;
    }
    .course_curriculum_list{
        margin:0;
        padding:0;
        width: 100%;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        padding-top:5px;
    }
    .course_curriculum_list li{
        list-style: none;
    }


</style>
@endpush
@push('scripts')
<script>
    var add_course_type_count = 0;
    $(document).on('click', '.add_item', function() {
        add_course_type_count ++;
        $('.course_type_list').append('<li>\
                                        <input type="text" name="course_type_name_'+add_course_type_count+'" class="form-control" placeholder="種別名 (例: 5科目,40分（週〇回）)">\
                                        <label class="show_pulldown">\
                                            <input type="checkbox" name="course_type_show_pulldown_'+add_course_type_count+'" class="custom-control-input" value="1">\
                                            回数のプルダウン表示\
                                        </label>\
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

    $(document).on('change', '.from_grade, .to_grade', function() {
        $.ajax({
            url: "{{ route('course.get_course_curriculums') }}",
            type: "POST",
            data: { 
                from_grade: $(".from_grade").val(),
                to_grade: $(".to_grade").val(),
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                $('#course_curriculum_list').html(response);
            },
            error: function(xhr) {
            }
        });

    });    
 
    function check_data(){
        $("#add_course_type_count").val(add_course_type_count);
        return true;
    }
    
</script>
@endpush
@section("content")
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">コースマスター編集 #{{ $course->id }}</div>
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

                    <p>コースの基本情報と、このコースで提供する教科を選択してください。</p>
                    <form method="POST" action="{{route('course.update',$course->id)}}" class="form-horizontal">
                        {{ csrf_field() }}
                        {{ method_field("PUT") }}

                        <input type="hidden" id="add_course_type_count" name="add_course_type_count" value="0">
                        <div class="form-group">
                            <label for="brand" class="col-md-3 control-label">
                                ブランド:
                                <span class="text-danger">※</span>
                            </label>
                            <div class="col-md-8">
                                {{ Form::select('brand', config('const.brand'),$course->brand ?? null,['placeholder' => '選択してください', 'class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name" class="col-md-3 control-label">
                                コース名:
                                <span class="text-danger">※</span>
                            </label>
                            <div class="col-md-8">
                                <input class="form-control" name="name" type="text" id="name" value="{{$course->name}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="from_grade" class="col-md-3 control-label">
                                対象学年（共通）:
                                <span class="text-danger">※</span>
                            </label>
                            <div class="col-md-8 inline-flex">
                                {{ Form::select('from_grade', config('const.grade_type'),$course->from_grade ?? null,['placeholder' => '開始学年', 'class' => 'form-control from_grade']) }}
                                〜
                                {{ Form::select('to_grade', config('const.grade_type'),$course->to_grade ?? null,['placeholder' => '終了学年', 'class' => 'form-control to_grade']) }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="course_type_list" class="col-md-3 control-label">
                                コース種別:
                            </label>
                            <div class="col-md-8">
                                <ul class="course_type_list" id="course_type_list">
                                @if($course->course_type)
                                    @foreach($course->course_type as $key => $course_type)
                                    <li>
                                        <input type="text" name="old_course_type_name_{{ $course_type->id }}" class="form-control" placeholder="種別名 (例: 5科目,40分（週〇回）)" value="{{ $course_type->type_name }}">
                                        <label class="show_pulldown">
                                            <input type="checkbox" name="course_type_show_pulldown_{{ $course_type->id }}" class="custom-control-input" @if($course_type->show_pulldown == '1') checked @endif value="1">
                                            回数のプルダウン表示
                                        </label>
                                        <button type="button" class="btn btn-danger btn-xs delete_item" title="Delete Item">
                                            <span class="glyphicon glyphicon-trash"></span>
                                                削除
                                        </button>
                                    </li>
                                    @endforeach
                                @endif
                                </ul>
                                <button type="button" class="btn btn-default btn-sm add_item">
                                    <span class="glyphicon glyphicon-plus"></span>コース種別を追加
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="course_curriculum_list" class="col-md-3 control-label">
                                提供教科:
                            </label>
                            <div class="col-md-8 inline-flex">
                                <ul class="course_curriculum_list" id="course_curriculum_list">
                                @if($curriculums)
                                    @foreach($curriculums as $key => $curriculum)
                                    @php
                                        $checked = '';
                                        foreach($course->course_curriculum as $course_curriculum){
                                            if($course_curriculum->curriculum_id == $curriculum->id){
                                                $checked = 'checked';
                                                break;
                                            }
                                        }
                                    @endphp
                                    <li>
                                        <label>
                                            <input type="checkbox" name="course_curriculum[]" class="custom-control-input" {{ $checked }} value="{{ $curriculum->id }}">
                                            {{ $curriculum->name }}
                                        </label>
                                    </li>
                                    @endforeach
                                @endif
                                </ul>    
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-offset-4 col-md-4">
                                <input class="btn btn-primary" type="submit" value="更新" onclick="return check_data();">
                            </div>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection