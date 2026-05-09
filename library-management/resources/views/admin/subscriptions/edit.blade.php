@extends('admin.layout.main_app')
@section('title', 'Subscription')

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
                    <form role="form" action="{{ route('admin.subscriptions.update', ['id' => $subscription->id]) }}" method="post" id="edit-subscription-form" enctype="multipart/form-data">
                        @csrf
                        <!-- Card body -->
                        <div class="card-body">
                            <!-- Hidden input -->
                            <input type="hidden" name="subscription_id" id="subscription_id" value="{{ $subscription->id }}">
                            <!-- Hidden input -->

                            <div class="row row-sm">
                                <div class="col-md-4 col-lg-4 col-xl-4">
                                    <div class="form-group">
                                        <label class="">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ $subscription->name }}" placeholder="Enter Subscription Name" data-check-url="{{route('admin.subscriptions.checkSubscriptionName')}}"/>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 col-lg-4 col-xl-4">
                                    <div class="form-group">
                                        <label class="">Amount</label>
                                        <input type="number" class="form-control" id="amount" name="amount" value="{{ $subscription->amount }}" placeholder="Enter Amount"/>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4 col-xl-4">
                                    <div class="form-group">
                                        <label class="">Validity (In Months)</label>
                                        <input type="text" class="form-control" id="validity" name="validity" value="{{ $subscription->validity }}" placeholder="Enter Validity"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row row-sm">
                                <div class="col-md-12 col-lg-12 col-xl-12">
                                    <div class="form-group">
                                        <label class="">Description</label>
                                        <input type="text" class="form-control" id="description" name="description" value="{{ $subscription->description }}" placeholder="Enter Description"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row row-sm">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Type</label>
                                        <select class="form-control select-picker" id="type" name="type">
                                            <option value="AUTHOR" {{($subscription->type == 'AUTHOR') ? 'selected' : ''}}>Author</option>
                                            <option value="READER" {{($subscription->type == 'READER') ? 'selected' : ''}}>Reader</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Status</label>
                                        <select class="form-control select-picker" id="status" name="status">
                                            <option value="1" {{($subscription->status == 1) ? 'selected' : ''}}>Active</option>
                                            <option value="0" {{($subscription->status == 0) ? 'selected' : ''}}>In-active</option>
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
                                        <a href="{{route('admin.subscriptions.index')}}" class="btn btn-info">Cancel</a>
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
<script src="{{ asset('public/js/subscriptions/subscriptions-edit.js') }}"></script>
@endpush