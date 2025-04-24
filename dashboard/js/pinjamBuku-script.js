function validateDates() {
    var tanggalPinjam = document.getElementById('tanggal_pinjam').value;
    var tanggalKembali = document.getElementById('tanggal_kembali').value;

    if (tanggalPinjam >= tanggalKembali) {
        alert('Tanggal kembali harus setelah tanggal pinjam!');
        return false;
    }
    return true;
}

function updateReturnDate() {
    var tanggalPinjam = document.getElementById('tanggal_pinjam').value;
    if (tanggalPinjam) {
        var date = new Date(tanggalPinjam);
        date.setDate(date.getDate() + 7);

        var yyyy = date.getFullYear();
        var mm = String(date.getMonth() + 1).padStart(2, '0');
        var dd = String(date.getDate()).padStart(2, '0');

        document.getElementById('tanggal_kembali').value = yyyy + '-' + mm + '-' + dd;
        document.getElementById('tanggal_kembali').min = tanggalPinjam;
    }
}