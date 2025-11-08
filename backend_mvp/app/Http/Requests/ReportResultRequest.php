<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportResultRequest extends FormRequest
{
    public function rules()
    {
        return [
            'home_score' => 'required|integer|min:0',
            'away_score' => 'required|integer|min:0',
        ];
    }
}
