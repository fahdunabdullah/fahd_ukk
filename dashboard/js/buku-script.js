document.addEventListener('DOMContentLoaded', function () {
    const books = document.querySelectorAll('.book');
    books.forEach((book, index) => {
        book.style.opacity = '0';
        book.style.transform = 'scale(0.9)';

        setTimeout(() => {
            book.style.transition = 'all 0.5s ease';
            book.style.opacity = '1';
            book.style.transform = 'scale(1)';
        }, 50 * index);

        book.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-10px)';
            this.style.boxShadow = '0 10px 20px rgba(0,0,0,0.2)';
        });

        book.addEventListener('mouseleave', function () {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
        });

        const quickView = book.querySelector('.quick-view');
        if (quickView) {
            quickView.addEventListener('click', function () {
                const bookImg = book.querySelector('img').src;
                const bookTitle = book.querySelector('.book-title').textContent;
                const bookId = book.querySelector('.pinjam-btn').href.split('=')[1];

                const bookAuthor = book.getAttribute('data-author');
                const bookPublisher = book.getAttribute('data-publisher');
                const bookYear = book.getAttribute('data-year');

                document.getElementById('modal-image').src = bookImg;
                document.getElementById('modal-title').textContent = bookTitle;
                document.getElementById('modal-author').textContent = bookAuthor;
                document.getElementById('modal-publisher').textContent = bookPublisher;
                document.getElementById('modal-year').textContent = bookYear;
                document.getElementById('modal-pinjam').href = `pinjamBuku.php?id=${bookId}`;

                const modal = document.getElementById('book-detail-modal');
                modal.style.display = 'flex';
                setTimeout(() => {
                    modal.style.opacity = '1';
                    document.querySelector('.modal-content').style.transform = 'translateY(0)';
                }, 10);
            });
        }
    });

    const closeModal = document.querySelector('.close-modal');
    closeModal.addEventListener('click', function () {
        const modal = document.getElementById('book-detail-modal');
        document.querySelector('.modal-content').style.transform = 'translateY(-20px)';
        modal.style.opacity = '0';
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    });

    window.addEventListener('click', function (event) {
        const modal = document.getElementById('book-detail-modal');
        if (event.target === modal) {
            document.querySelector('.modal-content').style.transform = 'translateY(-20px)';
            modal.style.opacity = '0';
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }
    });

    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const searchText = this.value.toLowerCase();
            const books = document.querySelectorAll('.book');
            let hasResults = false;

            books.forEach(book => {
                const title = book.getAttribute('data-title');
                if (title.includes(searchText)) {
                    book.style.display = '';
                    hasResults = true;
                } else {
                    book.style.display = 'none';
                }
            });

            document.getElementById('no-results').style.display = hasResults ? 'none' : 'flex';
        });
    }

    const viewGridBtn = document.getElementById('view-grid');
    const viewListBtn = document.getElementById('view-list');
    const gallery = document.getElementById('gallery');

    if (viewGridBtn && viewListBtn) {
        viewGridBtn.addEventListener('click', function () {
            gallery.classList.remove('list-view');
            viewGridBtn.classList.add('active');
            viewListBtn.classList.remove('active');
        });

        viewListBtn.addEventListener('click', function () {
            gallery.classList.add('list-view');
            viewListBtn.classList.add('active');
            viewGridBtn.classList.remove('active');
        });
    }

    const sortSelect = document.getElementById('sort-books');
    if (sortSelect) {
        sortSelect.addEventListener('change', function () {
            const sortValue = this.value;
            const books = Array.from(document.querySelectorAll('.book'));

            books.sort((a, b) => {
                const titleA = a.getAttribute('data-title');
                const titleB = b.getAttribute('data-title');

                if (sortValue === 'asc') {
                    return titleA.localeCompare(titleB);
                } else if (sortValue === 'desc') {
                    return titleB.localeCompare(titleA);
                }
                return 0;
            });

            const gallery = document.getElementById('gallery');
            books.forEach(book => gallery.appendChild(book));
        });
    }
});


document.addEventListener('DOMContentLoaded', function () {
    const quickViewButtons = document.querySelectorAll('.quick-view');
    const modal = document.getElementById('book-detail-modal');
    const closeModal = document.querySelector('.close-modal');

    quickViewButtons.forEach(button => {
        button.addEventListener('click', function () {
            const book = this.closest('.book');
            const bookId = book.querySelector('.pinjam-btn').href.split('=')[1];
            const title = book.dataset.title;
            const author = book.dataset.author;
            const publisher = book.dataset.publisher;
            const year = book.dataset.year;
            const category = book.dataset.category;
            const stock = book.dataset.stock;
            const imgSrc = book.querySelector('img').src;

            document.getElementById('modal-title').textContent = title;
            document.getElementById('modal-author').textContent = author;
            document.getElementById('modal-publisher').textContent = publisher;
            document.getElementById('modal-year').textContent = year;
            document.getElementById('modal-category').textContent = category;
            document.getElementById('modal-stock').textContent = stock;
            document.getElementById('modal-image').src = imgSrc;
            document.getElementById('modal-pinjam').href = `pinjamBuku.php?id=${bookId}`;

            modal.style.display = 'block';
        });
    });

    closeModal.addEventListener('click', function () {
        modal.style.display = 'none';
    });

    window.addEventListener('click', function (event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
});
