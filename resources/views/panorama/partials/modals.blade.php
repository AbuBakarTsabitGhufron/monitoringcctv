<!-- Modal Add/Edit -->
<div class="modal fade" id="panoramaModal" tabindex="-1" aria-labelledby="panoramaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="panoramaModalLabel">Tambah CCTV Lokasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="panoramaForm" method="POST">
                    @csrf
                    <input type="hidden" id="idLokasi">
                    <div class="mb-3">
                        <label for="namaWilayah" class="form-label">Nama Wilayah</label>
                        <select class="form-control" id="namaWilayah" name="namaWilayah" required>
                            <option value="" disabled selected>Pilih Wilayah</option>
                            <option value="KOTA JOGJA">KOTA JOGJA</option>
                            <option value="KABUPATEN SLEMAN">KAB SLEMAN</option>
                            <option value="KABUPATEN BANTUL">KAB BANTUL</option>
                            <option value="KABUPATEN KP">KAB KULONPROGO</option>
                            <option value="KABUPATEN GK">KAB GUNUNG KIDUL</option>
                            <option value="KABUPATEN GK">KHUSUS</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="namaTitik" class="form-label">Titik Wilayah</label>
                        <input type="text" class="form-control" id="namaTitik" name="namaTitik" required>
                    </div>
                    <div class="mb-3">
                        <label for="link" class="form-label">Link</label>
                        <input type="text" class="form-control" id="link" name="link" required>
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