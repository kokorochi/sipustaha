<div class="form-group {{$errors->has($passing_variable) ? 'has-error' : null}}">

    @if($upd_mode == 'edit')
        <label for="{{$passing_variable}}" class="control-label">{{$passing_description}}
            <label class="text-danger"> Kosongkan jika file tidak diupdate</label>
        </label>
    @else
        <label for="{{$passing_variable}}" class="control-label">{{$passing_description}}</label>
    @endif

    <div class="clearfix"></div>
    @if($disabled == null)
        <input name="{{$passing_variable}}" id="fileinput-upload" type="file" class="file" accept=".pdf">
        @if($errors->has($passing_variable))
            <label class="error" style="display: inline-block;">
                {{$errors->first($passing_variable)}}
            </label>
        @endif
    @elseif(isset($diseminasi))
        <a href="{{url('diseminasi/download-document?id=' . $diseminasi['id'] . '&type=' . $passing_type)}}" class="btn btn-theme rounded">Unduh</a>
    @else
        <a href="{{url('pustahas/download-document?id=' . $pustaha['id'] . '&type=' . $passing_type)}}" class="btn btn-theme rounded">Unduh</a>
    @endif
</div>