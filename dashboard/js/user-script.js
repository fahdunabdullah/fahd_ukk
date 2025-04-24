document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.container').classList.add('fade-in');
    
    const menuButtons = document.querySelectorAll('.btn');
    menuButtons.forEach((button, index) => {
        setTimeout(() => {
            button.classList.add('show');
        }, 300 + (index * 150));
    });
    
    const welcomeText = document.querySelector('.welcome-header h1');
    const originalText = welcomeText.textContent;
    welcomeText.textContent = '';
    
    let i = 0;
    function typeWriter() {
        if (i < originalText.length) {
            welcomeText.textContent += originalText.charAt(i);
            i++;
            setTimeout(typeWriter, 50);
        }
    }
    
    setTimeout(typeWriter, 500);
});

document.querySelectorAll('.btn').forEach(button => {
    button.addEventListener('mouseover', function() {
        this.classList.add('pulse');
    });
    
    button.addEventListener('mouseout', function() {
        this.classList.remove('pulse');
    });
});

document.querySelector('.btn-danger').addEventListener('click', function(e) {
    e.preventDefault();
    
    if (confirm('Apakah Anda yakin ingin keluar dari sistem?')) {
        window.location.href = this.getAttribute('href');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const footer = document.querySelector('.footer');
    const today = new Date();
    const formattedDate = today.toLocaleDateString('id-ID', {
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric'
    });
    
    const timeElement = document.createElement('div');
    timeElement.classList.add('timestamp');
    timeElement.innerHTML = `<br>${formattedDate}`;
    footer.appendChild(timeElement);
});

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <span>${message}</span>
        <button class="close-btn">&times;</button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 500);
    }, 5000);
    
    notification.querySelector('.close-btn').addEventListener('click', () => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 500);
    });
}

setTimeout(() => {
    showNotification('Selamat datang di Perpustakaan Digital!', 'success');
}, 1000);