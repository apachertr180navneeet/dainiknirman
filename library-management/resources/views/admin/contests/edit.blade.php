@extends('admin.layout.main_app')
@section('title', 'Contest')

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
                    <form role="form" action="{{ route('admin.contests.update', ['id' => $contest->id]) }}" method="post" id="edit-contest-form" enctype="multipart/form-data">
                        @csrf
                        <!-- Card body -->
                        <div class="card-body">
                            <!-- Hidden input -->
                            <input type="hidden" name="contest_id" id="contest_id" value="{{ $contest->id }}">
                            <!-- Hidden input -->

                            <div class="row row-sm">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Title</label>
                                        <input type="text" class="form-control" id="title" name="title" value="{{ $contest->title }}" placeholder="Enter Title" data-check-url="{{route('admin.contests.checkContestTitle')}}"/>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Date</label>
                                        @php
                                        $date = date("d-m-Y", strtotime($contest->date));
                                        @endphp
                                        <input type="text" class="form-control date-picker" id="date" name="date" value="{{ $date }}" placeholder="Enter Date"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row row-sm">
                                <div class="col-md-12 col-lg-12 col-xl-12">
                                    <div class="form-group">
                                        <label class="">Description</label>
                                        <textarea class="form-control" id="description" name="description" placeholder="Enter Description">{{ $contest->description }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row row-sm">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Status</label>
                                        <select class="form-control select-picker" id="status" name="status">
                                            <option value="1" {{($contest->status == 1) ? 'selected' : ''}}>Active</option>
                                            <option value="0" {{($contest->status == 0) ? 'selected' : ''}}>In-active</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <!-- Card footer -->
                        <div class="card-footer">
                            <div class="row row-sm">
                                <div class="col-md-12 col-lg-12 col-xl-12 text-right">
                                    <div class="form-group">
                                        <a href="{{route('admin.contests.index')}}" class="btn btn-info">Cancel</a>
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
<script src="{{ asset('public/js/contests/contests-edit.js') }}"></script>
@endpush