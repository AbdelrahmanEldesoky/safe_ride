<x-master-layout :assets="$assets ?? []">
    <div>
        <?php $id = $id ?? null; ?>
        @if(isset($id))
            {!! Form::model($data, ['route' => ['dispatch.update', $id], 'method' => 'patch', 'data-toggle'=>'validator' ]) !!}
        @else
            {!! Form::open(['route' => ['dispatch.store'], 'method' => 'post', 'data-toggle'=>'validator' ]) !!}
        @endif
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{ $pageTitle }}</h4>
                        </div>

                        <?php echo $button; ?>
                    </div>

                    <div class="card-body">
                        <div class="new-user-info">
                            <div class="row">
                                {{ Form::hidden('start_latitude', null, [ 'id' => 'start_latitude'] ) }}
                                {{ Form::hidden('start_longitude', null, [ 'id' => 'start_longitude']) }}
                                {{ Form::hidden('end_latitude', null, [ 'id' => 'end_latitude'] ) }}
                                {{ Form::hidden('end_longitude', null, [ 'id' => 'end_longitude']) }}
                                <div class="form-group col-md-4">
                                    {{ Form::label('rider_id', __('message.rider').' <span class="text-danger">*</span>', ['class' => 'form-control-label'], false) }}
                                    <select name="rider_id" class="form-control select2js" id="rider_id" required>
                                        <option
                                            value="">{{ __('message.select_field', ['name' => __('message.rider')]) }}</option>
                                        @foreach ($riders as $rider)
                                            <option
                                                value="{{ $rider->id }}" {{isset($id) && $rider->id == $data->rider_id ? ' selected' : ''}}>{{ $rider->first_name .' '. $rider->last_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    {{ Form::label('start_address', __('message.start_address').' <span class="text-danger">*</span>',['class' => 'form-control-label'], false) }}
                                    {{ Form::text('start_address', old('start_address'),[ 'placeholder' => __('message.start_address'),'class' =>'form-control', 'required']) }}
                                </div>

                                {{-- @foreach(config('translatable.locales') as $locale)
                                    <div class="form-group col-md-4">
                                        <label class="form-label">{{__('message.start_address')}} ({{__('ride.'.$locale)}}) <span
                                                class="text-danger">*</span> </label>
                                        <input type="text" name="start_address[{{ $locale }}]" class="form-control"
                                            data-validation="required"
                                            value="{{isset($id) ? $data->getTranslation('start_address',$locale) : ''}}">
                                    </div>
                                @endforeach
                                @foreach(config('translatable.locales') as $locale)
                                    <div class="form-group col-md-4">
                                        <label class="form-label">{{__('message.end_address')}} ({{__('ride.'.$locale)}}) <span
                                                class="text-danger">*</span> </label>
                                        <input type="text" name="end_address[{{ $locale }}]" class="form-control"
                                            data-validation="required"
                                            value="{{isset($id) ? $data->getTranslation('end_address',$locale) : ''}}">
                                    </div>
                                @endforeach --}}
                                <div class="form-group col-md-4">
                                    {{ Form::label('end_address', __('message.end_address').' <span class="text-danger">*</span>',['class' => 'form-control-label'], false) }}
                                    {{ Form::text('end_address', old('end_address'),[ 'placeholder' => __('message.end_address'),'class' =>'form-control', 'required']) }}
                                </div>

                                <div class="form-group col-md-4">
                                    @if ( $id == null )
                                        <label class="form-control-label" for="service_id">{{ __('message.service') }}
                                            <span class="text-danger" id="distance_unit">* </span></label>
                                    @else
                                        <label class="form-control-label" for="service_id">{{ __('message.service') }}
                                            <span class="text-danger"
                                                  id="distance_unit">* (<small>{{ __('message.distance_in_'.optional($data->service)->distance_unit )  }}</small>)</span>
                                        </label>
                                    @endif
                                    <select name="service_id" class="form-control select2js" id="service_id" required>
                                        <option
                                            value="">{{ __('message.select_field', ['name' => __('message.service')]) }}</option>
                                        @foreach ($services as $service)
                                            <option
                                                value="{{ $service->id }}" {{isset($id) && $service->id == $data->service_id ? ' selected' : ''}}>{{ $service->getTranslation('name', LaravelLocalization::getCurrentLocale()) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- <div class="form-group col-md-4">
                                    {{ Form::label('service_id', __('message.service').' <span class="text-danger">*</span>', ['class' => 'form-control-label'], false) }}
                                    <a class="float-right serviceList" href="javascript:void(0)"><i class="ri-refresh-line"></i></a>
                                    {{ Form::select('service_id', isset($id) ? [ optional($data->service)->id => optional($data->service)->display_name ] : [] , old('service_id') , [
                                            'class' => 'select2js form-group service',
                                            'required',
                                            'data-placeholder' => __('message.select_name',[ 'select' => __('message.service') ]),
                                        ])
                                    }}
                                </div> --}}

                                <div class="form-group col-md-4">
                                    {{ Form::label('driver_id', __('message.driver').' <span class="text-danger">*</span>', ['class' => 'form-control-label'], false) }}

                                    <select name="driver_id" class="form-control select2js" id="driver_id" required>
                                        <option
                                            value="">{{ __('message.select_field', ['name' => __('message.driver')]) }}</option>
                                        @foreach ($drivers as $driver)
                                            <option
                                                value="{{ $driver->id }}" {{isset($id) && $driver->id == $data->drive_id ? ' selected' : ''}}>{{ $driver->first_name .' '. $driver->last_name }}</option>
                                        @endforeach
                                    </select>
                                    {{--                                    {{ Form::label('driver_id', __('message.driver'), ['class' => 'form-control-label'], false) }}--}}
                                    {{--                                    <a class="float-right driverList" href="#"><i class="ri-refresh-line"></i></a>--}}
                                    {{--                                    {{ Form::select('driver_id', isset($id) ? [ optional($data->driver)->id => optional($data->driver)->display_name ] : [] , old('driver_id') , [--}}
                                    {{--                                            'class' => 'select2js form-group driver',--}}
                                    {{--                                            'data-placeholder' => __('message.select_name',[ 'select' => __('message.driver') ]),--}}
                                    {{--                                        ])--}}
                                    {{--                                    }}--}}
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="" class="form-control-label">{{__('message.booking_type')}}</label>
                                    <select name="booking_type" id="booking_type" class="form-control select2js">
                                        <option value="now">{{__('message.now')}}</option>
                                        <option value="schedule">{{__('message.schedule')}}</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6" id="from_date" style="display: none">
                                    <label for="" class="form-control-label"> {{__('message.from_date')}} </label>
                                    <input type="datetime-local" name="from_date" class="form-control">
                                </div>
{{--                                <div class="form-group col-md-6" id="to_date" style="display: none">--}}
{{--                                    <label for="" class="form-control-label"> {{__('auth.to_date')}} </label>--}}
{{--                                    <input type="datetime-local" name="to_date" class="form-control">--}}
{{--                                </div>--}}
                            </div>

                            <hr>
                            {{ Form::submit( __('message.book_now'), [ 'class'=> 'btn btn-md btn-primary float-right', 'data-form' => 'ajax' ] ) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    @section('bottom_script')
        <script>
            $(document).ready(function () {
                $('#booking_type').on('change', function () {
                    if ($(this).val() === 'schedule') {
                        $('#from_date, #to_date').show();
                    } else {
                        $('#from_date, #to_date').hide();
                    }
                });

                // // التحقق من قيمة الـ select عند التحميل الأولي
                // $('#booking_type').trigger('change');
            });
        </script>
        <script>
            $(function () {

                $(document).ready(function () {
                    $('#start_address').val('');
                    $('#end_address').val('');
                });

                if (window.google || window.google.maps) {
                    initialize();
                }

                function initialize() {
                    var start_address_input = document.getElementById('start_address');
                    var start_address = new google.maps.places.Autocomplete(start_address_input);

                    start_address.addListener('place_changed', function () {
                        var place = start_address.getPlace();
                        if (!place.geometry) {
                            alert("{{ __('message.address_autocomplete_error', ['address' => __('message.start_address')]) }}");
                            $('#start_address').focus();
                            return;
                        }
                        start_latitude = place.geometry['location'].lat();
                        start_longitude = place.geometry['location'].lng();
                        $('#start_latitude').val(start_latitude);
                        $('#start_longitude').val(start_longitude);
                        $('#start_address').val(place.formatted_address);
                        serviceList(start_latitude, start_longitude);
                    });

                    var end_address_input = document.getElementById('end_address');
                    var end_address = new google.maps.places.Autocomplete(end_address_input);

                    end_address.addListener('place_changed', function () {
                        var endplace = end_address.getPlace();
                        if (!endplace.geometry) {
                            alert("{{ __('message.address_autocomplete_error', ['address' => __('message.end_address')]) }}");
                            $('#end_address').focus();
                            return;
                        }
                        end_latitude = endplace.geometry['location'].lat();
                        end_longitude = endplace.geometry['location'].lng();
                        $('#end_latitude').val(end_latitude);
                        $('#end_longitude').val(end_longitude);
                        $('#end_address').val(endplace.formatted_address);
                    });
                }

                $(document).on('change', '.service', function () {
                    var service_id = $(this).val();
                    $('.driver').empty();
                    if (service_id != null) {
                        driverList(service_id);
                    }
                })
            });

            $('.serviceList').on('click', function () {
                start_latitude = $('#start_latitude').val();
                start_longitude = $('#start_longitude').val();

                if (start_latitude != '' && start_longitude != '') {
                    serviceList(start_latitude, start_longitude);
                } else {
                    $('.service').empty();
                }
            });

            function serviceList(latitude, longitude) {
                var route = "{{ route('ajax-list',[ 'type' => 'service_for_ride']) }}&latitude=" + latitude + "&longitude=" + longitude;
                route = route.replaceAll('amp;', '');

                $.ajax({
                    url: route,
                    success: function (result) {
                        $('.service').select2({
                            width: '100%',
                            placeholder: "{{ __('message.select_name',[ 'select' => __('message.service') ]) }}",
                            data: result.results
                        });

                        $(".service").val(latitude).trigger('change');
                    }
                })
            }

            $('.driverList').on('click', function () {
                service_id = $('#service_id').val();
                if (service_id != null) {
                    driverList(service_id);
                } else {
                    $('.driver').empty();
                }
            });

            function driverList(service_id) {
                latitude = $('#start_latitude').val();
                longitude = $('#start_longitude').val();

                var route = "{{ route('ajax-list',[ 'type' => 'driver_for_ride']) }}&service_id=" + service_id + "&latitude=" + latitude + "&longitude=" + longitude;
                route = route.replaceAll('amp;', '');

                $.ajax({
                    url: route,
                    success: function (result) {
                        $('.driver').select2({
                            width: '100%',
                            placeholder: "{{ __('message.select_name',[ 'select' => __('message.driver') ]) }}",
                            data: result.results
                        });

                        $(".driver").val(service_id).trigger('change');
                    }
                })
            }
        </script>
    @endsection
</x-master-layout>
