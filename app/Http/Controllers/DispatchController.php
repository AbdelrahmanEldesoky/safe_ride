<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RideRequest;
use App\Http\Requests\DispatchRequest;
use App\Models\Service;
use App\Models\Coupon;

use App\Traits\RideRequestTrait;
use App\Jobs\NotifyViaMqtt;
use App\Http\Resources\RideRequestResource;
use App\Models\Notification;
use Carbon\Carbon;
use App\Models\User;
use App\Models\RideRequestHistory;
use phpDocumentor\Reflection\DocBlock\Tags\Uses;

class DispatchController extends Controller
{
    use RideRequestTrait;

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pageTitle = __('message.new_booking');
        $assets = ['map_place'];
        $auth_user = authSession();
        $services = Service::all();
        $drivers = User::where('user_type', 'driver')->get();
        $riders = User::where('user_type', 'rider')->get();
        $button = $auth_user->can('riderequest list') ? '<a href="' . route('riderequest.index') . '" class="float-right btn btn-sm btn-primary">' . __('message.list_form_title', ['form' => __('message.riderequest')]) . '</a>' : '';
        return view('dispatch.form', compact('pageTitle', 'assets', 'button', 'services', 'drivers', 'riders'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(DispatchRequest $request)
    {
        $data = $request->all();

        // Check if the rider has registred a riderequest already
        $rider_exists_riderequest = RideRequest::whereNotIn('status', ['canceled', 'completed'])->where('rider_id', request('rider_id'))->where('is_schedule', 0)->exists();

        if ($rider_exists_riderequest) {
            return json_custom_response([
                'message' => __('message.rider_already_in_riderequest'),
                'status' => false,
                'event' => 'validation',
            ]);
        }

        // foreach (config('translatable.locales') as $locale) {
        //     $data['end_address'][$locale] = $request->input("end_address.$locale");
        //     $data['start_address'][$locale] = $request->input("start_address.$locale");
        // }
        // Check if the driver in riderequest already
        if (request('driver_id') != null) {
            $driver_exists_riderequest = RideRequest::whereNotIn('status', ['canceled', 'completed'])->where('driver_id', request('driver_id'))->where('is_schedule', 0)->exists();

            if ($driver_exists_riderequest) {
                return json_custom_response([
                    'message' => __('message.driver_already_in_riderequest'),
                    'status' => false,
                    'event' => 'validation',
                ]);
            }
        }

        $coupon_code = $request->coupon_code;

        if ($coupon_code != null) {
            $coupon = Coupon::where('code', $coupon_code)->first();
            $status = isset($coupon_code) ? 400 : 200;

            if ($coupon != null) {
                $status = Coupon::isValidCoupon($coupon);
            }
            if ($status != 200) {
                $response = couponVerifyResponse($status);
                return json_custom_response($response, $status);
            } else {
                $data['coupon_code'] = $coupon->id;
                $data['coupon_data'] = $coupon;
            }
        }

        $service = Service::with('region')->where('id', $request->service_id)->first();

        $timezone = $service->region->timezone ?? 'UTC';
        $rider = User::where('id', request('rider_id'))->first();
        if ($rider != null) {
            $rider->timezone = $timezone;
            $rider->save();
        }

        $data['datetime'] = Carbon::parse(date('Y-m-d H:i:s'))->setTimezone($timezone)->toDateTimeString();

        if (request()->has('driver_id') && request('driver_id') != null) {
            $data['riderequest_in_driver_id'] = $data['driver_id'];
            $data['riderequest_in_datetime'] = $data['datetime'];

            unset($data['driver_id']);
        }

        $data['distance_unit'] = $service->region->distance_unit ?? 'km';
        $data['status'] = 'new_ride_requested';
        $data['payment_type'] = 'cash';

        $place_details = mighty_get_distance_matrix(request('start_latitude'), request('start_longitude'), request('end_latitude'), request('end_longitude'));
        // distance in meter
        $dropoff_distance_in_meters = distance_value_from_distance_matrix($place_details);
        $dropoff_time_in_seconds = duration_value_from_distance_matrix($place_details);

        $distance_in_unit = 0;

        if ($dropoff_distance_in_meters) {
            // Region->distance_unit == km ( convert meter to km )
            $distance_in_unit = $dropoff_distance_in_meters / 1000;
        }
        $service_data = $service;
        $service_data['distance_unit'] = $distance_in_unit;

        // caclulate ride
        $ridefee = calculateRideFares($distance_in_unit, $dropoff_time_in_seconds, $service_data);
        $data['distance'] = $distance_in_unit;
        $data['total_amount'] = $ridefee['total_amount'];
        $data['duration'] = $dropoff_time_in_seconds / 60;
        $data['booking_type'] = $request->booking_type;
        if ($request->booking_type == 'schedule' && $request->from_date) {
            $data['from_date'] = $request->from_date;
        }

        $result = RideRequest::create($data);

        $message = __('message.save_form', ['form' => __('message.riderequest')]);

        if ($result->is_schedule) {
            $rider_data = [
                'rider_id' => $result->rider_id,
                'rider_name' => optional($result->rider)->display_name ?? '',
            ];

            $history_data = [
                'ride_request_id' => $result->id,
                'history_type' => $result->status,
                'history_message' => __('message.ride.new_ride_requested'),
                'datetime' => date('Y-m-d H:i:s'),
                'history_data' => json_encode($rider_data),
            ];

            RideRequestHistory::create($history_data);
        } else {
            if ($result->status == 'new_ride_requested') {

                $history_data = [
                    'ride_request_id' => $result->id,
                    'history_type' => $result->status,
                    'ride_request' => $result,
                ];
                saveRideHistory($history_data);
                if ($result->riderequest_in_driver_id != null) {
                    $this->notifyDriverForRide($result);
                } else {
                    $this->acceptDeclinedRideRequest($result);
                }
                $notify_data = new \stdClass();
                $notify_data->success = true;
                $notify_data->success_type = $result->status;
                $notify_data->success_message = __('message.ride.new_ride_requested');
                $notify_data->result = new RideRequestResource($result);
                dispatch(new NotifyViaMqtt('new_ride_request_' . $result->rider_id, json_encode($notify_data), $result->rider_id));
            } else {
                $history_data = [
                    'history_type' => $result->status,
                    'ride_request_id' => $result->id,
                    'ride_request' => $result,
                ];

                saveRideHistory($history_data);
            }
        }
        return response()->json(['status' => true, 'event' => 'reset', 'message' => $message]);

        // return redirect()->route('riderequest.index')->withSuccess($message);
    }
}
