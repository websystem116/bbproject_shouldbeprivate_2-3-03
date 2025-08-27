@extends('layouts.app')
@section('content')
@push('css')
<link href="{{ asset('css/bootstrap-datepicker3.css') }}" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
<link href="{{ asset('css/sales_index.css') }}" rel="stylesheet">
@endpush
@push('scripts')
<script type="text/javascript" src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap-datepicker.ja.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/i18n/ja.js"></script>
<script>
	$(function() {
                $('.select_search').select2({
                    language: "ja",
                    width: '300px'
                });
                $('.select_search_grade').select2({
                    language: "ja",
                    width: '80px'
                });
                $('.select_search_2').select2({
                    language: "ja",
                    width: '400px'
                });

                // click event　id=search
                $('#search').on('click', function() {

                    // 検索項目が空の場合は、submitしない
                    if ($('input[name="last_name"]').val()) {
                        return true;
                    }
                    if ($('input[name="first_name"]').val()) {
                        return true;
                    }

                    if ($('select[name="school_year_start"]').val()) {
                        return true;
                    }

                    if ($('select[name="school_year_end"]').val()) {
                        return true;
                    }
                    if ($('select[name="school_building"]').val()) {
                        return true;
                    }
                    if ($('select[name="school"]').val()) {
                        return true;
                    }
                    if ($('select[name="product"]').val()) {
                        return true;
                    }
                    if ($('select[name="grade"]').val()) {
                        return true;
                    }
                    if ($('select[name="discount"]').val()) {
                        return true;
                    }

                    // this is checkbox name = brothers_flg
                    if ($('input[name="brothers_flg"]').prop('checked')) {
                        return true;
                    }

                    if ($('input[name="ot_enrolled_flg"]').prop('checked')) {
                        return true;
                    }

                    alert('検索項目を入力してください。');
                    return false;

                });
            });
					$(function() {
						// monthPick
						var currentTime = new Date();
						var year = currentTime.getFullYear();
						var year2 = parseInt(year) + 10;

						$(".monthPick").datepicker({
						autoclose: true,
						language: 'ja',
						clearBtn: true,
						format: "yyyy-mm",
						minViewMode: 1,
						maxViewMode: 2
						});
					});

            function checkAll() {
                if ($('input[name="check[]"]:checked').length == 0) {
                    $('input[name="check[]"]').prop('checked', true);
                } else {
                    $('input[name="check[]"]').prop('checked', false);
                }
            }

            function getChecked() {
                var checked = [];
                $('input[name="check[]"]:checked').each(function() {
                    checked.push($(this).val());
                });

                // validations
                var error_message = [];

                // 1件以上選択されているかチェック
                if (checked.length == 0) {
                    error_message.push('1件以上チェックしてください。');
                }

                // 講座が選択されているかチェック
                if ($('select[name="selected_product"]').val() == 0) {
                    error_message.push('商品を選択してください。');
                }

                // 売上年月が選択されているかチェック
                if ($('input[name="sale_month_date"]').val() == '') {
                    error_message.push('売上月を選択してください。');
                }

                // エラーメッセージがある場合は、アラートを表示して、submitしない
                if (error_message.length > 0) {
                    alert(error_message.join(' '));
                    return false;
                }


                // formを取得　id = bulk_create
                var form = document.getElementById('bulk_store');

                // もしinput.name='checked[]'が存在していたら、削除する
                if (document.getElementsByName('checked[]').length > 0) {
                    var inputs = document.getElementsByName('checked[]');
                    for (var i = 0; i < inputs.length; i++) {
                        form.removeChild(inputs[i]);
                    }
                }

                // formに配列を追加 foreachで回す
                checked.forEach(function(value) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'checked[]';
                    input.value = value;
                    form.appendChild(input);
                });

                form.submit();

                return checked;
            }

            function getChecked_for_delete() {
                var checked = [];
                $('input[name="check[]"]:checked').each(function() {
                    checked.push($(this).val());
                });

                // validations
                var error_message = [];

                // 1件以上選択されているかチェック
                if (checked.length == 0) {
                    error_message.push('1件以上チェックしてください。');
                }

                // 講座が選択されているかチェック
                if ($('select[name="selected_product_for_delete"]').val() == 0) {
                    error_message.push('商品を選択してください。');
                }

                // 売上年月が選択されているかチェック
                if ($('input[name="sale_month_date_for_delete"]').val() == '') {
                    error_message.push('売上月を選択してください。');
                }

                // エラーメッセージがある場合は、アラートを表示して、submitしない
                if (error_message.length > 0) {
                    alert(error_message.join(' '));
                    return false;
                }


                // formを取得
                var form = document.getElementById('bulk_delete');

                // もしinput.name='checked[]'が存在していたら、削除する
                if (document.getElementsByName('checked[]').length > 0) {
                    var inputs = document.getElementsByName('checked[]');
                    for (var i = 0; i < inputs.length; i++) {
                        form.removeChild(inputs[i]);
                    }
                }

                // formに配列を追加 foreachで回す
                checked.forEach(function(value) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'checked[]';
                    input.value = value;
                    form.appendChild(input);
                });

                form.submit();

                return checked;
            }
</script>
@endpush
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">売上一覧</div>
				<div class="panel-body">

					{{ Form::model($student_search, ['route' => 'sales.index', 'method' => 'GET', 'class' => 'form-horizontal']) }}
					<!-- search value -->
					{{ Form::hidden('search', '1') }}
					<div class="container">
						<div class="row col-xs-11">
							<div class="panel-group" id="sampleAccordion">
								<div class="panel panel-default">
									<div class="panel-heading">
										<h3 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#sampleAccordion"
                                                    href="#sampleAccordionCollapse1">
                                                    ▽検索条件
                                                </a>
                                            </h3>
									</div>
									<div id="sampleAccordionCollapse1" class="panel-collapse collapse in">
										<div class="panel-body">
											<div class="form-group">
												<div class="row">
													<div class="col-xs-2 text-right">
														{{ Form::label('name', '生徒氏名', ['class' => 'control-label']) }}
													</div>
													<div class="col-xs-4">
														{{ Form::text('last_name', null, ['placeholder' => '姓', 'class' => 'form-control form-name']) }}
													</div>
													<div class="col-xs-4">
														{{ Form::text('first_name', null, ['placeholder' => '名', 'class' => 'form-control form-name']) }}
													</div>
												</div>
											</div>
											<br>
											<div class="form-group">
												<div class="row">
													<div class="col-xs-2 text-right">
														{{ Form::label('school_building', '学年', ['class' => 'control-label']) }}
													</div>
													<div class="col-xs-3">
														{{ Form::select('school_year_start', config('const.school_year'), null, ['placeholder' => '選択', 'class' => 'select_search_grade']) }}
														〜
														{{ Form::select('school_year_end', config('const.school_year'), null, ['placeholder' => '選択', 'class' => 'select_search_grade']) }}
													</div>
													<div class="col-xs-2 text-right">
														{{ Form::label('school_building', '校舎', ['class' => 'control-label']) }}
													</div>
													<div class="col-xs-3">
														{{ Form::select('school_building', $school_buildings, null, ['placeholder' => '選択してください', 'class' => 'select_search']) }}
													</div>

												</div>
											</div>
											<br>
											<div class="form-group">
												<div class="row">
													<div class="col-xs-2 text-right">
														{{ Form::label(' school', '学校', ['class' => 'control-label']) }}
													</div>
													<div class="col-xs-3">
														{{ Form::select('school', $schools, null, ['placeholder' => '選択してください', 'class' => 'select_search']) }}
													</div>
													<div class="col-xs-2 text-right">
														{{ Form::label('product', '商品', ['class' => 'control-label']) }}
													</div>
													<div class="col-xs-3">
														{{ Form::select('product', $products, null, ['placeholder' => '選択してください', 'class' => 'form-control select_search']) }}
													</div>
												</div>
											</div>
											<div class="form-group">
												<div class="row">
													<div class="col-xs-2 text-right">
														{{ Form::label('discount', '割引', ['class' => 'control-label']) }}
													</div>
													<div class="col-xs-3">
														{{ Form::select('discount', $discounts, null, ['placeholder' => '選択してください', 'class' => 'select_search']) }}
													</div>
													<div class="col-xs-2 text-right">
														{{ Form::checkbox('brothers_flg', '1', null, ['class' => 'custom-control-input ', 'id' => 'brothers_flg1']) }}
														{{ Form::label('brothers_flg1', '兄弟姉妹', ['class' => 'custom-control-label']) }}
													</div>
													<div class="col-xs-2">
														{{ Form::checkbox('not_enrolled_flg', '1', null, ['class' => 'custom-control-input', 'id' => 'not_enrolled_flg1']) }}
														{{ Form::label('not_enrolled_flg1', '休卒退塾者', ['class' => 'custom-control-label']) }}
													</div>
												</div>
											</div>
											<div class="form-group">
												<div class="row">
													<div class="col-xs-2 text-right">
														{{ Form::label('work_month', '年月', ['class' => 'control-label']) }}
													</div>
													<div class="col-xs-2">
														{{ Form::text('work_month', null, ['placeholder' => '年月', 'class' => 'form-control form-name monthPick', 'autocomplete=off','style' => 'background-color:white']) }}
													</div>
												</div>
											</div>
											<div class="form-group">
												<div class="row">
													<div class="text-center">
														<button class="btn btn-primary" id="search">検索</button>

														<button type="button" class="btn btn-primary" name="reset" value="reset">
															<a href="{{ route('sales.index') }}" style="color: white;">リセット</a>
														</button>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							{{ Form::close() }}

							<br />
							<br />

							<div class="table-responsive">
								<table class="table table-borderless">
									<thead>
										<tr>
											<th>選択</th>
											<th>売上年月</th>
											<th>生徒No</th>
											<th>生徒氏名</th>
											<th>学年</th>
											<th>校舎名</th>
											<th>前月残高</th>
											<th>当月売上</th>
											<th>割引</th>
											<th>商品名</th>
											<th>編集</th>
                                            @if (auth()->user()->roles == 1)
											<th>削除</th>
											@endif
										</tr>
									</thead>
									<tbody>

										@if (count($sales) > 0)
										<button type="button" class="btn btn-primary" onclick="checkAll()">一括選択</button>
										<div style="margin-top:8px">
											{{ $sales_count_info['sales_total'] }} 件中
											{{ $sales_count_info['sales_first_item'] }} -
											{{ $sales_count_info['sales_last_item'] }} 件を表示
										</div>
										@endif

										@foreach ($sales as $item)
										<tr>
											{{-- 選択 --}}
											<td>
												<!-- checkbox  -->
												<input type="checkbox" name="check[]" value="{{ $item->student_no }}">
											</td>
											{{-- 売上年月 --}}
											<td>{{ $item->sale_month }}</td>
											{{-- 生徒No --}}
											<td>{{ $item->student_no ?? '' }}</td>
											{{-- 生徒氏名 --}}
											<td>{{ $item->student->surname . $item->student->name }}</td>
											{{-- 学年 --}}
											<td>{{ config('const.school_year')[$item->student->grade] }}</td>
											{{-- 校舎名 --}}
											<td>{{ $item->student->schoolbuilding->name ?? '' }}</td>
											{{-- 前月残高 --}}
											<td></td>
											{{-- 当月売上 --}}
											<td></td>
											{{-- 割引 --}}
											<td>{{ $item->student->discount->name ?? '' }}
											</td>
											{{-- 商品名 --}}
											<td>{{ $item->sales_detail[0]->product->name ?? '' }}</td>

											<td><a href="{{ url('/shinzemi/sales/' . $item->id . '/edit') }}" title="Edit sales" class="btn btn-primary btn-xs">編集</a>
											</td>
                                            @if (auth()->user()->roles == 1)
											<td>
												@if($sales_month == $item->sale_month)
												<form method="POST" action="{{ route('sales.destroy', $item->id) }}" class="form-horizontal" style="display:inline;">
                                                            {{ csrf_field() }}

                                                            {{ method_field('DELETE') }}
                                                            <button type="submit" class="btn btn-danger btn-xs" title="Delete User" onclick="return confirm('削除しますか')">
                                                                削除
                                                            </button>
                                                        </form>
												@endif
                                            </td>
											@endif
										</tr>
										@endforeach
									</tbody>
								</table>

							</div>


						</div>
					</div>
				</div>
			</div>

			<div style="margin-bottom: 64px;">
				<form method="post" action="{{ route('sales.bulk_store') }}" id="bulk_store" name="bulk_store">
					@csrf

					<div style="display: flex;margin-top: 16px;">

						<div>
							<button type="button" class="btn btn-primary" style="margin-left: 16px;margin-top:16px; " onclick="getChecked()">
								一括登録
							</button>
						</div>

						<div style="margin-left: 16px;">
							<div>
								商品：
							</div>
							<select name="selected_product" id="" class="form-control select_search_2">
								<option value="0">選択してください</option>
								@foreach ($products_select_list as $key => $value)
								<option value="{{ $key }}">{{ $value }}</option>
								@endforeach
							</select>
						</div>
						<div style="margin-left: 16px;">
							<div>
								売上年月:
							</div>

							<!-- 当月の＋1ヵ月 -->
							<input type="text" name="sale_month_date" id="date" class="form-control" readonly value="{{ $sales_month }}">
						</div>
					</div>

				</form>

				<form method="post" action="{{ route('sales.bulk_delete') }}" id="bulk_delete" name="bulk_delete">
					@csrf

					<div style="display: flex;margin-top: 16px;">

						<div>
							<button type="button" class="btn btn-danger" style="margin-left: 16px;margin-top:16px; " onclick="getChecked_for_delete()">
								一括削除
							</button>
						</div>

						<div style="margin-left: 16px;">
							<div>
								商品：
							</div>
							<select name="selected_product_for_delete" id="" class="form-control select_search_2">
								<option value="0">選択してください</option>
								@foreach ($products_select_list as $key => $value)
								<option value="{{ $key }}">{{ $value }}</option>
								@endforeach
							</select>
						</div>
						<div style="margin-left: 16px;">
							<div>
								売上年月:
							</div>

							<!-- 当月の＋1ヵ月 -->
							<input type="text" name="sale_month_date_for_delete" id="date" class="form-control" readonly value="{{ $sales_month }}">
						</div>
					</div>

				</form>

			</div>
			@endsection
