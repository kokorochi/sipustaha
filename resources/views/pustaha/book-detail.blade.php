<div id="book-container" class="detail-container">
    <div class="form-group">
        <label for="author" class="control-label">Penulis</label>
        <input name="author" class="form-control" type="text"
               value="{{$pustaha['author']}}" disabled>
    </div>

    @include('pustaha.item-table')
    @include('layout.input-textarea', ['passing_variable' => 'title', 'passing_description' => 'Judul'])
    @include('layout.input-date', ['passing_variable' => 'pustaha_date', 'passing_description' => 'Tahun Terbit'])
    @include('layout.input-text', ['passing_variable' => 'city', 'passing_description' => 'Kota'])
    @include('layout.input-text', ['passing_variable' => 'country', 'passing_description' => 'Negara'])
    @include('layout.input-text', ['passing_variable' => 'publisher', 'passing_description' => 'Publisher'])
    @include('layout.input-text', ['passing_variable' => 'editor', 'passing_description' => 'Editor'])
    @include('layout.input-text', ['passing_variable' => 'issue', 'passing_description' => 'Edisi'])
    @include('layout.input-text', ['passing_variable' => 'isbn_issn', 'passing_description' => 'ISBN'])
    @include('layout.input-upload', ['passing_variable' => 'file_name_ori', 'passing_description' => 'File (Cover, Daftar Isi)', 'passing_type' => '1'])
    {{-- @include('layout.input-upload', ['passing_variable' => 'file_claim_request_ori', 'passing_description' => 'File (Surat Permohonan)', 'passing_type' => '2'])
    @include('layout.input-upload', ['passing_variable' => 'file_claim_accomodation_ori', 'passing_description' => 'File (Bukti Penyebarluasan IPTEK)', 'passing_type' => '3'])
    @include('layout.input-upload', ['passing_variable' => 'file_certification_ori', 'passing_description' => 'File (1 halaman Cover Depan)', 'passing_type' => '4']) --}}

</div>