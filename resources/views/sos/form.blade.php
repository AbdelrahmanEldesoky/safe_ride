<x-master-layout :assets="$assets ?? []">
    <div>
        <?php $id = $id ?? null;?>
        @if(isset($id))
            {!! Form::model($data, ['route' => ['sos.update', $id], 'method' => 'patch' ]) !!}
        @else
            {!! Form::open(['route' => ['sos.store'], 'method' => 'post' ]) !!}
        @endif
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{ $pageTitle }}</h4>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="new-user-info">
                            <div class="row">
                                {{-- <div class="form-group col-md-4">
                                    {{ Form::label('region_id', __('message.region'), ['class' => 'form-control-label']) }}
                                    {{ Form::select('region_id', isset($id) ? [ optional($data->region)->id => optional($data->region)->name ] : [] , old('region_id') , [
                                        'data-ajax--url' => route('ajax-list', [ 'type' => 'region' ]),
                                        'data-placeholder' => __('message.select_field', [ 'name' => __('message.region') ]),
                                        'class' =>'form-control select2js', 'required'
                                        ])
                                    }}
                                </div> --}}
                                <div class="form-group col-md-4">
                                    @if ( $id == null )
                                        <label class="form-control-label" for="region_id">{{ __('message.region') }}
                                            <span class="text-danger" id="distance_unit">* </span></label>
                                    @else
                                        <label class="form-control-label" for="region_id">{{ __('message.region') }}
                                            <span class="text-danger"
                                                  id="distance_unit">* (<small>{{ __('message.distance_in_'.optional($data->region)->distance_unit )  }}</small>)</span>
                                        </label>
                                    @endif
                                    <select name="region_id" class="form-control select2js" id="region_id" required>
                                        <option
                                            value="">{{ __('message.select_field', ['name' => __('message.region')]) }}</option>
                                        @foreach ($regions as $region)
                                            <option
                                                value="{{ $region->id }}" {{isset($id) && $region->id == $data->region_id ? ' selected' : ''}}>{{ $region->getTranslation('name', LaravelLocalization::getCurrentLocale()) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @foreach(config('translatable.locales') as $locale)
                                    <div class="form-group col-md-4">
                                        <label class="form-label">{{__('message.title')}} ({{__('ride.'.$locale)}}) <span
                                                class="text-danger">*</span> </label>
                                        <input type="text" name="title[{{ $locale }}]" class="form-control"
                                            data-validation="required"
                                            value="{{isset($id) ? $data->getTranslation('title',$locale) : ''}}">
                                    </div>
                                @endforeach

                                {{-- <div class="form-group col-md-4">
                                    {{ Form::label('title', __('message.title').' <span class="text-danger">*</span>',['class' => 'form-control-label'], false ) }}
                                    {{ Form::text('title', old('title'),[ 'placeholder' => __('message.title'),'class' =>'form-control','required']) }}
                                </div> --}}

                                <div class="form-group col-md-4">
                                    {{ Form::label('contact_number', __('message.contact_number').' <span class="text-danger">*</span>',['class' => 'form-control-label'], false ) }}
                                    {{ Form::text('contact_number', old('contact_number'),[ 'placeholder' => __('message.contact_number'),'class' =>'form-control','required']) }}
                                </div>

                                <div class="form-group col-md-4">
                                    {{ Form::label('status',__('message.status').' <span class="text-danger">*</span>',['class'=>'form-control-label'],false) }}
                                    {{ Form::select('status',[ '1' => __('message.active'), '0' => __('message.inactive') ], old('status'), [ 'class' =>'form-control select2js','required']) }}
                                </div>
                            </div>
                            <hr>
                            {{ Form::submit( __('message.save'), ['class'=>'btn btn-md btn-primary float-right']) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    @section('bottom_script')
    @endsection
</x-master-layout>
