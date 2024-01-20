<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">

                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3">
                            <h5 class="font-weight-bold">{{ $pageTitle ?? __('message.list') }}</h5>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">

                        {{ Form::model($setting_data,['method' => 'POST','route'=>'privacy-policy-save', 'data-toggle'=>"validator" ] ) }}
                        {{ Form::hidden('id') }}
                        @foreach(config('translatable.locales') as $locale)
                            <div class="form-group col-md-12">
                                <label class="form-label">{{__('message.privacy_policy')}} ({{__('ride.'.$locale)}})
                                    <span
                                        class="text-danger">*</span> </label>
                                <textarea type="text" name="value[{{ $locale }}]"
                                          class="form-control tinymce-privacy_policy"
                                          data-validation="required">{{ json_decode($setting_data->value ?? "")->$locale ?? '' }}
                            </textarea>
                            </div>
                        @endforeach
                        {{ Form::submit( __('message.save'), ['class'=>'btn btn-md btn-primary float-right']) }}
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @section('bottom_script')
        <script>
            (function ($) {
                $(document).ready(function () {
                    tinymceEditor('.tinymce-privacy_policy', ' ', function (ed) {

                    }, 450)

                });

            })(jQuery);
        </script>
    @endsection
</x-master-layout>
