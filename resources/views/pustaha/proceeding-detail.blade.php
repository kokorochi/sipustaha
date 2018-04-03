<div id="proceeding-container" class="detail-container">
    <div class="form-group">
        <label for="author" class="control-label">Author</label>
        <input name="author" class="form-control" type="text"
               value="{{$pustaha['author']}}" disabled>
    </div>

    @include('pustaha.item-table')

    @include('layout.input-text', ['passing_variable' => 'publisher', 'passing_description' => 'Pemakalah'])
    @include('layout.input-text', ['passing_variable' => 'title', 'passing_description' => 'Judul'])
    @include('layout.input-text', ['passing_variable' => 'name', 'passing_description' => 'Nama Seminar'])
    @include('layout.input-date', ['passing_variable' => 'pustaha_date', 'passing_description' => 'Tanggal Seminar'])
    @include('layout.input-text', ['passing_variable' => 'city', 'passing_description' => 'Kota'])
    @include('layout.input-text', ['passing_variable' => 'country', 'passing_description' => 'Negara'])
    @include('layout.input-text', ['passing_variable' => 'pages', 'passing_description' => 'Halaman'])
    @include('layout.input-text', ['passing_variable' => 'isbn_issn', 'passing_description' => 'ISBN'])
    @include('layout.input-text', ['passing_variable' => 'url_address', 'passing_description' => 'Link Online'])
    @include('layout.input-upload', ['passing_variable' => 'file_name_ori', 'passing_description' => 'File(Cover, Daftar Isi, Halaman Artikel Prosiding yang bersangkutan, Sertifikat Pemakalah, Acceptance Letter)', 'passing_type' => '1'])
    {{-- @include('layout.input-upload', ['passing_variable' => 'file_claim_request_ori', 'passing_description' => 'File (Surat Permohonan)', 'passing_type' => '2'])
    @include('layout.input-upload', ['passing_variable' => 'file_claim_accomodation_ori', 'passing_description' => 'File (Bukti Penyebarluasan IPTEK)', 'passing_type' => '3'])
    @include('layout.input-upload', ['passing_variable' => 'file_certification_ori', 'passing_description' => 'File (Sertifikasi)', 'passing_type' => '4']) --}}

</div>