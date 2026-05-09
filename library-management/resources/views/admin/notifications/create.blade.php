@extends('admin.layout.main_app')
@section('title', 'Notifications')

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
                        <form role="form" action="{{ route('admin.notifications.add') }}" method="post" id="add-book-form" enctype="multipart/form-data">
                            @csrf
                            <!-- Card body -->
                            <div class="card-body">
                                <div class="row row-sm">
                                    <div class="col-md-6 col-lg-6 col-xl-6">
                                        <div class="form-group">
                                            <label class="">User Type</label>
                                            <select class="form-control select-picker" id="user_type" name="user_type" onchange="ajaxGetSendToUsers();">
                                                <option value="">All</option>
                                                <option value="{{config('constants.roles.AUTHOR.value')}}">Author</option>
                                                <option value="{{config('constants.roles.READER.value')}}">Reader</option>
                                                <option value="{{config('constants.roles.AUTHOR_READER.value')}}">Both</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row row-sm">
                                    <div class="col-md-12 col-lg-12 col-xl-12">
                                        <label class="">Send To</label>
                                        <div class="form-group">
                                            <input type="radio" name="sendto" id="sendtoall" value="all" checked="true" onchange="ajaxGetSendToUsers();" />&nbsp;<label for="sendtoall">All</label>&nbsp;&nbsp;
                                            <input type="radio" name="sendto" id="sendtocustom" value="custom" onchange="ajaxGetSendToUsers();" />&nbsp;<label for="sendtocustom">Select</label>&nbsp;&nbsp;
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-lg-12 col-xl-12" id="custom-users" style="display: none;">
                                        <div class="form-group">
                                            <label class="">Users</label>
                                            <div id="sendtocustomlist"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row row-sm">
                                    <div class="col-md-12 col-lg-12 col-xl-12">
                                        <label class="">Notification Type</label>
                                        <div class="form-group">
                                            <input type="radio" name="sendtype" id="sendpush" value="push" checked onclick="showeditor('P')" />&nbsp;
                                            <label for="sendpush">Push</label>
                                        </div>
                                    </div>
                                </div>
                                

                                <div class="row row-sm">
                                    <div class="col-md-12 col-lg-12 col-xl-12">
                                        <div class="form-group">
                                            <label class="">Message</label>
                                            <textarea name="message" id="message_short" class="form-control" required="true" maxlength="180"></textarea>
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
<script>
$(document).ready(function(){
    setTimeout(() => {
        Books.getAuthors();
    }, 500);
});

function ajaxGetSendToUsers()
{
  var sendto_type = $("[name='sendto']:checked").val();
  var sendtype = $("[name='sendtype']:checked").val();
  var usertype = usertype = $("#user_type").val();

  if(sendto_type == "custom")
  {
    $.ajax({
      url:"<?php echo route('admin.notifications.ajaxGetSendTo') ?>",
      data:{usertype: usertype},
      type:"POST",
      success:function(result){
        if(result.status){
            $("#custom-users").show();
            $("#sendtocustomlist").html(result.data);
            $("#userselect").selectpicker({
                liveSearch: true,
                selectedTextFormat: "count > 2",
                size: "8",
                iconBase: "fontawesome",
                tickIcon: "fa fa-check"
            });
        }
      }
    });
  }

  if(sendto_type == "all")
  {
    $("#custom-users").hide();
    $("#sendtocustomlist").html("");
  }
}
</script>
@endpush