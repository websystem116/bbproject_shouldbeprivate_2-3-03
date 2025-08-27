<?php

namespace App\Services;

class PasswordGenerationService
{
    /**
     * 使用可能な文字セット (小文字 'l', 大文字 'I' を除外)
     *
     * @var string
     */
    private const BASE_CHARS = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ0123456789';

    /**
     * 指定された文字を除外してランダムなパスワードを生成する
     *
     * @param int $length パスワードの長さ
     * @param array $excludeChars 除外する文字の配列
     * @return string 生成されたパスワード
     * @throws \Exception 暗号学的に安全な乱数生成器が利用できない場合
     */
    public function generateExcludedPassword(
        int $length = 8,
        array $excludeChars = ['l', 'I']
    ): string
    {
        // $excludeChars に含まれる文字を $baseChars からさらに削除 (柔軟性のため)
        $allowedChars = str_replace($excludeChars, '', self::BASE_CHARS);

        $allowedCharsCount = strlen($allowedChars);
        if ($allowedCharsCount === 0) {
            throw new \Exception('使用可能な文字がありません。');
        }

        $password = '';
        for ($i = 0; $i < $length; $i++) {
            // random_int は暗号学的に安全な乱数を生成する
            $randomIndex = random_int(0, $allowedCharsCount - 1);
            $password .= $allowedChars[$randomIndex];
        }

        return $password;
    }
}