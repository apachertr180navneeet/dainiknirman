@extends('admin.layout.main_app')
@section('title', 'Contest Author')

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
                    <form role="form" action="{{ route('admin.contest-authors.update', ['contest_id' => $contestAuthor->contest_id, 'id' => $contestAuthor->id]) }}" method="post" id="edit-contest-author-form" enctype="multipart/form-data">
                        @csrf

                        @php
                        $disableRankSelection = '';
                        $hideSubmit = false;

                        if(!empty($contestAuthor->rank)){
                            $disableRankSelection = 'disabled';
                            $hideSubmit = true;
                        }
                        @endphp
                        <!-- Card body -->
                        <div class="card-body">
                            <!-- Hidden input -->
                            <input type="hidden" name="contest_author_id" id="contest_author_id" value="{{ $contestAuthor->id }}">
                            <input type="hidden" name="contest_id" id="contest_id" value="{{ $contestAuthor->contest_id }}">
                            <!-- Hidden input -->

                            {{-- <div class="row row-sm">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Contest Title</label>
                                        <input type="text" class="form-control" id="title" name="title" value="{{ $contestAuthor->contest_title }}" placeholder="Enter Contest Title" disabled/>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Date</label>
                                        @php
                                        $date = date("d-m-Y", strtotime($contestAuthor->contest_date));
                                        @endphp
                                        <input type="text" class="form-control date-picker" id="date" name="date" value="{{ $date }}" placeholder="Enter contest Date" disabled/>
                                    </div>
                                </div>
                            </div>
                            <div class="row row-sm">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Player Name</label>
                                        <input type="text" class="form-control" id="player_name" name="player_name" value="{{ $contestAuthor->author_name }}" placeholder="Enter Player" disabled/>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Rank</label>
                                        <select class="form-control select-picker" id="rank" name="rank"  data-check-url="{{route('admin.contest-authors.checkContestRank')}}" {{$disableRankSelection}}>
                                            <option value="1" {{($contestAuthor->rank == 1) ? 'selected' : ''}}>1</option>
                                            <option value="2" {{($contestAuthor->rank == 2) ? 'selected' : ''}}>2</option>
                                            <option value="3" {{($contestAuthor->rank == 3) ? 'selected' : ''}}>3</option>
                                        </select>
                                    </div>
                                </div>
                            </div> --}}
                            <div class="row row-sm">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Contest Title</label>
                                        <input type="text" class="form-control" id="title" name="title" value="{{ $contestAuthor->contest_title }}" placeholder="Enter Contest Title" disabled/>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">User Title</label>
                                        <input type="text" class="form-control" id="user_contest_title" name="user_contest_title" value="{{ $contestAuthor->title }}" placeholder="Enter User Contest Title" disabled/>
                                    </div>
                                </div>
                            </div>
                            <div class="row row-sm">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Date</label>
                                        @php
                                        $date = date("d-m-Y", strtotime($contestAuthor->contest_date));
                                        @endphp
                                        <input type="text" class="form-control date-picker" id="date" name="date" value="{{ $date }}" placeholder="Enter contest Date" disabled/>
                                    </div>
                                </div>
                            <!-- </div>
                            <div class="row row-sm"> -->
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Player Name</label>
                                        <input type="text" class="form-control" id="player_name" name="player_name" value="{{ $contestAuthor->author_name }}" placeholder="Enter Player" disabled/>
                                    </div>
                                </div>
                            </div>
                            <div class="row row-sm">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Rank</label>
                                        <select class="form-control select-picker" id="rank" name="rank"  data-check-url="{{route('admin.contest-authors.checkContestRank')}}" {{$disableRankSelection}}>
                                            <option value="1" {{($contestAuthor->rank == 1) ? 'selected' : ''}}>1</option>
                                            <option value="2" {{($contestAuthor->rank == 2) ? 'selected' : ''}}>2</option>
                                            <option value="3" {{($contestAuthor->rank == 3) ? 'selected' : ''}}>3</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row row-sm">
                                <div class="col-md-12 col-lg-12 col-xl-12">
                                    <div class="form-group">
                                        <label class="">Contest Remark</label>
                                        <textarea class="form-control" id="remark" name="remark" placeholder="Contest Remark" disabled>{{$contestAuthor->remark ?? ''}}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row row-sm">
                                <div class="col-md-12 col-lg-12 col-xl-12">
                                    <div class="form-group">
                                        <label class="">Contest Description</label>
                                        <textarea class="form-control" id="description" name="description" placeholder="Contest Description" disabled rows="10">{{$contestAuthor->description ?? ''}}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row row-sm">
                                <div class="col-md-12 col-lg-12 col-xl-12">
                                    <div class="form-group">
                                        <label class="">Admin Remark</label>
                                        <textarea class="form-control" id="admin_remark" name="admin_remark" placeholder="Enter Admin Remark">{{$contestAuthor->admin_remark ?? ''}}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row row-sm">
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Terms Accepted</label><br/>
                                        @if($contestAuthor->is_accept_terms == 1)
                                        Yes
                                        @else
                                        No
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6 col-xl-6">
                                    <div class="form-group">
                                        <label class="">Status</label>
                                        <select class="form-control select-picker" id="status" name="status">
                                            <option value="1" {{($contestAuthor->status == 1) ? 'selected' : ''}}>Active</option>
                                            <option value="0" {{($contestAuthor->status == 0) ? 'selected' : ''}}>In-active</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <!-- Card footer -->
                        @if(!$hideSubmit)
                        <div class="card-footer">
                            <div class="row row-sm">
                                <div class="col-md-12 col-lg-12 col-xl-12 text-right">
                                    <div class="form-group">
                                        <a href="{{route('admin.contest-authors.index', ['contest_id' => $contestAuthor->contest_id])}}" class="btn btn-info">Cancel</a>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
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
<script src="{{ asset('public/js/contest-authors/contest-authors-edit.js') }}"></script>
@endpush