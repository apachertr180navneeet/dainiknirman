@extends('admin.layout.main_app')
@section('title', 'Setting Edit')

@push('styles')
<!-- Select2 css-->
<link href="{{ asset('public/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('public/plugins/dropify/dropify.css') }}" rel="stylesheet">

<style>
    .bootstrap-select.btn-group > .dropdown-toggle{
        padding: 8px 10px !important;
    }
</style>
@endpush

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="card">
                    <!-- <div class="card-header">
                        <h3 class="card-title">Customer Detail</h3>
                    </div> -->
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form role="form" action="{{ route('admin.settings.update') }}" method="post" id="edit-setting-form" enctype="multipart/form-data">
                            <div class="card-body">
                            @csrf
                            <div class="row row-sm">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Site Title</label>
                                        <input type="text" class="form-control" id="site_title" name="site_title" value="{{ $data['site_title'] }}" placeholder="Enter Site Title" required/>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Logo Title</label>
                                        <input type="text" class="form-control" id="logo_title" name="logo_title" value="{{ $data['logo_title'] }}" placeholder="Enter Logo Title" required/>
                                    </div>
                                </div>
                            </div>

                            <div class="row row-sm">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Reserved Right</label>
                                        <input type="text" class="form-control" id="reserved_right" name="reserved_right" value="{{ $data['reserved_right'] }}" placeholder="Enter Reserved Right" required/>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Show Records Per Page</label>
                                        <select class="form-control" name="item_per_page" id="item_per_page">
                                            <option value="10" {{($data['item_per_page'] == 10) ? "selected" : ""}}>10</option>
                                            <option value="25" {{($data['item_per_page'] == 25) ? "selected" : ""}}>25</option>
                                            <option value="50" {{($data['item_per_page'] == 50) ? "selected" : ""}}>50</option>
                                            <option value="100" {{($data['item_per_page'] == 100) ? "selected" : ""}}>100</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row row-sm">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">App Version</label>
                                        <input type="text" class="form-control" id="app_version" name="app_version" value="{{ $data['app_version'] }}" placeholder="Enter App Version" required/>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">UPI ID</label>
                                        <input type="text" class="form-control" id="upi_id" name="upi_id" value="{{ $data['upi_id'] }}" placeholder="Enter UPI ID" required/>
                                    </div>
                                </div>
                            </div>

                            @php
                            $qrImage = null;
                            $bannerImage = null;
                            if(!empty($data['qr_code_image']))
                            {
                                $qrImage = \Storage::disk('local')->url('images/settings/'.$data['qr_code_image']);
                            }

                            if(!empty($data['banner_image']))
                            {
                                $bannerImage = \Storage::disk('local')->url('images/settings/'.$data['banner_image']);
                            }
                            @endphp

                            <div class="row row-sm">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">QR Code Image</label>
                                        <input type="file" class="form-control image-preview" id="qr_code_image" name="qr_code_image" data-default-file="{{$qrImage}}" data-show-remove="false"/>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Banner Image</label>
                                        <input type="file" class="form-control image-preview" id="banner_image" name="banner_image" data-default-file="{{$bannerImage}}" data-show-remove="false"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row row-sm">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Service Plan</label>
                                        <input type="file" class="form-control" id="service_plan" name="service_plan"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <div class="row row-sm">
                                <div class="col-md-12 col-lg-12 col-xl-12 text-right">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.card -->
            </div>
            <!--/.col (left) -->
        </div>
    </div>
</section>
@endsection
@push('scripts')
<script src="{{ asset('public/plugins/dropify/dropify.min.js') }}"></script>
<script src="{{ asset('public/plugins/jquery-ui/jquery-ui.js') }}"></script>
<script src="{{ asset('public/js/components.js') }}"></script>
<script src="{{ asset('public/js/settings/settings-edit.js') }}"></script>
@endpush