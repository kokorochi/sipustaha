@extends('main_layout')

@php
    $olds = session()->getOldInput();

    if(!isset($pustaha)){
        $approval = new \App\Approval();
    }
    if(!isset($approval)){
        $approval = new \App\Approval();
    }

    foreach ($olds as $key => $old){
        if($key !== '_token'){
            $approval[$key] = old($key);
        }
    }
@endphp

@section('content')
    <!-- START @PAGE CONTENT -->
    <section id="page-content">
        <!-- Start page header -->
        <div id="tour-11" class="header-content">
            <h2><i class="fa fa-cloud-upload"></i>Approve Pustaha</h2>
            <div class="breadcrumb-wrapper hidden-xs">
                <span class="label">Direktori Anda:</span>
                <ol class="breadcrumb">
                    <li class="active">Approve Pustaha > Approve</li>
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
                        <div id="pustaha-container" class="panel-body">

                            @if($upd_mode != 'create')
                                <input name="id" type="hidden" value="{{$pustaha['id']}}">
                            @endif
                            <input name="upd_mode" type="hidden" value="{{$upd_mode}}" disabled>
                            <div class="form-group {{$errors->has('research_id') ? 'has-error' : null}}">
                                <label for="research_id" class="control-label">Judul Penelitian</label>
                                <input name="research_full" type="text" class="form-control search-research" value="{{$pustaha['research_full']}}"
                                       placeholder="Judul Penelitian" {{$upd_mode == 'edit' ? 'disabled' : null}} required>
                                <input name="research_id" type="hidden" value="{{$pustaha['research_id']}}">
                                @if($errors->has('research_id'))
                                    <label class="error" style="display: inline-block;">
                                        {{$errors->first('research_id')}}
                                    </label>
                                @endif
                            </div>
                            
                            <div class="form-group {{$errors->has('pustaha_type') ? 'has-error' : null}}">
                                <label for="pustaha_type" class="control-label">Jenis Pustaha</label>
                                <select name='pustaha_type' class="form-control mb-15 select2"
                                        {{$disabled}} required>
                                    <option value="" disabled selected>-- Pilih Jenis Pustaha --</option>
                                    <option value="BUKU" {{$pustaha['pustaha_type'] == 'BUKU' ? 'selected' : null}}>
                                        Buku
                                    </option>
                                    <option value="JURNAL-N" {{$pustaha['pustaha_type'] == 'JURNAL-N' ? 'selected' : null}}>
                                        Jurnal Nasional
                                    </option>
                                    <option value="JURNAL-I" {{$pustaha['pustaha_type'] == 'JURNAL-I' ? 'selected' : null}}>
                                        Jurnal Internasional
                                    </option>
                                    <option value="PROSIDING-N" {{$pustaha['pustaha_type'] == 'PROSIDING-N' ? 'selected' : null}}>
                                        Prosiding Nasional</option>
                                    <option value="PROSIDING-I" {{$pustaha['pustaha_type'] == 'PROSIDING-I' ? 'selected' : null}}>
                                        Prosiding Internasional
                                    </option>
                                    <option value="HKI" {{$pustaha['pustaha_type'] == 'HKI' ? 'selected' : null}}>
                                        HKI
                                    </option>
                                    <option value="PATEN" {{$pustaha['pustaha_type'] == 'PATEN' ? 'selected' : null}}>
                                        Paten
                                    </option>
                                </select>
                                @if($errors->has('form_of_coop'))
                                    <label class="error" style="display: inline-block;">
                                        {{$errors->first('form_of_coop')}}
                                    </label>
                                @endif
                            </div>

                            @include('pustaha.book-detail')

                            @include('pustaha.journal-detail')

                            @include('pustaha.proceeding-detail')

                            @include('pustaha.hki-detail')

                            @include('pustaha.patent-detail')
                        </div><!-- /.panel -->
                    </div>

                    @if($type == 'lp')
                        @include('pustaha.diseminasi-detail')
                    @endif

                    <div class="panel rounded shadow">
                        <div class="panel-heading">
                            <div class="pull-left">
                                <h3 class="panel-title">Approval Action</h3>
                            </div>
                            <div class="pull-right">
                                <button class="btn btn-sm" data-action="collapse" data-container="body"
                                        data-toggle="tooltip"
                                        data-placement="top" data-title="Collapse"><i class="fa fa-angle-up"></i>
                                </button>
                            </div>
                            <div class="clearfix"></div>
                        </div><!-- /.panel-heading -->

                        <div id="approval" class="panel-body">
                            <form action="{{url($action_url)}}" method="post" enctype="multipart/form-data">
                                {{csrf_field()}}
                                <input name="type" type="hidden" value="{{$type}}">
                                <input name="pustaha_id" type="hidden" value="{{$pustaha['id']}}">
                                
                                @if($type == 'lp')
                                    @include('approval.approval-dissemination')
                                @else
                                    @include('approval.approval-incentive')
                                @endif

                            </form>
                        </div>
                    </div><!-- /.body-content -->
                </div><!-- /.col-md-12 -->
            </div><!-- /.row -->
        </div>
        <!--/ End body content -->

        <!-- Start footer content -->
    @include('layout.footer')
    <!--/ End footer content -->

    </section><!-- /#page-content -->
    <!--/ END PAGE CONTENT -->
@endsection