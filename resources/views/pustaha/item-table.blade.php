@if($upd_mode != "display")
    <div class="form-group">
        <label for="author" class="control-label">Penulis Selanjutnya / Co-Author</label>
        <div class="clearfix"></div>
        <a href="#" class="btn btn-theme btn-md rounded table-add" data-toggle="tooltip" title="Tambah Co-Author"><i class="fa fa-plus"></i></a>
    </div>
@endif
<div class="form-group table-responsive item-table" id="item-table">
    <table class="table">
        <thead>
        <th class="text-center">Dosen Luar</th>
        <th class="text-center">NIP/NIDN/Nama (Dosen USU)</th>
        <th class="text-center">Nama</th>
        <th class="text-center">Afiliasi</th>
        <th class="text-center">Hapus</th>
        </thead>
        <tbody>
        @foreach($pustaha_items as $pustaha_item)
            <tr class="text-center">
                <td>

                    <input name="item_external[]" type="checkbox" class="form-control" value="X"
                           {{$pustaha_item['item_external'] == null ? null : "checked"}}
                           >

                </td>
                <td>
                    <input name="item_username_display[]" type="text" class="form-control search-employee"
                           value="{{$pustaha_item['item_username_display']}}"
                            {{$pustaha_item['item_external'] == null ? null : "disabled"}}>
                    <input name="item_username[]" type="hidden" value="{{$pustaha_item['item_username']}}">
                </td>
                <td>
                    <input name="item_name[]" type="text" class="form-control"
                           value="{{$pustaha_item['item_name']}}"
                            {{$pustaha_item['item_external'] == null ? "disabled" : null}}>
                </td>
                <td>
                    <input name="item_affiliation[]" type="text" class="form-control"
                           value="{{$pustaha_item['item_affiliation']}}"
                            {{$pustaha_item['item_external'] == null ? "disabled" : null}}>
                </td>
                <td>
                    @if($upd_mode != "display")
                        <a href="#" class="table-remove btn btn-danger rounded"><i class="fa fa-trash"></i></a>
                    @endif
                </td>
            </tr>
        @endforeach
        <tr class="hide text-center">
            <td>
                <input name="item_external[]" type="checkbox" class="form-control" disabled>
            </td>
            <td>
                <input name="item_username_display[]" type="text" class="form-control search-employee" disabled>
                <input name="item_username[]" type="hidden" value="" disabled>
            </td>
            <td>
                <input name="item_name[]" type="text" class="form-control" disabled>
            </td>
            <td>
                <input name="item_affiliation[]" type="text" class="form-control" disabled>
            </td>
            <td>
                <a href="#" class="table-remove btn btn-danger rounded"><i class="fa fa-trash"></i></a>
            </td>
        </tr>
        </tbody>
    </table>
</div>