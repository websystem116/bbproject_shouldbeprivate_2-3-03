
        @extends("layouts.app")
        @section("content")
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading"></div>
                            <div class="panel-body">
                            <br/>
                            <br/>
                            <div class="table-responsive">
                                <table class="table ">
                                    <tbody>
                                        <tr>
                                            <th>ユーザー名</th>
                                            @foreach($job_descriptions as $job_description)
                                                <th>{{ $job_description->name }}（明細表記）</th>
                                                <th>{{ $job_description->name }}（給与計算時）</th>
                                            @endforeach
                                            
                                        
                                        </tr>

                                        @foreach($users as $user )
										<tr>
                                            <td>{{ $user->full_name }}</td>
                                            @foreach($job_descriptions as $job_description)
                                            <td>{{$salary_details_datas[$user->id][1][$job_description->id]['working_time']?? 0}} </td>
                                            <td>{{$salary_details_datas[$user->id][2][$job_description->id]['working_time']?? 0}} </td>
                                            @endforeach
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endsection
    