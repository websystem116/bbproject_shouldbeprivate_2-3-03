<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validate;
use DB;
use Auth;
use Session;
use App\Token;

    /**
     * mypage/phoneを開く
     *
     * @return $this
     */
class MypageController extends Controller
{
    protected function getRegisterPhone()
    {
        $user = Auth::user();

        return view('mypage.phone')
            ->with([
                'country_code' => $user->country_code,
                'phone' => $user->phone,
                'verified_phone' => $user->verified_phone
            ]);
    }

    /**
     * mypage/phoneからのpostを受け取る
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    protected function postRegisterPhone()
    {
        $user = Auth::user();

        $user->country_code = $_POST['country_code'];
        $user->phone = $_POST['phone'];
        $user->verified_phone = 0; //認証結果は一度リセットする
        $user->save();
        Auth::login($user); //ログインしているユーザー情報を更新

        $token = Token::create([
            'user_id' => $user->id
        ]);

        if ($token->sendCode())
        {
            Session::put('token_id', $token->id);

            return redirect('mypage/phone/verify')->withInput();
        }

        $token->delete(); // delete token because it can't be sent
        return redirect('mypage/phone')->with('status', __('Send SMS is failed.'));
    }

    /**
     * mypage/phone/verifyを開く
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function getVerifyPhone()
    {
        // post内容を取得
        $postdata = Session::get('_old_input');

        return view('mypage.verify', compact('postdata'));
    }

    /**
     * mypage/phone/verifyからpostを受け取る
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function postVerifyPhone()
    {
        $user = Auth::user();
        $token = Token::whereId(session('token_id'))->first();

        $v_code = $_POST['verified_code'];

        if ($v_code == $token->code)
        {
            $user->verified_phone = 1;
            $user->save();

            $token->used = 1;
            $token->save();

            return redirect('mypage')->with('status', __('Phone Number Authentication is complete.'));
        }

        return redirect('mypage/phone/verify');
    }

}
