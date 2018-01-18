@extends('main_layout')

@section('content')
    <!-- START @PAGE CONTENT -->
    <section id="page-content">

        <!-- Start page header -->
        <div id="tour-11" class="header-content">
            <h2><i class="fa fa-bar-chart-o"></i>Laporan</h2>
            <div class="breadcrumb-wrapper hidden-xs">
                <span class="label">Direktori Anda:</span>
                <ol class="breadcrumb">
                    <li class="active">Pustaha > Laporan</li>
                </ol>
            </div>
        </div><!-- /.header-content -->
        <!--/ End page header -->

        <!-- Start body content -->
        <div class="body-content animated fadeIn">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel rounded shadow">
                        <div class="panel-heading">
                            <div class="pull-left">
                                <h3 class="panel-title">{{$page_title}}</h3>
                            </div>
                            <div class="pull-right">
                                <button class="btn btn-sm" data-action="collapse" data-container="body"
                                        data-toggle="tooltip"
                                        data-placement="top" data-title="Collapse"><i class="fa fa-angle-up"></i>
                                </button>
                            </div>
                            <div class="clearfix"></div>
                        </div><!-- /.panel-heading -->
                        <div class="panel-body">
                            {{--action="report"--}}
                            <form id='form_report'  method="post" enctype="multipart/form-data">
                                {{csrf_field()}}
                                <div class="form-group {{$errors->has('pustaha_type') ? 'has-error' : null}}">
                                    <label for="pustaha_type" class="control-label">Jenis Pustaha</label>
                                    <select name='pustaha_type' class="form-control mb-15 select2"
                                            required>
                                        <option value="" disabled selected>-- Pilih Jenis Pustaha --</option>
                                        <option value="BUKU"> Buku </option>
                                        <option value="JURNAL-N"> Jurnal Nasional </option>
                                        <option value="JURNAL-I"> Jurnal Internasional</option>
                                        <option value="PROSIDING"> Prosiding </option>
                                        <option value="HKI"> HKI </option>
                                        <option value="PATEN" > Paten </option>
                                    </select>
                                    @if($errors->has('form_of_coop'))
                                        <label class="error" style="display: inline-block;">
                                            {{$errors->first('form_of_coop')}}
                                        </label>
                                    @endif
                                </div>
                            </form>
                        </div><!-- /.panel -->
                    </div><!-- /.body-content -->
                </div><!-- /.col-md-12 -->
            </div><!-- /.row -->
            <div class="panel rounded shadow" id="report_result">
                <div class="panel-heading">
                    <div class="pull-left">
                        <h3 class="panel-title">Hasil Laporan</h3>
                    </div>
                    <div class="pull-right">
                        <button class="btn btn-sm" data-action="collapse" data-container="body"
                                data-toggle="tooltip"
                                data-placement="top" data-title="Collapse"><i class="fa fa-angle-up"></i>
                        </button>
                    </div>
                    <div class="clearfix"></div>
                </div><!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="form-group container text-center" id="result_container" style="display: none;">
                        <a href="" id="btn-download1"><button name="filter-report" type="button" class="btn btn-lg btn-theme btn-slidedown submit"><i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                Download Report 1</button></a>
                        <a href="" id="btn-download2"><button name="filter-report" type="button" class="btn btn-lg btn-success btn-slidedown submit"><i class="fa fa-file-excel-o" aria-hidden="true"></i>
                            Download Report 2</button></a>
                    </div>
                </div><!-- /.panel -->
            </div>
            <!--/ End body content -->

            <!-- Start footer content -->
        @include('layout.footer')
        <!--/ End footer content -->

    </section><!-- /#page-content -->
    <!--/ END PAGE CONTENT -->
@endsection