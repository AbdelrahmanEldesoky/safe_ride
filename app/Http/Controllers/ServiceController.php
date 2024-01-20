<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;
use App\Models\Service;
use App\DataTables\ServiceDataTable;
use App\Http\Requests\ServiceRequest;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ServiceDataTable $dataTable)
    {
        $pageTitle = __('message.list_form_title', ['form' => __('message.service')]);
        $auth_user = authSession();
        $assets = ['datatable'];
        $button = $auth_user->can('service add') ? '<a href="' . route('service.create') . '" class="float-right btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i> ' . __('message.add_form_title', ['form' => __('message.service')]) . '</a>' : '';
        return $dataTable->render('global.datatable', compact('pageTitle', 'button', 'auth_user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pageTitle = __('message.add_form_title', ['form' => __('message.service')]);
        $regions = Region::all();
        return view('service.form', compact('pageTitle', 'regions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ServiceRequest $request)
    {
        if (env('APP_DEMO')) {
            return redirect()->route('service.index')->withErrors(__('message.demo_permission_denied'));
        }
//        $service = Service::create($request->all());
        $service = new Service();
        foreach (config('translatable.locales') as $locale) {
            $service->setTranslation('name', $locale, $request->input("name.$locale"));
        }
        $service->region_id = $request->region_id;
        $service->capacity = $request->capacity;
        $service->base_fare = $request->base_fare;
        $service->minimum_fare = $request->minimum_fare;
        $service->minimum_distance = $request->minimum_distance;
        $service->per_distance = $request->per_distance;
        $service->per_minute_drive = $request->per_minute_drive;
        $service->per_minute_wait = $request->per_minute_wait;
        $service->waiting_time_limit = $request->waiting_time_limit;
        $service->cancellation_fee = $request->cancellation_fee;
        $service->payment_method = $request->payment_method;
        $service->commission_type = $request->commission_type;
        $service->admin_commission = $request->admin_commission;
        $service->fleet_commission = $request->fleet_commission;
        $service->status = $request->status;
        foreach (config('translatable.locales') as $locale) {
            $service->setTranslation('description', $locale, $request->input("description.$locale"));
        }
        $service->save();
        uploadMediaFile($service, $request->service_image, 'service_image');
        return redirect()->route('service.index')->withSuccess(__('message.save_form', ['form' => __('message.service')]));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pageTitle = __('message.add_form_title', ['form' => __('message.service')]);
        $data = Service::findOrFail($id);

        return view('service.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pageTitle = __('message.update_form_title', ['form' => __('message.service')]);
        $data = Service::findOrFail($id);
        $regions = Region::all();
        return view('service.form', compact('data', 'pageTitle', 'id','regions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(ServiceRequest $request, $id)
    {
        if (env('APP_DEMO')) {
            return redirect()->route('service.index')->withErrors(__('message.demo_permission_denied'));
        }
        $service = Service::findOrFail($id);
        foreach (config('translatable.locales') as $locale) {
            $service->setTranslation('name', $locale, $request->input("name.$locale"));
        }
        $service->region_id = $request->region_id;
        $service->capacity = $request->capacity;
        $service->base_fare = $request->base_fare;
        $service->minimum_fare = $request->minimum_fare;
        $service->minimum_distance = $request->minimum_distance;
        $service->per_distance = $request->per_distance;
        $service->per_minute_drive = $request->per_minute_drive;
        $service->per_minute_wait = $request->per_minute_wait;
        $service->waiting_time_limit = $request->waiting_time_limit;
        $service->cancellation_fee = $request->cancellation_fee;
        $service->payment_method = $request->payment_method;
        $service->commission_type = $request->commission_type;
        $service->admin_commission = $request->admin_commission;
        $service->fleet_commission = $request->fleet_commission;
        $service->status = $request->status;
        foreach (config('translatable.locales') as $locale) {
            $service->setTranslation('description', $locale, $request->input("description.$locale"));
        }
        $service->save();
        // Service data...
//        $service->fill($request->all())->update();

        // Save service service_image...
        if (isset($request->service_image) && $request->service_image != null) {
            $service->clearMediaCollection('service_image');
            $service->addMediaFromRequest('service_image')->toMediaCollection('service_image');
        }

        if (auth()->check()) {
            return redirect()->route('service.index')->withSuccess(__('message.update_form', ['form' => __('message.service')]));
        }
        return redirect()->back()->withSuccess(__('message.update_form', ['form' => __('message.service')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (env('APP_DEMO')) {
            $message = __('message.demo_permission_denied');
            if (request()->ajax()) {
                return response()->json(['status' => true, 'message' => $message]);
            }
            return redirect()->route('service.index')->withErrors($message);
        }
        $service = Service::findOrFail($id);
        $status = 'errors';
        $message = __('message.not_found_entry', ['name' => __('message.service')]);

        if ($service != '') {
            $service->delete();
            $status = 'success';
            $message = __('message.delete_form', ['form' => __('message.service')]);
        }

        if (request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message]);
        }

        return redirect()->back()->with($status, $message);
    }
}
