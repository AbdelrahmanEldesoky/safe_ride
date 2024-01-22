<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;


class DriverRequest extends FormRequest
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
        $method = strtolower($this->method());
        $user_id = $this->route()->driver;
        $user_type =isset(request()->user_type)?request()->user_type:'driver';

        $rules = [];
        switch ($method) {
            case 'post':
                $rules = [
                    'username' => [
                        'required',
                        Rule::unique('users')->where(function ($query) use ($user_type, $user_id) {
                            // Add a condition to check for the specific user type
                            return $query->where('user_type', $user_type);
                        }),
                    ],
                    'email' => [
                        'email',
                        'nullable',
                        Rule::unique('users')->where(function ($query) use ($user_type, $user_id) {
                            // Add a condition to check for the specific user type
                            return $query->where('user_type', $user_type);
                        }),
                    ],
                    'contact_number' => [
                        'max:20',
                        'required',
                        Rule::unique('users')->where(function ($query) use ($user_type, $user_id) {
                            // Add a condition to check for the specific user type
                            return $query->where('user_type', $user_type);
                        }),
                    ],
                    'password' => 'required|min:8',

                ];
                break;
            case 'patch':
                $rules = [
                    'username' => [
                        'required',
                        Rule::unique('users')->where(function ($query) use ($user_type, $user_id) {
                            // Add a condition to check for the specific user type
                            return $query->where('user_type', $user_type)->where('id', '!=', $user_id);
                        }),
                    ],
                    'email' => [
                        'email',
                        'nullable',
                        Rule::unique('users')->where(function ($query) use ($user_type, $user_id) {
                            // Add a condition to check for the specific user type
                            return $query->where('user_type', $user_type)->where('id', '!=', $user_id);
                        }),
                    ],
                    'contact_number' => [
                        'max:20',
                        'required',
                        Rule::unique('users')->where(function ($query) use ($user_type, $user_id) {
                            // Add a condition to check for the specific user type
                            return $query->where('user_type', $user_type)->where('id', '!=', $user_id);
                        }),
                    ],
                ];
                break;
        }

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
