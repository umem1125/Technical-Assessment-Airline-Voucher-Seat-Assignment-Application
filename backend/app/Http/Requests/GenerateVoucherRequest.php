<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class GenerateVoucherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'id' => ['required', 'string'],
            'flightNumber' => ['required', 'string'],
            'date' => ['required', 'date_format:Y-m-d'],
            'aircraft' => ['required', Rule::in(['ATR', 'Airbus 320', 'Boeing 737 Max'])],
        ];
    }

    // costum error message
    #[Override]
    public function messages()
    {
        return [
            'aircraft.in' => 'Tipe pesawat harus berupa ATR, Airbus 320, atau Boeing 737 Max.',
            'date.date_format' => 'Format tanggal harus YYYY-MM-DD',
        ];
    }
}
