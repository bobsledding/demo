<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LineWebhookRequest extends FormRequest
{
    /**
     * 驗證已在middleware完成
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'events' => 'required|array',
        ];
    }
}
