    @extends("layouts.app")
    @section("content")

    <div class="container">

        <table class="table">
            <thead>
                <tr>
                    <th>マスター管理</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <a href="{{route('school.index')}}">学校</a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{route('bank.index')}}">銀行</a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{route('discount.index')}}">割引</a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{route('school_building.index')}}">校舎</a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{route('branch_bank.index')}}">銀行支店</a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{route('product.index')}}">講座</a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{route('manage_target.index')}}">目標管理</a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{route('authoritie.index')}}">権限</a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{route('subject_teacher.index')}}">科目別担当講師</a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{route('classification.index')}}">区分</a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{route('questionnaire_rule.index')}}">アンケートルール</a>
                    </td>
                </tr>

                <tr>
                    <td>
                        <a href="{{route('questionnaire_score.index')}}">アンケート点数</a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{route('highschool_course.index')}}">高校コース</a>
                    </td>
                </tr>
            </tbody>
        </table>
            
    </div>
@endsection
