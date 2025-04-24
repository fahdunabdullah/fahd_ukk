const logoutBtn = document.getElementById('logout-btn');
const modal = document.getElementById('logout-modal');
const closeModal = document.querySelector('.close');
const cancelLogout = document.getElementById('cancel-logout');
const confirmLogout = document.getElementById('confirm-logout');

logoutBtn.addEventListener('click', function (e) {
    e.preventDefault();
    modal.style.display = 'block';
});

closeModal.addEventListener('click', function () {
    modal.style.display = 'none';
});

cancelLogout.addEventListener('click', function () {
    modal.style.display = 'none';
});

confirmLogout.addEventListener('click', function () {
    window.location.href = '../auth/logout.php';
});

window.onclick = function (event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}