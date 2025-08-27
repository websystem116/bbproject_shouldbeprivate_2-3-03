@extends("layouts.app")
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">discount {{ $discount->id }}</div>
                    <div class="panel-body">

                        <a href="{{ url('/shinzemi/discount') }}" title="Back"><button
                                class="btn btn-warning btn-xs">Back</button></a>
                        <a href="{{ url('/shinzemi/discount') . '/' . $discount->id . '/edit' }}" title="Edit discount"><button
                                class="btn btn-primary btn-xs">Edit</button></a>
                        <form method="POST" action="/discount/{{ $discount->id }}" class="form-horizontal"
                            style="display:inline;">
                            {{ csrf_field() }}
                            {{ method_field('delete') }}
                            <button type="submit" class="btn btn-danger btn-xs" title="Delete User"
                                onclick="return confirm('Confirm delete')">
                                Delete
                            </button>
                        </form>
                        <br />
                        <br />
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th>id</th>
                                        <td>{{ $discount->id }} </td>
                                    </tr>
                                    <tr>
                                        <th>name</th>
                                        <td>{{ $discount->name }} </td>
                                    </tr>
                                    <tr>
                                        <th>name_short</th>
                                        <td>{{ $discount->name_short }} </td>
                                    </tr>
                                    <tr>
                                        <th>discount_rate_class</th>
                                        <td>{{ $discount->discount_rate_class }} </td>
                                    </tr>
                                    <tr>
                                        <th>discount_rate_personal</th>
                                        <td>{{ $discount->discount_rate_personal }} </td>
                                    </tr>
                                    <tr>
                                        <th>discount_rate_course</th>
                                        <td>{{ $discount->discount_rate_course }} </td>
                                    </tr>
                                    <tr>
                                        <th>discount_rate_join</th>
                                        <td>{{ $discount->discount_rate_join }} </td>
                                    </tr>
                                    <tr>
                                        <th>discount_rate_monthly</th>
                                        <td>{{ $discount->discount_rate_monthly }} </td>
                                    </tr>
                                    <tr>
                                        <th>discount_rate_teachingmaterial</th>
                                        <td>{{ $discount->discount_rate_teachingmaterial }} </td>
                                    </tr>
                                    <tr>
                                        <th>discount_rate_test</th>
                                        <td>{{ $discount->discount_rate_test }} </td>
                                    </tr>
                                    <tr>
                                        <th>discount_rate_certification</th>
                                        <td>{{ $discount->discount_rate_certification }} </td>
                                    </tr>
                                    <tr>
                                        <th>discount_rate_other</th>
                                        <td>{{ $discount->discount_rate_other }} </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
