<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user_id = auth()->user()->id ?? request()->id;

        $rules = [
            'username'  => 'nullable|unique:users,username,'.$user_id,
            'email'     => 'nullable|email|unique:users,email,'.$user_id,
            'contact_number' => 'max:20|unique:users,contact_number,'.$user_id,
            'front_image' => 'nullable | image',
            'back_image' => 'nullable | image',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'userProfile.dob.*'  =>'DOB is required.',
        ];
    }

     /**
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator) {
        $data = [
            'status' => true,
            'message' => $validator->errors()->first(),
            'all_message' =>  $validator->errors()
        ];

        if ( request()->is('api*')){
           throw new HttpResponseException( response()->json($data,422) );
        }

        if ($this->ajax()) {
            throw new HttpResponseException(response()->json($data,422));
        } else {
            throw new HttpResponseException(redirect()->back()->withInput()->with('errors', $validator->errors()));
        }
    }
}
