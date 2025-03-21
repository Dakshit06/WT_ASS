function loadNotifications() {
    fetch('get_notifications.php')
        .then(response => response.json())
        .then(data => {
            const dropdown = document.querySelector('.notification-dropdown');
            dropdown.innerHTML = data.notifications.map(n => `
                <a href="#" class="dropdown-item ${n.is_read ? '' : 'bg-light'}" data-id="${n.id}">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="bi bi-${getNotificationIcon(n.type)}"></i>
                        </div>
                        <div>
                            <p class="mb-0">${n.message}</p>
                            <small class="text-muted">${n.created_at}</small>
                        </div>
                    </div>
                </a>
            `).join('') || '<p class="dropdown-item mb-0">No notifications</p>';
        });
}

function getNotificationIcon(type) {
    const icons = {
        info: 'info-circle',
        success: 'check-circle',
        warning: 'exclamation-triangle',
        error: 'x-circle'
    };
    return icons[type] || 'bell';
}

// Initialize notifications
document.addEventListener('DOMContentLoaded', function() {
    loadNotifications();
    setInterval(loadNotifications, 30000);
});
