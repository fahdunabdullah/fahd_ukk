function validateDates() {
    var tanggalPinjam = new Date(document.querySelector('input[name="tanggal_pinjam"]').value);
    var tanggalKembali = new Date(document.querySelector('input[name="tanggal_kembali"]').value);
    
    if (tanggalKembali <= tanggalPinjam) {
        alert("Tanggal kembali harus setelah tanggal pinjam!");
        return false;
    }
    
    return true;
}

function updateReturnDate() {
    var tanggalPinjamInput = document.querySelector('input[name="tanggal_pinjam"]');
    var tanggalKembaliInput = document.getElementById('tanggal_kembali');
    
    if (tanggalPinjamInput.value) {
        var tanggalPinjam = new Date(tanggalPinjamInput.value);
        var tanggalKembali = new Date(tanggalPinjam);
        tanggalKembali.setDate(tanggalPinjam.getDate() + 7);
        
        var year = tanggalKembali.getFullYear();
        var month = String(tanggalKembali.getMonth() + 1).padStart(2, '0');
        var day = String(tanggalKembali.getDate()).padStart(2, '0');
        
        tanggalKembaliInput.value = `${year}-${month}-${day}`;
    }
}

// Execute when page loads
document.addEventListener('DOMContentLoaded', function() {
    updateReturnDate();
});