@extends('admin.layout.main_app')
@section('title', 'Books')

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
                    <form role="form" action="{{ route('admin.books.update', ['id' => $book->id]) }}" method="post" id="edit-book-form" enctype="multipart/form-data">
                        @csrf
                        <!-- Card body -->
                        <div class="card-body">
                            <!-- Hidden input -->
                            <input type="hidden" name="book_id" id="book_id" value="{{ $book->id }}">
                            <!-- Hidden input -->

                            <div class="row row-sm">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Book Name</label>
                                        <input type="text" class="form-control" id="book_name" name="book_name" value="{{ $book->book_name }}" placeholder="Enter Book Name" data-check-url="{{route('admin.books.checkBookName')}}"/>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Author</label>
                                        <input type="text" class="form-control" id="author_name" name="author_name" value="{{ $book->author_name }}" placeholder="Enter Author Name"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row row-sm">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Launch Date</label>
                                        <input type="text" class="form-control date-picker" id="launch_date" name="launch_date" value="{{ $book->launch_date }}" placeholder="Enter Launch Date"/>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Book Type</label>
                                        <select class="form-control select-picker" id="book_type" name="book_type">
                                            <option value="F" {{$book->book_type == 'F' ? 'selected' : ''}}>Free</option>
                                            <option value="P" {{$book->book_type == 'P' ? 'selected' : ''}}>Paid</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row row-sm">
                                <div class="col-md-12 col-lg-12 col-xl-12">
                                    <div class="form-group">
                                        <label class="">Description</label>
                                        <input type="text" class="form-control" id="description" name="description" value="{{ $book->description }}" placeholder="Enter Description"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row row-sm">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    @php
                                    $coverPictureImage = '';
                                    if(isset($book->cover_picture) && !empty($book->cover_picture))
                                    {
                                        $coverPictureImage = $book->cover_picture;
                                        $coverPictureImage = \Storage::disk("public")->url("book/cover/".$coverPictureImage);
                                        // $coverPictureImage = asset($coverPictureImage);
                                    }
                                    @endphp
                                    <div class="form-group">
                                        <label class="">Cover Picture 
                                            @if(!empty($coverPictureImage))
                                                <!-- <span>
                                                    <a href="{{$coverPictureImage}}" download><i class="fa fa-download"></i></a>
                                                </span> -->
                                            @endif
                                        </label>
                                        <input type="file" class="form-control image-preview" id="cover_picture" name="cover_picture" data-show-remove="false" data-default-file="{{$coverPictureImage}}"/>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12 col-xl-12">
                                            @php
                                            $bookPdf = '';
                                            if(isset($book->book_pdf) && !empty($book->book_pdf))
                                            {
                                                $bookPdf = $book->book_pdf;
                                                $bookPdf = \Storage::disk("public")->url("book/pdf/".$bookPdf);
                                                // $bookPdf = asset($bookPdf);
                                            }
                                            @endphp
                                            <div class="form-group">
                                                <label class="">Book PDF
                                                    @if(!empty($bookPdf))
                                                        <span>
                                                            <a href="{{$bookPdf}}" download><i class="fa fa-download"></i></a>
                                                        </span>
                                                    @endif
                                                </label>
                                                <input type="file" class="form-control" id="book_pdf" name="book_pdf"/>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-lg-12 col-xl-12">
                                            <div class="form-group">
                                                <label class="">Price</label>
                                                <input type="text" class="form-control" id="price" name="price" value="{{ $book->price }}" placeholder="Enter Price" />
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-lg-12 col-xl-12">
                                            <div class="form-group">
                                                <label class="">Status</label>
                                                <select class="form-control select-picker" id="status" name="status">
                                                    <option value="1" {{($book->status == 1) ? 'selected' : ''}}>Active</option>
                                                    <option value="0" {{($book->status == 0) ? 'selected' : ''}}>In-active</option>
                                                </select>
                                            </div>
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
<script src="{{ asset('public/js/books/books-edit.js') }}"></script>
@endpush