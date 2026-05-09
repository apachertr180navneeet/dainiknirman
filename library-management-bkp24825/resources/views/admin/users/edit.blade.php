@extends('admin.layout.main_app')
@section('title', 'Users')

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
                    <form role="form" action="{{ route('admin.users.update', ['id' => $user->id]) }}" method="post" id="edit-user-form" enctype="multipart/form-data">
                        @csrf
                        <!-- Card body -->
                        <div class="card-body">
                            <!-- Hidden input -->
                            <input type="hidden" name="user_id" id="user_id" value="{{ $user->id }}">
                            <!-- Hidden input -->

                            <div class="row row-sm">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" placeholder="Enter Name"/>
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Mobile</label>
                                        <input type="number" class="form-control" id="mobile" name="mobile" value="{{ $user->mobile }}" placeholder="Enter Mobile" data-check-url="{{route('admin.users.checkUserMobile')}}"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row row-sm">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" placeholder="Enter Email" data-check-url="{{route('admin.users.checkUserEmail')}}"/>
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Gender</label>
                                        <select class="form-control select-picker" id="gender" name="gender">
                                            <option value="">Select</option>
                                            <option value="M" {{$user->gender == 'M' ? 'selected' : ''}}>Male</option>
                                            <option value="F" {{$user->gender == 'F' ? 'selected' : ''}}>Female</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row row-sm">
                                <div class="col-md-12 col-lg-12 col-xl-12">
                                    <div class="form-group">
                                        <label class="">Address</label>
                                        <input type="text" class="form-control" id="address" name="address" value="{{ $user->address }}" placeholder="Enter Address"/>
                                        @error('address')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- <div class="row row-sm">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">City Name</label>
                                        <input type="text" class="form-control" id="city_name" name="city_name" value="{{ $user->city_name }}" placeholder="Enter City Name" />
                                        @error('city_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">State Name</label>
                                        <input type="text" class="form-control" id="state_name" name="state_name" value="{{ $user->state_name }}" placeholder="Enter State Name" />
                                        @error('state_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div> -->

                            <div class="row row-sm">
                                <div class="col-md-12 col-lg-12 col-xl-12">
                                    <h3>Bank Details</h3>
                                </div>
                            </div>

                            <div class="row row-sm">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Account Holder Name</label>
                                        <input type="text" class="form-control" id="account_holder_name" name="account_holder_name" value="{{ $user->account_detail->account_holder_name ?? '' }}" placeholder="Enter Acc. Holder Name"/>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">IFSC Code</label>
                                        <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" value="{{ $user->account_detail->ifsc_code ?? '' }}" placeholder="Enter Bank Name"/>
                                        @error('ifsc_code')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row row-sm">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Branch Name</label>
                                        <input type="text" class="form-control" id="branch_name" name="branch_name" value="{{ $user->account_detail->branch_name ?? '' }}" placeholder="Enter Branch Name"/>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Bank A/c No.</label>
                                        <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" value="{{ $user->account_detail->account_number ?? '' }}" placeholder="Enter Bank Account No." data-check-url="{{route('admin.users.checkBankAccountNumber')}}"/>
                                        @error('bank_account_number')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row row-sm">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">City Name</label>
                                        <input type="text" class="form-control" id="city_name" name="city_name" value="{{ $user->account_detail->city_name ?? '' }}" placeholder="Enter City Name"/>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Status</label>
                                        <select class="form-control select-picker" id="status" name="status">
                                            <option value="1" {{($user->status == 1) ? 'selected' : ''}}>Active</option>
                                            <option value="0" {{($user->status == 0) ? 'selected' : ''}}>In-active</option>
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
                                        <a href="{{route('admin.users.index')}}" class="btn btn-info">Cancel</a>
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
<script src="{{ asset('public/js/users/users-edit.js') }}"></script>
@endpush