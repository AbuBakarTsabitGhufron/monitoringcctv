<!-- Modal Add/Edit -->
<div class="modal fade" id="cctvlokasiModal" tabindex="-1" aria-labelledby="cctvlokasiModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cctvlokasiModalLabel">Tambah CCTV Lokasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="cctvForm" method="POST">
                    @csrf
                    <input type="hidden" id="idLokasi">
                    <div class="mb-3">
                        <label for="wilayah_id">Nama Wilayah</label>
                        <select class="form-control" id="wilayah_id" name="wilayah_id" required>
                            <option value="" disabled selected>Pilih Wilayah</option>
                            <option value="1">KABUPATEN BANTUL</option>
                            <option value="2">KOTA YOGYAKARTA</option>
                            <option value="3">KABUPATEN SLEMAN</option>
                            <option value="4">KABUPATEN KULON PROGO</option>
                            <option value="5">KABUPATEN GUNUNG KIDUL</option>
                            <option value="7">ATCS KOTA</option>
                            <option value="8">ATCS DIY</option>
                            <option value="9">MALIOBORO</option>
                            <option value="10">PANORAMA</option>
                        </select>
                    </div>
                    <div class="mb-3"> 
                        <label for="nama_lokasi" class="form-label">Nama Lokasi</label>
                        <input type="text" class="form-control" id="nama_lokasi" name="nama_lokasi" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_cctv" class="form-label">Titik Wilayah</label>
                        <input type="text" class="form-control" id="nama_cctv" name="nama_cctv" required>
                    </div>
                    <div class="mb-3">
                        <label for="link_stream" class="form-label">Link</label>
                        <input type="text" class="form-control" id="link_stream" name="link_stream" required>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>