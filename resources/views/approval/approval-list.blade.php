@extends('main_layout')

@section('content')

    <!-- START @PAGE CONTENT -->
    <section id="page-content">

       <!-- Start page header -->
        <div id="tour-11" class="header-content">
            <h2><i class="fa fa-cloud-upload"></i>Approve Pustaha</h2>
            <div class="breadcrumb-wrapper hidden-xs">
                <span class="label">Direktori Anda:</span>
                <ol class="breadcrumb">
                    <li class="active">Approve Pustaha</li>
                </ol>
            </div>
        </div><!-- /.header-content -->
        <!--/ End page header -->

        <!-- Start body content -->
        <div class="body-content animated fadeIn">
            <div class="panel rounded shadow">
                <div class="panel-heading">
                    <div class="pull-left">
                        <h3 class="panel-title">Daftar Pustaha</h3>
                    </div>
                    <div class="pull-right">
                        <button class="btn btn-sm" data-action="collapse" data-container="body" data-toggle="tooltip"
                                data-placement="top" data-title="Collapse"><i class="fa fa-angle-up"></i></button>
                    </div>
                    <div class="clearfix"></div>
                </div><!-- /.panel-heading -->
                
                @if(!empty($auths))
                    {{-- {{dd($auths)}} --}}
                    <input type="hidden" id="auths" value="{{$auths}}">
                    {{csrf_field()}}
                @endif
                
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive mb-20">
                                <table id="approval-pustaha-list" class="table table-striped table-theme">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>No.</th>
                                        <th>Jenis Pustaha</th>
                                        <th>Judul</th>
                                        <th>Tanggal</th>
                                        <th>ISBN/ISSN</th>
                                        <th>Owner</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div><!-- /.row -->
                </div>
            </div>

        </div><!-- /.body-content -->
        <!--/ End body content -->

        @include('layout.footer')

    </section><!-- /#page-content -->
    <!--/ END PAGE CONTENT -->
@endsection