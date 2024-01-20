<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class DispatchRequest extends FormRequest
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
        $rules = [
            'rider_id'          => 'required',
            // 'driver_id'         => 'required',
            'service_id'        => 'required',
            'start_latitude'    => 'required',
            'start_longitude'   => 'required',
            'end_latitude'      => 'required',
            'end_longitude'     => 'required',
            'booking_type' => 'required',
            'from_date' => 'required_if:booking_type,schedule',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'start_latitude.required'    => __('validation.required', [ 'attribute' => __('message.start_address') ]),
            'start_longitude.required'   => __('validation.required', [ 'attribute' => __('message.start_address') ]),
            'end_latitude.required'      => __('validation.required', [ 'attribute' => __('message.end_address') ]),
            'end_longitude.required'     => __('validation.required', [ 'attribute' => __('message.end_address') ]),
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
            $data['status'] = false;
            $data['event'] = 'validation';
            throw new HttpResponseException(response()->json($data,200));
        } else {
            throw new HttpResponseException(redirect()->back()->withInput()->with('errors', $validator->errors()));
        }
    }
}
