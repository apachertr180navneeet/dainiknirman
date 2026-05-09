<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RegistrationRequest extends FormRequest
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
        return [
            'name' => 'required',
            'gender' => 'required|in:M,F',
            'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'mobile' => 'required|integer|unique:users,mobile,NULL,NULL,deleted_at,NULL',
            'profile_photo' => 'sometimes|image|max:1024|mimes:jpg,jpeg,png,gif'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function messages():array
    {
        return [
            'name.required' => 'Name is required field.',
            'gender.required' => 'Gender is required field.',
            'gender.in' => 'Gender can be either male or female.',
            'email.required' => 'Email is required field.',
            'email.email' => 'Please enter valid email address.',
            'email.unique' => 'Email address is already in use.',
            'mobile.required' => 'Mobile number is required field.',
            'mobile.unique' => 'Mobile number is already in use.',
            'profile_photo.image' => 'Please upload a valid image file.',
            'profile_photo.max' => 'Please upload a valid image file upto 5mb.',
            'profile_photo.mimes' => 'Please upload a valid image file.'
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $response = new JsonResponse([
            'status' => false,
            'message' => $validator->errors()->first(),
            'data'=> null
        ], 422);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
