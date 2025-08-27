@extends("layouts.app")
@section("content")
@push('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
@endpush
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/i18n/ja.js"></script>
<script src="{{ asset('/js/access_user.js') }}"></script>
<script>
	$(function() {
    $('.select_search1').select2({
        language: "ja",
        width: '100px'
    });
    $('.select_search2').select2({
        language: "ja",
        width: '150px'
    });
    $('.select_search3').select2({
        language: "ja",
        width: '400px'
    });
});

$(function() {
    $(".reset").on('click', function() {
        console.log('リセット');
        window.location.href = "/shinzemi/access_user"; //URLリセットする
    });
    $('.check_all').on("click", function() {
        if ($('input[name="student_check[]"]:checked').length == 0) {
            $('input[name="student_check[]"]').prop('checked', true);
        } else {
            $('input[name="student_check[]"]').prop('checked', false);
        }
    });
});

</script>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">入退室生徒リスト</div>
				<div class="panel-body">
					{{ Form::model($student_search, ['route' => 'access_user.index', 'method' => 'GET', 'class' => 'form-horizontal']) }}
					<div class="">
						<div class="panel-group" id="sampleAccordion">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h3 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#sampleAccordion" href="#sampleAccordionCollapse1">▽検索条件</a>
                                    </h3>
								</div>
								<div id="sampleAccordionCollapse1" class="panel-collapse collapse in">
									<div class="panel-body">
										<div class="form-group row">
											<div class="col-xs-1 text-left">
												{{ Form::label('number', '管理No', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{ Form::number('id_start',$student_search['id_start'] ?? null, ['placeholder' => '管理No', 'class' => 'form-control form-name']) }}
											</div>
											<div class="col-xs-1 text-center">
												{{ Form::label('wave', '～', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{ Form::number('id_end', $student_search['id_end'] ?? null, ['placeholder' => '管理No', 'class' => 'form-control form-name']) }}
											</div>
										</div>
										<div class="form-group row">
											<div class="col-xs-1 text-left">
												{{ Form::label('name', '生徒氏名', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{ Form::text('surname',$student_search['surname'] ?? null, ['placeholder' => '姓', 'class' => 'form-control form-name']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{ Form::text('name', $student_search['name'] ?? null, ['placeholder' => '名', 'class' => 'form-control form-name']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{ Form::text('surname_kana', $student_search['surname_kana'] ?? null, ['placeholder' => '姓カナ', 'class' => 'form-control form-name hira_change']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{ Form::text('name_kana', $student_search['name_kana'] ?? null, ['placeholder' => '名カナ', 'class' => 'form-control form-name hira_change']) }}
											</div>
										</div>
										<div class="form-group row">
											<div class="col-xs-1 text-left">
												{{ Form::label('school_building', '校舎名', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{ Form::select('school_building_id',$schoolbuildings_select_list,$student_search['school_building_id'] ?? null,['placeholder' => '選択してください','class' => 'form-control select_search3']) }}
											</div>
										</div>

										<div class="form-group">
											<div class="row">
												<div class="text-center">
													{{ Form::submit('検索', ['name' => 'search', 'class' => 'btn btn-primary']) }}
													{{ Form::reset('リセット', ['class' => 'btn btn-primary reset']) }}
													{{-- {{ Form::submit('CSV出力', ['name' => 'output','class' => 'btn btn-primary']) }} --}}
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						{{ Form::close() }}
					</div>
				</div>
				{{ Form::model($student_search, ['route' => 'access_user.index', 'method' => 'POST', 'class' => 'form-horizontal']) }}
				<div class="panel-body">
					<div>{{$student->count() }}件を表示</div>
					{{-- <div>{{ $student->total() }} 件中 {{ $student->firstItem() }} - {{ $student->lastItem() }} 件を表示
					</div> --}}
					<a href="{{ route('access_user.create') }}" class="btn btn-success btn-sm" title="Add New student">
						新規追加
					</a>
					<br>

					<div class="table-responsive">
						<table class="table table-striped table-hover">
							<thead>
								<tr>
									<th style="width: 10%">管理No</th>
									<th style="width: 10%">生徒名</th>
									<th style="width: 10%">校舎名</th>
									<th style="width: 5%">編集</th>
									<th style="width: 5%">削除</th>
								</tr>
							</thead>
							<tbody>
								@foreach($student as $key => $item)
								<tr>
									<td>{{ $item->id}} </td>
									<td>{{ $item->surname}} {{ $item->name}}</td>
									<td>{{ $item->schoolbuilding->name ?? ""}}</td>
									<td>
										<a href="{{ url('/shinzemi/access_user/' . $item->id . '/edit') }}" title="Edit bank" class="btn btn-primary btn-xs">編集</a>
									</td>
									<td>
										<form action="{{ url('/shinzemi/access_user/' . $item->id) }}" method="POST" style="display:inline;">
											@csrf
											@method('DELETE')
											@foreach(request()->all() as $key => $value)
												@if(!in_array($key, ['_token', '_method'])) <!-- 不要なフィールドを除外 -->
													<input type="hidden" name="{{ $key }}" value="{{ $value }}">
												@endif
											@endforeach
											<button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('本当に削除してもよろしいですか？');">
												削除
											</button>
										</form>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
						{{-- <div class="pagination-wrapper"> {{ $student->appends(request()->input())->links() }}
						</div> --}}
					</div>
				</div>
				{{ Form::close() }}
			</div>
		</div>
	</div>
</div>
@endsection
