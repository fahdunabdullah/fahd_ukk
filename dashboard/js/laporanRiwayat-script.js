document.addEventListener('DOMContentLoaded', function () {
    initSearch();

    animasiTabel();

    setupTombolCetak();

    buatTabelBisaDiurutkan();
});

function initSearch() {
    const inputPencarian = document.getElementById('search-input');
    if (!inputPencarian) return;

    inputPencarian.addEventListener('keyup', function () {
        const kataPencarian = this.value.toLowerCase();
        const barisTabel = document.querySelectorAll('table tbody tr');

        const isPopulerTable = document.querySelector('.populer-header') !== null;
        const judul_column = isPopulerTable ? 2 : 3;

        let hasilDitemukan = false;

        barisTabel.forEach(baris => {
            const judulBuku = baris.querySelector(`td:nth-child(${judul_column})`);
            if (!judulBuku) return;

            if (!judulBuku.hasAttribute('data-original-text')) {
                judulBuku.setAttribute('data-original-text', judulBuku.textContent);
            }

            const originalText = judulBuku.getAttribute('data-original-text');

            if (originalText.toLowerCase().includes(kataPencarian)) {
                baris.style.display = '';
                hasilDitemukan = true;

                judulBuku.textContent = originalText;
            } else {
                baris.style.display = 'none';
            }
        });

        let pesanTidakDitemukan = document.getElementById('tidak-ditemukan-message');

        if (!hasilDitemukan && kataPencarian.length > 0) {
            if (!pesanTidakDitemukan) {
                pesanTidakDitemukan = document.createElement('div');
                pesanTidakDitemukan.id = 'tidak-ditemukan-message';
                pesanTidakDitemukan.className = 'empty-message';
                pesanTidakDitemukan.innerHTML = '<i class="fas fa-info-circle"></i> Buku tidak ditemukan.';

                const tabel = document.querySelector('table');
                tabel.parentNode.insertBefore(pesanTidakDitemukan, tabel.nextSibling);
            }
            pesanTidakDitemukan.style.display = 'block';
        } else if (pesanTidakDitemukan) {
            pesanTidakDitemukan.style.display = 'none';
        }

        updateRingkasan();
    });

    const style = document.createElement('style');
    style.textContent = `
        #search-container { position: relative; max-width: 1000px; margin: 20px auto; }
        #search-input { width: 100%; padding: 10px 15px 10px 40px; border: 1px solid #ddd; border-radius: 4px; }
        #search-input:focus { border-color: #3498db; box-shadow: 0 0 8px rgba(52, 152, 219, 0.5); }
        .search-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #888; }
        #tidak-ditemukan-message { display: none; text-align: center; padding: 20px; }
    `;
    document.head.appendChild(style);
}

function animasiTabel() {
    const barisTabel = document.querySelectorAll('table tbody tr');

    barisTabel.forEach((baris, index) => {
        baris.style.opacity = '0';
        baris.style.animation = `fadeIn 0.3s ease forwards ${index * 0.05}s`;
    });

    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .status-returned { color: #27ae60; font-weight: 500; }
        .status-borrowed { color: #e67e22; font-weight: 500; }
    `;
    document.head.appendChild(style);

    barisTabel.forEach(baris => {
        const statusCell = baris.querySelector('td:nth-child(6)');
        if (!statusCell) return;

        if (statusCell.textContent === 'Sudah Dikembalikan') {
            statusCell.classList.add('status-returned');
        } else if (statusCell.textContent === 'Masih Dipinjam') {
            statusCell.classList.add('status-borrowed');
        }
    });
}

function setupTombolCetak() {
    const tombolCetak = document.querySelectorAll('.btn-print');

    tombolCetak.forEach(tombol => {
        tombol.addEventListener('click', function (e) {
            e.preventDefault();

            if (confirm('Siap mencetak laporan. Lanjutkan?')) {
                window.print();
            }
        });
    });
}

function buatTabelBisaDiurutkan() {
    const tabel = document.querySelector('table');
    if (!tabel) return;

    const headerKolom = tabel.querySelectorAll('th');

    headerKolom.forEach((header, index) => {
        if (index === headerKolom.length - 1) return;

        header.style.cursor = 'pointer';
        header.innerHTML += ' <span class="sort-icon">↕️</span>';
        header.setAttribute('data-sort-direction', 'none');

        header.addEventListener('click', function () {
            const arahUrutan = this.getAttribute('data-sort-direction');
            const arahBaru = arahUrutan === 'asc' ? 'desc' : 'asc';

            headerKolom.forEach(h => {
                h.setAttribute('data-sort-direction', 'none');
                h.querySelector('.sort-icon').textContent = '↕️';
            });

            this.setAttribute('data-sort-direction', arahBaru);
            this.querySelector('.sort-icon').textContent = arahBaru === 'asc' ? '↑' : '↓';

            urutkanTabel(tabel, index, arahBaru);
        });
    });

    const style = document.createElement('style');
    style.textContent = `
        .sort-icon {
            font-size: 0.8em;
            margin-left: 5px;
            opacity: 0.6;
        }
        th[data-sort-direction="asc"] .sort-icon,
        th[data-sort-direction="desc"] .sort-icon {
            opacity: 1;
        }
    `;
    document.head.appendChild(style);
}

function urutkanTabel(tabel, indexKolom, arah) {
    const tbody = tabel.querySelector('tbody');
    const baris = Array.from(tbody.querySelectorAll('tr'));

    baris.sort((barisA, barisB) => {
        const isiA = barisA.cells[indexKolom].textContent.trim();
        const isiB = barisB.cells[indexKolom].textContent.trim();

        if (isTanggal(isiA) && isTanggal(isiB)) {
            const tanggalA = new Date(isiA);
            const tanggalB = new Date(isiB);
            return arah === 'asc' ? tanggalA - tanggalB : tanggalB - tanggalA;
        }

        if (!isNaN(isiA) && !isNaN(isiB)) {
            return arah === 'asc' ? isiA - isiB : isiB - isiA;
        }

        return arah === 'asc'
            ? isiA.localeCompare(isiB)
            : isiB.localeCompare(isiA);
    });

    baris.forEach(baris => tbody.appendChild(baris));
}

