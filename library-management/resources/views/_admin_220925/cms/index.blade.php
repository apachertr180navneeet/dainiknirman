@extends('admin.layout.main_app')
@section('title', 'Cms')

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
            
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="card-title">Cms</h3>
                        </div>
                        <div class="col-md-5">
                        </div>
                        <div class="col-md-1">
                            @if(auth()->user()->hasPermissionTo('Add Cms'))
                                {{-- <a href="{{ route('admin.cms.create') }}" class="btn btn-block btn-primary"><i class="fas fa-plus"></i> Add</a> --}}
                            @endif
                        </div>
                    </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body" style="overflow: auto;">
                        <table id="dataTable" class="table table-bordered table-hover" data-url="{{route('admin.cms.getCms')}}" >
                            <thead>
                                <tr>
                                    <th>S.no.</th>
                                    <th>Title</th>
                                    <th>Created</th>
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
$viewPermission = auth()->user()->hasPermissionTo('View Cms');
$editPermission = auth()->user()->hasPermissionTo('Edit Cms');
$deletePermission = auth()->user()->hasPermissionTo('Delete Cms');
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
<script src="{{ asset('public/js/cms/cms.js') }}"></script>
@endpush
