<div id="journal-container" class="detail-container">
    <div class="form-group">
        <label for="author" class="control-label">Author</label>
        <input name="author" class="form-control" type="text"
               value="{{$pustaha['author']}}" disabled>
    </div>

    @include('pustaha.item-table')

    @include('layout.input-text', ['passing_variable' => 'title', 'passing_description' => 'Judul'])
    @include('layout.input-text', ['passing_variable' => 'name', 'passing_description' => 'Nama Jurnal'])
    @include('layout.input-date', ['passing_variable' => 'pustaha_date', 'passing_description' => 'Tahun Terbit'])
    @include('layout.input-text', ['passing_variable' => 'pages', 'passing_description' => 'Halaman'])
    @include('layout.input-text', ['passing_variable' => 'volume', 'passing_description' => 'Volume'])
    @include('layout.input-text', ['passing_variable' => 'issue', 'passing_description' => 'Issue/Nomor'])
    @include('layout.input-text', ['passing_variable' => 'isbn_issn', 'passing_description' => 'ISSN'])
    @include('layout.input-text', ['passing_variable' => 'url_address', 'passing_description' => 'Link Online'])
    @include('layout.input-upload', ['passing_variable' => 'file_name_ori', 'passing_description' => 'File(Cover Jurnal, Daftar Isi, Halaman Artikel Jurnal yang bersangkutan, Acceptance Letter)', 'passing_type' => '1'])
    @include('layout.input-upload', ['passing_variable' => 'file_claim_request_ori', 'passing_description' => 'File (Surat Permohonan)', 'passing_type' => '2'])
    @include('layout.input-upload', ['passing_variable' => 'file_claim_accomodation_ori', 'passing_description' => 'File (Bukti Penyebarluasan IPTEK)', 'passing_type' => '3'])
    @include('layout.input-upload', ['passing_variable' => 'file_certification_ori', 'passing_description' => 'File (Sertifikasi)', 'passing_type' => '4'])

</div>