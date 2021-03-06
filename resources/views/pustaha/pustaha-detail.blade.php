@extends('main_layout')

@php
    $olds = session()->getOldInput();
    foreach ($olds as $key => $old)
    {
        if($key !== '_token' &&
            $key != 'item_external' &&
            $key != 'item_username_display' &&
            $key != 'item_username' &&
            $key != 'item_name' &&
            $key != 'item_affiliation'
        )
        {
            $pustaha[$key] = old($key);
        }
    }

    $ctr = 0;
    if(old('item_external.0'))
        $pustaha_items = new \Illuminate\Database\Eloquent\Collection();
    while(old('item_external.' . $ctr))
    {
        $pustaha_item = new \App\PustahaItem();
        $pustaha_item['item_external'] = old('item_external.' . $ctr);
        $pustaha_item['item_username_display'] = old('item_username_display.' . $ctr);
        $pustaha_item['item_username'] = old('item_username.' . $ctr);
        $pustaha_item['item_name'] = old('item_name.' . $ctr);
        $pustaha_item['item_affiliation'] = old('item_affiliation.' . $ctr);
        $pustaha_items->push($pustaha_item);
        $ctr++;
    }

    if(!isset($edit))
        $edit = false;
    if(!isset($pustaha)){
        $pustaha = new \App\Pustaha();
    }
    if(!isset($pustaha_items)){
        $pustaha_items = new \Illuminate\Support\Collection();
    }
@endphp

@section('content')
    <!-- START @PAGE CONTENT -->
    <section id="page-content">

        <!-- Start page header -->
        <div id="tour-11" class="header-content">
            <h2><i class="fa fa-cloud-upload"></i>Pustaha</h2>
            <div class="breadcrumb-wrapper hidden-xs">
                <span class="label">Direktori Anda:</span>
                <ol class="breadcrumb">
                    <li class="active">Pustaha > Tambah</li>
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
                            @if($upd_mode == 'display')
                                <div class="form-group">
                                    <h5>History Status: </h5>
                                    @foreach($approvales as $approval)
                                        <div class="col-md-12">
                                            @if($approval->code!="SB" && $approval->code!="UP")
                                                <li class='text-danger'>
                                                    <i>{{$approval->approval_status}} - {{$approval->approval_annotation}}</i>
                                                </li>
                                            @else
                                                <li class='text-danger'>
                                                    <i>{{$approval->approval_status}}</i>
                                                </li>   
                                            @endif
                                        </div>
                                    @endforeach

                                    @if($edit)
                                        <a href="{{url('pustahas/edit?id=' . $pustaha->id)}}" class="btn btn-success rounded">Ubah</a>
                                    @endif
                                    <a href="{{url('/')}}" class="btn btn-danger rounded">Batal</a>
                                </div>
                            @endif
                            <form action="{{url($action_url)}}" method="post" enctype="multipart/form-data">
                                {{csrf_field()}}
                                @if($upd_mode != 'create')
                                    <input name="id" type="hidden" value="{{$pustaha['id']}}">
                                    <input name="research_id" type="hidden" value="{{$pustaha['research_id']}}">
                                    <input name="pustaha_type" type="hidden" value="{{$pustaha['pustaha_type']}}">
                                @endif
                                <input name="upd_mode" type="hidden" value="{{$upd_mode}}" disabled>
                                <div class="form-group is_simpel">
                                    Publikasi terkait dengan data di Sistem Penelitian (SIMPEL) ? 
                                    <input type="checkbox" class="switch" name="is_simpel" 
                                    @if($upd_mode == 'create') @php $pustaha['is_simpel']= 1; @endphp @endif
                                 {{$pustaha['is_simpel'] == '1' ? 'checked' : 'null'}} data-on-text="YA" data-off-text="TIDAK" data-on-color="teal" id="is_simpel">
                                </div>
                                <div id="research" {{$pustaha['is_simpel'] == '1' ? "null" : "style=display:none"}} class="form-group {{$errors->has('research_id') ? 'has-error' : null}}">
                                    <label for="research_id" class="control-label">Judul Penelitian</label>
                                    @if($auth == 'SU')
                                        <input name="research_full" type="text" class="form-control search-research" value="{{$pustaha['research_full']}}"
                                           placeholder="Judul Penelitian" {{$upd_mode == 'edit' ? 'disabled' : null}}>
                                        <input name="research_id" type="hidden" value="{{$pustaha['research_id']}}">
                                    @else
                                        <select name='research_id' class="form-control mb-15 select2"
                                            {{$disabled}} {{$upd_mode == 'edit' ? 'disabled' : null}}>
                                            <option value="" disabled selected>-- Pilih Judul Penelitian --</option>
                                            @if(!empty($researches))
                                                @foreach($researches as $research)
                                                    <option value="{{$research->id}}" {{$research->id == $pustaha['research_id'] ? 'selected' : null}}>Author : {{$research->author}}, Judul : {{$research->title}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    @endif
                                    
                                    @if($errors->has('research_id'))
                                        <label class="error" style="display: inline-block;">
                                            {{$errors->first('research_id')}}
                                        </label>
                                    @endif
                                </div>

                                <div class="form-group {{$errors->has('pustaha_type') ? 'has-error' : null}}">
                                    <label for="pustaha_type" class="control-label">Jenis Pustaha</label>
                                    <select name='pustaha_type' class="form-control mb-15 select2"
                                            {{$disabled}} {{$upd_mode == 'edit' ? 'disabled' : null}} required>
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

                                @if($upd_mode == 'edit')
                                    <input type="hidden" name="_method" value="PUT">
                                @endif
                                {{csrf_field()}}

                                <div class="panel-footer">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                @if($disabled == null)
                                                    <button id="pustaha-submit" class="btn btn-success rounded"
                                                            type="submit">Submit
                                                    </button>
                                                @endif
                                                <a href="{{url('/')}}" class="btn btn-danger rounded">Batal</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div><!-- /.panel -->
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