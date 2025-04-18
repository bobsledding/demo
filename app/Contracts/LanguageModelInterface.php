<?php

namespace App\Contracts;

interface LanguageModelInterface
{
    /**
     * 傳入單句或對話紀錄以進行對話
     *
     * @param  array|string $input 單句或對話紀錄
     * @return string AI回覆
     */
    public function chat(array|string $input): string;

    /**
     * 傳入圖片取得分析結果
     *
     * @param  mixed $binary
     * @return string
     */
    public function analyze(string $binary): string;
}
