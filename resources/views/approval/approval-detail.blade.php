@extends('main_layout')

@php
    $olds = session()->getOldInput();

    if(!isset($pustaha)){
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
                                    <option value="PROSIDING" {{$pustaha['pustaha_type'] == 'PROSIDING' ? 'selected' : null}}>
                                        Prosiding
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
                                <div class="form-group {{$errors->has('incentive_id') ? 'has-error' : null}}">
                                    <label for="incentive_id" class="control-label">Pilih Incentive</label>
                                    <select name="incentive_id" class="form-control select2" style="width: 100%;" {{$disabled_approv}} required>
                                        <option value="" disabled selected>-- Pilih Incentive --</option>
                                        @foreach($incentive_ids as $incentive_id)
                                            <option value="{{$incentive_id['id']}}">
                                                    {{$incentive_id['description']}}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('incentive_id'))
                                        <label class="error" style="display: inline-block;">
                                            {{$errors->first('incentive_id')}}
                                        </label>
                                    @endif
                                </div>

                                <div class="form-group {{$errors->has('annotation') ? 'has-error' : null}}" >
                                    <label for="annotation" class="control-label">Approve Annotation</label>
                                    <textarea name="annotation" class="form-control"
                                              placeholder="Approve Annotation" {{$disabled_approv}} required></textarea>
                                    @if($errors->has('annotation'))
                                        <label class="error" style="display: inline-block;">
                                            {{$errors->first('annotation')}}
                                        </label>
                                    @endif
                                </div>
                                <div class="form-group {{$errors->has('approve_status') ? 'has-error' : null}}">
                                    <label for="name-survey" class="control-label">Approve Status </label>
                                        <div>
                                            <div class='rdio radio-inline rdio-theme rounded'>
                                                @if($type == 'wr3')
                                                    <input type='radio' class='radio-inline' id='approve_status1' {{$disabled_approv}} required  value='AC:WR3' name="approve_status">
                                                    @elseif($type == 'lp')
                                                    <input type='radio' class='radio-inline' id='approve_status1' {{$disabled_approv}} required  value='AC:LP' name="approve_status">
                                                @endif
                                                <label class='control-label' for='approve_status1'>Accepted</label>
                                            </div>
                                            <div class='rdio radio-inline rdio-theme rounded'>
                                                @if($type == 'wr3')
                                                    <input type='radio' class='radio-inline' id='approve_status2' {{$disabled_approv}} required  value='RJ:WR3' name="approve_status">
                                                @elseif($type == 'lp')
                                                    <input type='radio' class='radio-inline' id='approve_status2' {{$disabled_approv}} required  value='RJ:LP' name="approve_status">
                                                @endif
                                                <label class='control-label' for='approve_status2'>Rejected</label>
                                            </div>
                                        </div>
                                        @if($errors->has('approve_status'))
                                            <label class="error" style="display: inline-block;">
                                                {{$errors->first('approve_status')}}
                                            </label>
                                        @endif
                                </div>
                                
                                <div class="panel-footer">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <button id="approval-submit" class="btn btn-success rounded" type="submit">Submit
                                                </button>
                                                <a href="{{url('/approvals')}}" class="btn btn-danger rounded">Batal</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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