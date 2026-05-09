@extends('admin.layout.main_app')
@section('title', (request()->get('type') && request()->get('type') == 'u') ? 'User Magazine' : 'Magazine')

@push('styles')
<!-- Select2 css-->
<link href="{{ asset('public/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Internal DataTables css-->
<link href="{{ asset('public/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('public/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('public/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('public/css/custom.css') }}" rel="stylesheet" type="text/css" />

<style>
    .btn-toolbar{
        justify-content:flex-end !important;
    }
</style>
@endpush

@section('content')
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            @endif
            {{-- <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form class="custom-datatable-filter-form">
                            <div class="row">
                                <!-- <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                    <input type="text" name="filter_pan" id="filter_pan" placeholder="Search By PAN" class="form-control" value="" />
                                </div> -->
                                <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                    <div class="form-group">
                                        <label for="">Type</label>
                                        <select name="filter_magazine_type" id="filter_magazine_type" class="form-control select-picker">
                                            <option value="">Select</option>
                                            <option value="D">Daily</option>
                                            <option value="M">Monthly</option>
                                        </select>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                                    <input type="button" name="filter_btn" id="filter_btn" class="btn btn-dark apply-filter" value="Search" />
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div> --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="card-title">{{(request()->get('type') && request()->get('type') == 'u') ? 'User Magazines' : 'Magazines'}}</h3>
                        </div>
                        <div class="col-md-5">
                        </div>
                        <div class="col-md-1">
                            @if(auth()->user()->hasPermissionTo('Add Magazines') && !request()->get('type') && request()->get('type') != 'u')
                                <a href="{{ route('admin.magazines.create') }}" class="btn btn-block btn-primary"><i class="fas fa-plus"></i> Add</a>
                            @endif
                        </div>
                    </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body" style="overflow: auto;">
                        <table id="dataTable" class="table table-bordered table-hover" data-url="{{route('admin.magazines.getMagazines')}}" data-destroy-url="{{route('admin.magazines.destroy')}}" data-change-status-url="{{route('admin.magazines.changeStatus')}}" >
                            <thead>
                                <tr>
                                    <th>S.no.</th>
                                    <th>Title</th>
                                    <th>Type</th>
                                    
                                    @if(request()->get('type') && request()->get('type') == 'u')
                                        <th>Is Selected</th>
                                    @else
                                        <th>Date</th>
                                    @endif

                                    <th>Created</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->
@php
$viewPermission = auth()->user()->hasPermissionTo('View Magazines');
$editPermission = auth()->user()->hasPermissionTo('Edit Magazines');
$deletePermission = auth()->user()->hasPermissionTo('Delete Magazines');
@endphp

@endsection

@push('scripts')
<script>
    var viewPermission = '{{$viewPermission}}';
    var editPermission = '{{$editPermission}}';
    var deletePermission = '{{$deletePermission}}';
    var recordsFilterType = '{{$type}}';
</script>

<!-- Internal Chart.Bundle js-->
<script src="{{ asset('public/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('public/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('public/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('public/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('public/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('public/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('public/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('public/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('public/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('public/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('public/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
<script src="{{ asset('public/js/components.js') }}"></script>
<script src="{{ asset('public/js/magazines/magazines.js') }}"></script>
@endpush
