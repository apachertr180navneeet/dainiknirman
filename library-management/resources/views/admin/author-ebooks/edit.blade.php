@extends('admin.layout.main_app')
@section('title', 'Author Ebook')

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
                    <form role="form" action="{{ route('admin.author-ebooks.update', ['id' => $eBook->id]) }}" method="post" id="edit-book-form" enctype="multipart/form-data">
                        @csrf
                        <!-- Card body -->
                        <div class="card-body">
                            <!-- Hidden input -->
                            <input type="hidden" name="ebook_id" id="ebook_id" value="{{ $eBook->id }}">
                            <!-- Hidden input -->

                            <div class="row row-sm">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Title</label>
                                        <input type="text" class="form-control" id="title" name="title" value="{{ $eBook->title }}" placeholder="Enter Book Name" disabled/>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Author</label>
                                        <input type="text" class="form-control" id="author_name" name="author_name" value="{{ $eBook->author_name }}" placeholder="Enter Author Name" disabled/>
                                    </div>
                                </div>
                            </div>
                            <div class="row row-sm">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Mobile</label>
                                        <input type="text" class="form-control" id="author_mobile" name="author_mobile" value="{{ $eBook->mobile }}" placeholder="Enter Author Mobile" disabled/>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Date</label>
                                        @php
                                        $publishDate = "";
                                        if(!empty($eBook->publish_date)){
                                            $publishDate = date("d-m-Y", strtotime($eBook->publish_date));
                                        }
                                        @endphp
                                        <input type="text" class="form-control date-picker" id="publish_date" name="publish_date" value="{{ $publishDate }}" placeholder="Enter Publish Date"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row row-sm">
                                <div class="col-md-12 col-lg-12 col-xl-12">
                                    <div class="form-group">
                                        <label class="">Description</label>
                                        <input type="text" class="form-control" id="description" name="description" value="{{ $eBook->description }}" placeholder="Enter Description" disabled/>
                                    </div>
                                </div>
                            </div>

                            <div class="row row-sm">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    @php
                                    $eBookFile = '';
                                    if(isset($eBook->file_name) && !empty($eBook->file_name))
                                    {
                                        $eBookFile = $eBook->file_name;
                                        $eBookFile = \Storage::disk("local")->url("ebook/user/".$eBook->created_by."/".$eBookFile);
                                    }
                                    @endphp
                                    <div class="form-group">
                                        <label class="">Download Ebook 
                                            @if(!empty($eBookFile))
                                                <span>
                                                    <a href="{{$eBookFile}}" download><i class="fa fa-download"></i></a>
                                                </span>
                                            @endif
                                        </label>
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
                                        <a href="{{route('admin.author-ebooks.index')}}" class="btn btn-info">Cancel</a>
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
<script src="{{ asset('public/js/author-ebooks/author-ebooks-edit.js') }}"></script>
@endpush