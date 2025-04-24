function updateTime() {
    const now = new Date();
    const options = {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
        hour: '2-digit', minute: '2-digit', second: '2-digit'
    };
    document.getElementById('current-time').innerText = now.toLocaleDateString('id-ID', options);
}
setInterval(updateTime, 1000);
updateTime();

document.querySelectorAll('.menu-item').forEach((item, index) => {
    item.style.opacity = '0';
    item.style.transform = 'translateY(20px)';
    setTimeout(() => {
        item.style.transition = 'all 0.5s ease';
        item.style.opacity = '1';
        item.style.transform = 'translateY(0)';
    }, 100 * (index + 1));
});

function animateStats() {
    document.querySelectorAll('.stat-box').forEach(box => box.classList.add('loaded'));
}
setTimeout(animateStats, 800);

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
