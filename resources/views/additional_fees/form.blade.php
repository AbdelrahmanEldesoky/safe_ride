<x-master-layout :assets="$assets ?? []">
    <div>
        <?php $id = $id ?? null;?>
        @if(isset($id))
            {!! Form::model($data, ['route' => ['additionalfees.update', $id], 'method' => 'patch' ]) !!}
        @else
            {!! Form::open(['route' => ['additionalfees.store'], 'method' => 'post' ]) !!}
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
                                @foreach(config('translatable.locales') as $locale)
                                    <div class="form-group col-md-4">
                                        <label class="form-label">{{__('message.title')}} ({{__('ride.'.$locale)}}) <span
                                                class="text-danger">*</span> </label>
                                        <input type="text" name="title[{{ $locale }}]" class="form-control"
                                               data-validation="required"
                                               value="{{isset($id) ? $data->getTranslation('title',$locale) : ''}}">
                                    </div>
                                @endforeach
{{--                                <div class="form-group col-md-4">--}}
{{--                                    {{ Form::label('title', __('message.title').' <span class="text-danger">*</span>',['class' => 'form-control-label'], false ) }}--}}
{{--                                    {{ Form::text('title', old('title'),[ 'placeholder' => __('message.title'),'class' =>'form-control','required']) }}--}}
{{--                                </div>--}}

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
