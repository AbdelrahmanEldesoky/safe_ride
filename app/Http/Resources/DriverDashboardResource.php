<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Sos;
use App\Models\RideRequest;

use Illuminate\Database\Eloquent\Builder;

class DriverDashboardResource extends JsonResource
{
    public function toArray($request)
    {
        // dd($request);
        $riderData = [];
        $ride_request_data = RideRequest::with("rider")->where('driver_id', $this->id)->whereNotIn('status', ['canceled'])->where('is_driver_rated', false)->get();
        foreach ($ride_request_data as $value) {
            $riderData[] = $value->rider;
        }


        $on_ride_request = $this->driverRideRequestDetail
            ->whereNotIn('status', ['canceled'])
            ->where('is_driver_rated', false);

        $paymentData = [];
        $pending_payment_ride_request = $this->driverRideRequestDetail()->with("payment")->where('status', 'completed')->where('is_driver_rated', true)
            ->whereHas('payment', function ($q) {
                $q->where('payment_status', 'pending');
            })->get();
        foreach ($pending_payment_ride_request as $value) {
            $paymentData[] = $value->payment;
        }
        // dd($paymentData);

        $rider = isset($riderData) && optional($riderData) ? $riderData : null;
        $payment = isset($paymentData) && optional($paymentData) ? $paymentData : null;

        // dd(new RideRequestResource($on_ride_request));
        return [
            'id' => $this->id,
            'display_name' => $this->display_name,
            'email' => $this->email,
            'username' => $this->username,
            'user_type' => $this->user_type,
            'profile_image' => getSingleMedia($this, 'profile_image', null),
            'status' => $this->status,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            // 'sos'               => Sos::mySOs()->get(),
            'on_ride_request' => isset($on_ride_request) ?( new RideRequestResource($on_ride_request))->each(function ($item){$item;}) : null,
            'rider' => isset($rider) ? (new UserResource($rider))->each(function($item){$item;}) : null,
            'payment' => isset($payment) ? (new PaymentResource($payment)) : null,
        ];
    }
}
