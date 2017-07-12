<div id="patent-container" class="detail-container">

    @include('layout.input-text', ['passing_variable' => 'propose_no', 'passing_description' => 'Nomor Pemohon'])
    @include('layout.input-text', ['passing_variable' => 'pustaha_date', 'passing_description' => 'Tanggal'])
    @include('layout.input-date', ['passing_variable' => 'creator_name', 'passing_description' => 'Nama Pencipta'])
    @include('layout.input-textarea', ['passing_variable' => 'creator_address', 'passing_description' => 'Alamat Pencipta'])
    @include('layout.input-text', ['passing_variable' => 'creator_citizenship', 'passing_description' => 'Kewarganegaraan Pencipta'])
    @include('layout.input-text', ['passing_variable' => 'owner_name', 'passing_description' => 'Nama Pemegang Hak Cipta'])
    @include('layout.input-textarea', ['passing_variable' => 'owner_address', 'passing_description' => 'Alamat Pemegang Hak Cipta'])
    @include('layout.input-text', ['passing_variable' => 'owner_citizenship', 'passing_description' => 'Kewarganegaraan Pemegang Hak Cipta'])
    @include('layout.input-text', ['passing_variable' => 'creation_type', 'passing_description' => 'Jenis Ciptaan'])
    @include('layout.input-text', ['passing_variable' => 'title', 'passing_description' => 'Judul Ciptaan'])
    @include('layout.input-date', ['passing_variable' => 'announcement_date', 'passing_description' => 'Tanggal diumumkan di Indonesia/Luar Negeri'])
    @include('layout.input-text', ['passing_variable' => 'announcement_place', 'passing_description' => 'Tempat diumumkan di Indonesia/Luar Negeri'])
    @include('layout.input-text', ['passing_variable' => 'protection_period', 'passing_description' => 'Jangka Waktu Perlindungan'])
    @include('layout.input-text', ['passing_variable' => 'registration_no', 'passing_description' => 'Nomor Pencatatan'])
    @include('layout.input-upload', ['passing_variable' => 'file_name_ori', 'passing_description' => 'Unggah File'])

</div>