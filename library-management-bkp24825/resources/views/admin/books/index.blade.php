@extends('admin.layout.main_app')
@section('title', 'Books')

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
                                        <label for="">ITR Status</label>
                                        <select name="filter_itr_status" id="filter_itr_status" class="form-control select-picker">
                                            <option value="">Select</option>
                                            <option value="PENDING">Pending</option>
                                            <option value="PROCESS">Process</option>
                                            <option value="COMPLETE">Complete</option>
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
                            <h3 class="card-title">Books</h3>
                        </div>
                        <div class="col-md-5">
                        </div>
                        <div class="col-md-1">
                            @if(auth()->user()->hasPermissionTo('Add Book'))
                                <a href="{{ route('admin.books.create') }}" class="btn btn-block btn-primary"><i class="fas fa-plus"></i> Add</a>
                            @endif
                        </div>
                    </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body" style="overflow: auto;">
                        <table id="dataTable" class="table table-bordered table-hover" data-url="{{route('admin.books.getBooks')}}" data-destroy-url="{{route('admin.books.destroy')}}" data-change-status-url="{{route('admin.books.changeStatus')}}" data-export-csv-url="{{route('admin.books.export', ['type' => 'excel'])}}">
                            <thead>
                                <tr>
                                    <th>S.no.</th>
                                    <th>Book Name</th>
                                    <th>Author Name</th>
                                    <th>Launch Date</th>
                                    <th>Type</th>
                                    <th>Date</th>
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
$viewPermission = auth()->user()->hasPermissionTo('View Book');
$editPermission = auth()->user()->hasPermissionTo('Edit Book');
$deletePermission = auth()->user()->hasPermissionTo('Delete Book');
@endphp

@endsection

@push('scripts')
<script>
    var viewPermission = '{{$viewPermission}}';
    var editPermission = '{{$editPermission}}';
    var deletePermission = '{{$deletePermission}}';
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
<script src="{{ asset('public/js/books/books.js') }}"></script>
@endpush
