<?php

namespace App\Http\Controllers;

use App\Card;
use App\AccessUser;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;

class CardController extends Controller
{
    /**
     * QRカード画面を表示する
     *
     * @param Request $request
     * @return void
     */
    public function printPreview()
    {
        // URLのパラメータを取得
        $accessUserId = request('access-user-id');
        $accessUser = AccessUser::where('id', $accessUserId)->with(['schoolbuilding'])->first();

        //学校名名の文字数でフォントサイズを変更
        $school_name = $accessUser->schoolbuilding->name_short;
        $school_name_length = mb_strlen($school_name);
        if ($school_name_length >= 7) {
            $school_font_size = "9px";
        } else { //7文字以下の場合
            $school_font_size = "16px";
        }
        //userのsurnameとnameの文字数でフォントサイズを変更
        $user_name = $accessUser->surname . $accessUser->name;
        $user_name_length = mb_strlen($user_name);
        if ($user_name_length >= 7) {
            $user_font_size = "9px";
        } else { //7文字以下の場合
            $user_font_size = "16px";
        }
        // QRコードを生成
        $qr_code = QrCode::size(80)->generate($accessUser->id);

        return view('card.print_preview', compact('accessUser', 'qr_code', 'school_font_size', 'user_font_size'));
    }
    public function printPreviewAll(Request $request)
    {
        // セッションから選択されたユーザーを取得
        $selectedUsers = session('selected_users', []);

        // 画面上で選択されたユーザーを取得
        if ($request->has('selected_users')) {
            $newSelectedUsers = $request->input('selected_users', []);

            // 新たな選択と既存選択をマージしてセッションに保存
            $selectedUsers = array_unique(array_merge($selectedUsers, $newSelectedUsers));
        }

        // 選択解除されたユーザーを削除
        if ($request->has('delete_selected_users')) {
            $deleteSelectedUsers = $request->input('delete_selected_users', []);
            $selectedUsers = array_diff($selectedUsers, $deleteSelectedUsers);
        }

        // ユニークなユーザーIDを取得
        $selectedUsers = array_unique($selectedUsers);

        // ユニークなユーザーIDを取得したユーザー情報を取得
        $accessUsers = AccessUser::whereIn('id', $selectedUsers)->with(['schoolbuilding'])->get();

        foreach ($accessUsers as $accessUser) {
            // 学校名の文字数でフォントサイズを変更
            $school_name = $accessUser->schoolbuilding->name_short;
            $accessUser->schoolbuilding_name = $school_name;
            $school_name_length = mb_strlen($school_name);
            if ($school_name_length >= 7) {
                $accessUser->school_font_size = "9px";
            } else { //7文字以下の場合
                $accessUser->school_font_size = "16px";
            }
            // userのsurnameとnameの文字数でフォントサイズを変更
            $user_name = $accessUser->surname . $accessUser->name;
            $user_name_length = mb_strlen($user_name);
            if ($user_name_length >= 7) {
                $accessUser->user_font_size = "9px";
            } else { //7文字以下の場合
                $accessUser->user_font_size = "16px";
            }
            // QRコードを生成
            $accessUser->qr_code = QrCode::size(80)->generate($accessUser->id);
        }
        return view('card.print_preview_all', compact('accessUsers'));
    }
}
