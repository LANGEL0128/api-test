<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->role == 3;
    }

    protected function failedAuthorization()
    {
        throw new HttpResponseException(
            response()->json(['message' => 'Not Authorized'], 403)
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $validateEmailEdit = '';
        $id = $this->route('id');
        if(!empty($id))
            $validateEmailEdit = ','.$id;
        return [
            'name' => 'required|max:254',
            'email' => 'required|email|max:254|unique:users,email'.$validateEmailEdit,
            'password' => 'required|min:6|max:254',
            'role' => 'required|numeric|in:1,2',
            'favorite_gender' => Rule::requiredIf(function () {
                return request()->post('role') == 2;
            }),
            'reading_hours' => Rule::requiredIf(function () {
                return request()->post('role') == 2;
            }),
            'nickname' => Rule::requiredIf(function () {
                return request()->post('role') == 1;
            }),
            'principal_gender' => Rule::requiredIf(function () {
                return request()->post('role') == 1;
            }),
            'birth_date' => Rule::requiredIf(function () {
                return request()->post('role') == 1;
            }),
            'status' => Rule::requiredIf(function () {
                return request()->post('role') == 1;
            }),
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre es requerido',
            'name.max' => 'El nombre se excede el límite de caracteres',
            'email.required' => 'El correo es requerido',
            'email.required' => 'El correo no es un correo válido',
            'email.max' => 'El correo se excede del límite de caracteres',
            'email.unique' => 'El correo ya existe en nuestra base de datos. Pruebe con otro',
            'password.required' => 'La contraseña es requerida',
            'password.min' => 'La contraseña debe tener un mínimo de 6 caracteres',
            'password.max' => 'La contraseña se excede del límite de caracteres',
            'role.required' => 'El rol es requerido',
            'role.numeric' => 'El rol debe ser un número entre 1 o 2',
            'role.in' => 'El rol debe ser un número entre 1 o 2',
            'nickname.required_if' => 'El apodo es obligatorio cuando el rol es 1',
            'principal_gender.required_if' => 'El género principal es obligatorio cuando el rol es 1',
            'birth_date.required_if' => 'La fecha de nacimiento es obligatoria cuando el rol es 1',
            'status.required_if' => 'El estado es obligatorio cuando el rol es 1',
            'favorite_gender.required_if' => 'El género favorito es obligatorio cuando el rol es 2',
            'reading_hours.required_if' => 'La cantidad de horas de lecturas es obligatoria cuando el rol es 2',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->first();

        throw new HttpResponseException(
            response()->json([
                'description' => 'Ha ocurrido un error al validar los datos',
                'error' => $errors,
            ], 400)
        );
    }
}
