@extends('admin.layout.main_app')
@section('title', 'Book')

@push('styles')
<!-- Select2 css-->
<link href="{{ asset('public/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('public/plugins/dropify/dropify.css') }}" rel="stylesheet">
<link href="{{ asset('public/plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet">

<style>
    .bootstrap-select.btn-group > .dropdown-toggle{
        padding: 8px 10px !important;
    }
    /*input[type='text'], input[type='email']{
        text-transform: uppercase;
    }*/
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
                            <h3 class="card-title">Add</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form role="form" action="{{ route('admin.books.store') }}" method="post" id="add-book-form" enctype="multipart/form-data">
                            @csrf
                            <!-- Card body -->
                            <div class="card-body">
                                <div class="row row-sm">
                                    <div class="col-md-6 col-lg-6 col-xl-6">
                                        <div class="form-group">
                                            <label class="">Book Name</label>
                                            <input type="text" class="form-control" id="book_name" name="book_name" value="{{ old('book_name') }}" placeholder="Enter Book Name" data-check-url="{{route('admin.books.checkBookName')}}"/>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 col-lg-6 col-xl-6">
                                        <div class="form-group">
                                            <label class="">Author</label>
                                            <input type="text" class="form-control" id="author_name" name="author_name" value="{{ old('author_name') }}" placeholder="Enter Author Name"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="row row-sm">
                                    <div class="col-md-6 col-lg-6 col-xl-6">
                                        <div class="form-group">
                                            <label class="">Launch Date</label>
                                            <input type="text" class="form-control date-picker" id="launch_date" name="launch_date" value="{{ old('launch_date') }}" placeholder="Enter Launch Date"/>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-6 col-xl-6">
                                        <div class="form-group">
                                            <label class="">Book Type</label>
                                            <select class="form-control select-picker" id="book_type" name="book_type">
                                                <option value="">Select</option>
                                                <option value="F">Free</option>
                                                <option value="P">Paid</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row row-sm">
                                    <div class="col-md-12 col-lg-12 col-xl-12">
                                        <div class="form-group">
                                            <label class="">Description</label>
                                            <input type="text" class="form-control" id="description" name="description" value="{{ old('description') }}" placeholder="Enter Description"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="row row-sm">
                                    <div class="col-md-6 col-lg-6 col-xl-6">
                                        <div class="form-group">
                                            <label class="">Cover Picture</label>
                                            <input type="file" class="form-control image-preview" id="cover_picture" name="cover_picture"/>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-6 col-xl-6">
                                        <div class="col-md-12 col-lg-12 col-xl-12">
                                            <div class="form-group">
                                                <label class="">Book PDF</label>
                                                <input type="file" class="form-control" id="book_pdf" name="book_pdf"/>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-lg-12 col-xl-12">
                                            <div class="form-group">
                                                <label class="">Price</label>
                                                <input type="text" class="form-control" id="price" name="price" value="{{ old('price') }}" placeholder="Enter Price" />
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-lg-12 col-xl-12">
                                            <div class="form-group">
                                                <label class="">Status</label>
                                                <select class="form-control select-picker" id="status" name="status">
                                                    <option value="1">Active</option>
                                                    <option value="0">In-active</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row row-sm">
                                    
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <!-- Card footer -->
                            <div class="card-footer">
                                <div class="row row-sm">
                                    <div class="col-md-12 col-lg-12 col-xl-12 text-right">
                                        <div class="form-group">
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
<script src="{{ asset('public/js/books/books-create.js') }}"></script>
@endpush