@extends('admin.layout.main_app')
@section('title', 'Dashboard')

@push('styles')
<style>
    .small-box > .inner{
        min-height:200px;
    }
    .inner-inline-counter{
        border: 0px solid;
        height: 60px;
        display: flex;
    }
    .inner-inline-counter .inline-counter-container{
        border: 0px solid;
        height: inherit;
        display: flex;
        flex:33.33%;
        flex-direction: column;

    }
    .inner-inline-counter .inline-counter-container .inline-counter{
        border: 1px solid;
        height: inherit;
        font-size: 16px;
        font-weight: bold;
        text-align: center !important;
    }
    .inner-inline-counter .inline-counter-container .inline-counter:nth-child(2){
        border: 1px solid;
        font-size: 14px;
        font-weight: bold;
        text-align: center !important;
    }
</style>
@endpush

@section('content')
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{$data["users"]["total"] ?? 0}}</h3>
                    <p><b>Users</b></p>
                    <p class="m-0"><b>Active</b> : {{$data["users"]["active"] ?? 0}}</p>
                    <p class="m-0"><b>In-Active</b> : {{$data["users"]["inactive"] ?? 0}}</p>
                </div>
                <div class="icon">
                    <i class="fa fa-chart-pie"></i>
                </div>
                <a href="javascript;" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{$data["subscription_users"]["total"] ?? 0}}</h3>
                    <p><b>Subscription Users</b></p>
                    <p class="m-0"><b>Reader</b> : {{$data["subscription_users"]["reader"] ?? 0}}</p>
                    <p class="m-0"><b>Writer</b> : {{$data["subscription_users"]["author"] ?? 0}}</p>
                    <p class="m-0"><b>Both</b> : {{$data["subscription_users"]["author"] ?? 0}}</p>
                </div>
                <div class="icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <a href="javascript;" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{$data["subscriptions"]["total"] ?? 0}}</h3>
                    <p><b>Subscription Management</b></p>
                    <p class="m-0"><b>Total</b> : {{$data["subscriptions"]["total"] ?? 0}}</p>
                    <p class="m-0"><b>This Month</b> : {{$data["subscriptions"]["this_month"] ?? 0}}</p>
                </div>
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
                <a href="javascript;" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-pink">
                <div class="inner">
                    <h3>{{$data["books"]["total"] ?? 0}}</h3>
                    <p><b>Books</b></p>
                    <p class="m-0"><b>Total</b> : {{$data["books"]["total"] ?? 0}}</p>
                    <p class="m-0"><b>Sale</b> : {{$data["books"]["sale_count"] ?? 0}}</p>
                    <p class="m-0"><b>This Month</b> : {{$data["books"]["monthly_count"] ?? 0}}</p>
                </div>
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
                <a href="javascript;" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-pink">
                <div class="inner">
                    <h3>{{$data["contests"]["total"] ?? 0}}</h3>
                    <p><b>Contest Management</b></p>
                    <p class="m-0"><b>Total</b> : {{$data["contests"]["total"] ?? 0}}</p>
                    <p class="m-0"><b>Players</b> : {{$data["contests"]["players_count"] ?? 0}}</p>
                    <p class="m-0"><b>This Month</b> : {{$data["contests"]["monthly_count"] ?? 0}}</p>
                </div>
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
                <a href="javascript;" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->
@endsection
