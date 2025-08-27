@extends('layouts.app')
@section('content')
    @push('css')
        <link href="{{ asset('css/home.css') }}" rel="stylesheet">
    @endpush
    @push('scripts')
    @endpush

    <div class="card-group card_fild">
        <div class="card">
            <div class="card-row col-sm-12 text-center">
                <div class="card col-sm-2 shadow-sm">
                    <div class="card-header">
                        <h4 class="my-0 font-weight-normal">入退塾等の手続き</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mt-3 mb-4">
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('student.index') }}'">
                                    生徒情報登録
                                </button>
                            </li>
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary">
                                    体験
                                </button>
                            </li>
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary"
                                    onclick="location.href='{{ route('application.admission_index') }}'">
                                    入会
                                </button>
                            </li>
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary">
                                    コース変更
                                </button>
                            </li>
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary">
                                    転籍
                                </button>
                            </li>
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary">
                                    休塾
                                </button>
                            </li>
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary">
                                    退塾
                                </button>
                            </li>
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary">
                                    復塾
                                </button>
                            </li>
                            <li>
                                <button type="button" class="btn btn-lg btn-block btn-primary">
                                    講習会
                                </button>
                            </li>

                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
