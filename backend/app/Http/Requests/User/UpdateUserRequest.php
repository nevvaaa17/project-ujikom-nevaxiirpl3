<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Mendapatkan ID user yang sedang di-update dari berbagai kemungkinan kondisi
        $userParam = $this->route('user') ?? $this->route('id');
        
        if (is_object($userParam)) {
            $userId = $userParam->id;
        } elseif (!empty($userParam)) {
            $userId = $userParam;
        } else {
            // Ambil angka ID langsung dari ujung URL (misal: '1' dari /api/users/1)
            $userId = last(array_filter(explode('/', $this->path()), 'is_numeric'));
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 
                'string', 
                'email', 
                'max:255',
                // Mengabaikan ID user ini agar tidak bentrok dengan email dirinya sendiri
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => [
                'nullable', 
                'string',
                Password::min(8)->letters()->numbers(),
            ],
            'role' => [
                'required',
                Rule::in(['admin', 'petugas', 'peminjam']),
            ],
            'no_hp' => ['nullable', 'string', 'max:15'],
            'alamat' => ['nullable', 'string'],
            'foto_profile' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }
}