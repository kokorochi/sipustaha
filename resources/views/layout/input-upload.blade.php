<div class="form-group {{$errors->has($passing_variable) ? 'has-error' : null}}">
    <label for="{{$passing_variable}}" class="control-label">{{$passing_description}}</label>
    <div class="clearfix"></div>
    @if($disabled == null)
        <input name="{{$passing_variable}}" id="fileinput-upload" type="file" class="file">
        @if($errors->has($passing_variable))
            <label class="error" style="display: inline-block;">
                {{$errors->first($passing_variable)}}
            </label>
        @endif
    @else
        <a href="{{url('pustahas/download-document?id=' . $pustaha['id'] . '&type=' . $passing_type)}}" class="btn btn-theme rounded">Unduh</a>
    @endif
</div>