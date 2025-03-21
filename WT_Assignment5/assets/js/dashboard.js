// Dashboard chart initialization
function initializeCharts(data) {
    const ctx = document.getElementById('progressChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Tasks Completed',
                data: data.completed,
                borderColor: '#0d6efd',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
}

// Real-time progress updates
function updateProgress() {
    fetch('update_progress.php')
        .then(response => response.json())
        .then(data => {
            // Update progress indicators
            document.querySelectorAll('[data-progress]').forEach(element => {
                const key = element.dataset.progress;
                if (data[key]) element.textContent = data[key];
            });
        });
}

// Initialize dashboard components
document.addEventListener('DOMContentLoaded', function() {
    // Set up periodic updates
    setInterval(updateProgress, 30000);
    
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => new bootstrap.Tooltip(tooltip));
});
