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
        {{--<div class="row">
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{$data["category"]["total"] ?? 0}}</h3>
                    <p><b>Categories</b></p>
                    <p class="m-0"><b>Active</b> : {{$data["category"]["active"] ?? 0}}</p>
                    <p class="m-0"><b>In-Active</b> : {{$data["category"]["inactive"] ?? 0}}</p>
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
                    <h3>{{$data["service"]["total"] ?? 0}}</h3>
                    <p><b>Services</b></p>
                    <p class="m-0"><b>Active</b> : {{$data["service"]["active"] ?? 0}}</p>
                    <p class="m-0"><b>In-Active</b> : {{$data["service"]["inactive"] ?? 0}}</p>
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
                    <h3>{{$data["agent"]["total"] ?? 0}}</h3>
                    <p><b>Agents</b></p>
                    <p class="m-0"><b>Active</b> : {{$data["agent"]["active"] ?? 0}}</p>
                    <p class="m-0"><b>In-Active</b> : {{$data["agent"]["inactive"] ?? 0}}</p>
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
                    <h3>{{$data["distributor"]["total"] ?? 0}}</h3>
                    <p><b>Distributors</b></p>
                    <p class="m-0"><b>Active</b> : {{$data["distributor"]["active"] ?? 0}}</p>
                    <p class="m-0"><b>In-Active</b> : {{$data["distributor"]["inactive"] ?? 0}}</p>
                </div>
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
                <a href="javascript;" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-6 col-sm-12 col-xs-12">
                <!-- small box -->
                <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{$data["customer"]["total"] ?? 0}}</h3>
                    <p><b>Customers</b></p>
                    <p class="m-0"><b>Active</b> : {{$data["customer"]["active"] ?? 0}}</p>
                    <p class="m-0"><b>In-Active</b> : {{$data["customer"]["inactive"] ?? 0}}</p>
                    <div class="inner-inline-counter">
                        <div class="inline-counter-container">
                            <div class="inline-counter">{{$data["customer"]["itrPending"] ?? 0}}</div>
                            <div class="inline-counter">Pending</div>
                        </div>
                        <div class="inline-counter-container">
                            <div class="inline-counter">{{$data["customer"]["itrProcess"] ?? 0}}</div>
                            <div class="inline-counter">Process</div>
                        </div>
                        <div class="inline-counter-container">
                            <div class="inline-counter">{{$data["customer"]["itrInsufficiency"] ?? 0}}</div>
                            <div class="inline-counter">Doc. Insufficiency</div>
                        </div>
                        <div class="inline-counter-container">
                            <div class="inline-counter">{{$data["customer"]["itrQuery"] ?? 0}}</div>
                            <div class="inline-counter">Dep. Query</div>
                        </div>
                        <div class="inline-counter-container">
                            <div class="inline-counter">{{$data["customer"]["itrComplete"] ?? 0}}</div>
                            <div class="inline-counter">Complete</div>
                        </div>
                    </div>
                </div>
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
                <a href="javascript;" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>--}}
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->
@endsection
