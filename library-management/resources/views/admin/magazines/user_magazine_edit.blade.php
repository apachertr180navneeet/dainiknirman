@extends('admin.layout.main_app')
@section('title', 'Magazines')

@push('styles')
<!-- Select2 css-->
<link href="{{ asset('public/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('public/plugins/dropify/dropify.css') }}" rel="stylesheet">
<link href="{{ asset('public/plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet">

<style>
    .bootstrap-select.btn-group > .dropdown-toggle{
        padding: 8px 10px !important;
    }
    .customer-services{
        display: block;
        margin: 0px;
        padding: 0px;
        border: 1px solid #C9C9C9;
        min-height: 20px;
        max-height: 100px;
        overflow: auto;
    }
    .customer-services li{
        display: inline-block;
        list-style-type: none;
        margin: 1px;
        padding: 5px;
    }
    /* input[type='text'], input[type='email']{
        text-transform: uppercase;
    } */
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
                    <div class="card-header">
                        <h3 class="card-title">Edit</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form role="form" action="{{ route('admin.magazines.update', ['id' => $magazine->id]) }}" method="post" id="edit-magazine-form" enctype="multipart/form-data">
                        @csrf
                        <!-- Card body -->
                        <div class="card-body">
                            @php
                            $disabledField = request()->get('type') && request()->get('type') == 'u' ? 'disabled' : '';
                            @endphp
                            <!-- Hidden input -->
                            <input type="hidden" name="magazine_id" id="magazine_id" value="{{ $magazine->id }}">
                            <input type="hidden" name="magazine_pdf_name" id="magazine_pdf_name" value="{{ $magazine->file_name }}">
                            <input type="hidden" name="filter_magazine_type" id="filter_magazine_type" value="{{ request()->get('type') && request()->get('type') == 'u' ? request()->get('type') : '' }}">
                            <!-- Hidden input -->

                            <div class="row row-sm">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Title</label>
                                        <input type="text" class="form-control" id="title" name="title" value="{{ $magazine->title }}" placeholder="Enter Title" data-check-url="{{route('admin.magazines.checkMagazineName')}}" {{$disabledField}}/>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Type</label>
                                        <select name="type" id="type" class="form-control" {{$disabledField}}>
                                            <option value="D" {{$magazine->type == 'D' ? 'selected' : ''}}>Daily</option>
                                            <option value="M" {{$magazine->type == 'M' ? 'selected' : ''}}>Monthly</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row row-sm">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Genre</label>
                                        <input type="text" class="form-control" id="genre" name="genre" value="{{ $magazine->genre_name }}" placeholder="Enter Genre" {{$disabledField}}/>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Author Name</label>
                                        <input type="text" class="form-control" id="author" name="author" value="{{ $magazine->author_name }}" placeholder="Enter Author Name" {{$disabledField}}/>
                                    </div>
                                </div>
                            </div>

                            <div class="row row-sm">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Mobile No.</label>
                                        <input type="text" class="form-control" id="mobile_number" name="mobile_number" value="{{ $magazine->mobile_number }}" placeholder="Enter Mobile No." {{$disabledField}}/>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Is Selected</label>
                                        <select class="form-control select-picker" id="is_selected" name="is_selected">
                                            <option value="1" {{($magazine->is_selected == 1) ? 'selected' : ''}}>Yes</option>
                                            <option value="0" {{($magazine->is_selected == 0) ? 'selected' : ''}}>No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row row-sm">
                                <div class="col-md-12 col-lg-12 col-xl-12">
                                    <div class="form-group">
                                        <label class="">Description</label>
                                        <textarea class="form-control" name="description" id="description" placeholder="Enter Description" {{$disabledField}} rows="5">{{ $magazine->description }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row row-sm">
                                
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Status</label>
                                        <select class="form-control select-picker" id="status" name="status" {{$disabledField}}>
                                            <option value="1" {{($magazine->status == 1) ? 'selected' : ''}}>Active</option>
                                            <option value="0" {{($magazine->status == 0) ? 'selected' : ''}}>In-active</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- @if(request()->get('type') && request()->get('type') == 'u')
                            <div class="row">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Is Selected</label>
                                        <select class="form-control select-picker" id="is_selected" name="is_selected">
                                            <option value="1" {{($magazine->is_selected == 1) ? 'selected' : ''}}>Yes</option>
                                            <option value="0" {{($magazine->is_selected == 0) ? 'selected' : ''}}>No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @endif -->
                        </div>
                        
                        <!-- /.card-body -->
                        <!-- Card footer -->
                        <div class="card-footer">
                            <div class="row row-sm">
                                <div class="col-md-12 col-lg-12 col-xl-12 text-right">
                                    <div class="form-group">
                                        <a href="{{route('admin.magazines.index',['type' => 'u'])}}" class="btn btn-info">Cancel</a>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.Card footer -->
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
<script src="{{ asset('public/plugins/flatpickr/flatpickr.js') }}"></script>
<script src="{{ asset('public/js/components.js') }}"></script>
<script src="{{ asset('public/js/magazines/magazines-edit.js') }}"></script>
@endpush