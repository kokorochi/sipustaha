<div class="panel rounded shadow">
    <div class="panel-heading">
        <div class="pull-left">
            <h3 class="panel-title">Lampiran Permohanan Diseminasi</h3>
        </div>
        <div class="pull-right">
            <button class="btn btn-sm" data-action="collapse" data-container="body"
                    data-toggle="tooltip"
                    data-placement="top" data-title="Collapse"><i class="fa fa-angle-up"></i>
            </button>
        </div>
        <div class="clearfix"></div>
    </div><!-- /.panel-heading -->

    <div id="diseminasi" class="panel-body">

		<div class="form-group">
		    <label for="file_dissemination_ori" class="control-label">Lampiran Permohonan Bantuan Diseminasi (Cover, Biodata, Data isian, Surat Pernyataan Pengajuan)</label>
		    <div class="clearfix"></div>
		    <div class="form-group">
				<a href="{{url('diseminasi/download-document?id=' . $dissemination['id'] . '&type=1')}}" class="btn btn-theme rounded">Unduh</a> 
		    </div>
		</div>

		<div class="form-group">
		    <label for="file_iptek_ori" class="control-label">File (Bukti Penyebarluasan IPTEK)</label>
		    <div class="clearfix"></div>
		    <div class="form-group">
				<a href="{{url('diseminasi/download-document?id=' . $dissemination['id'] . '&type=2')}}" class="btn btn-theme rounded">Unduh</a> 
		    </div>
		</div>

		<div class="form-group">
		    <label for="file_presentation_ori" class="control-label">File Persentasi Seminar</label>
		    <div class="clearfix"></div>
		    <div class="form-group">
				<a href="{{url('diseminasi/download-document?id=' . $dissemination['id'] . '&type=3')}}" class="btn btn-theme rounded">Unduh</a> 
		    </div>
		</div>

		<div class="form-group">
		    <label for="file_poster_ori" class="control-label">File Poster</label>
		    <div class="clearfix"></div>
		    <div class="form-group">
				<a href="{{url('diseminasi/download-document?id=' . $dissemination['id'] . '&type=4')}}" class="btn btn-theme rounded">Unduh</a> 
		    </div>
		</div>
	</div>
</div>