<div class="form-group {{$errors->has('annotation') ? 'has-error' : null}}" >
    <label for="annotation" class="control-label">Approve Annotation</label>
    <textarea name="annotation" class="form-control"
              placeholder="Approve Annotation" {{$disabled_approv}} required>{{$approval['approval_annotation']}}</textarea>
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
                <input type='radio' class='radio-inline' id='approve_status1' {{$disabled_approv}} required  value='AC:LP' name="approve_status" {{$approval['approval_status'] == 'AC:LP' ? 'checked' : 'null'}}>
                <label class='control-label' for='approve_status1'>Accepted</label>
            </div>
            <div class='rdio radio-inline rdio-theme rounded'>
                <input type='radio' class='radio-inline' id='approve_status2' {{$disabled_approv}} required  value='RJ:LP' name="approve_status" {{$approval['approval_status'] == 'RJ:LP' ? 'checked' : 'null'}}>
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