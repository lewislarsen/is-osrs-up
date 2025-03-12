document.addEventListener('DOMContentLoaded', function() {
    // Initialize theme based on system preference
    initializeTheme();

    // Add theme toggle button to body
    addThemeToggleButton();

    // Set up the countdown timer
    initializeCountdown();

    // Setup modal history data viewing
    setupHistoryModalEvents();

    // Force refresh button
    setupForceRefreshButton();
});

function initializeTheme() {
    // Check if theme is already set in localStorage
    const savedTheme = localStorage.getItem('theme');
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    // Apply theme based on saved preference or system preference
    if (savedTheme) {
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
    } else if (systemPrefersDark) {
        document.documentElement.setAttribute('data-bs-theme', 'dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.setAttribute('data-bs-theme', 'light');
        localStorage.setItem('theme', 'light');
    }

    // Listen for system preference changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
        // Only auto-change if user hasn't explicitly set a preference
        if (!localStorage.getItem('theme')) {
            const newTheme = e.matches ? 'dark' : 'light';
            document.documentElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeToggleIcon(newTheme);
        }
    });
}

function addThemeToggleButton() {
    const button = document.createElement('button');
    button.classList.add('theme-toggle');
    button.setAttribute('title', 'Toggle Dark/Light Mode');
    button.setAttribute('aria-label', 'Toggle Dark/Light Mode');

    // Set initial icon based on current theme
    const currentTheme = document.documentElement.getAttribute('data-bs-theme');
    button.innerHTML = getThemeIcon(currentTheme);

    button.addEventListener('click', toggleTheme);
    document.body.appendChild(button);
}

function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-bs-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

    document.documentElement.setAttribute('data-bs-theme', newTheme);
    localStorage.setItem('theme', newTheme);

    updateThemeToggleIcon(newTheme);
}

function updateThemeToggleIcon(theme) {
    const themeToggle = document.querySelector('.theme-toggle');
    if (themeToggle) {
        themeToggle.innerHTML = getThemeIcon(theme);
    }
}

function getThemeIcon(theme) {
    return theme === 'dark'
        ? '<i class="bi bi-sun"></i>'
        : '<i class="bi bi-moon"></i>';
}

function initializeCountdown() {
    // Check if nextCheck and countdown element exist
    if (typeof nextCheck !== 'undefined' && document.getElementById('next-check-countdown')) {
        // Set up the countdown timer
        function updateCountdown() {
            const now = new Date();
            const diffSeconds = Math.floor((nextCheck - now) / 1000);

            if (diffSeconds <= 0) {
                document.getElementById('next-check-countdown').textContent = 'Any moment now...';
                return;
            }

            const minutes = Math.floor(diffSeconds / 60);
            const seconds = diffSeconds % 60;
            document.getElementById('next-check-countdown').textContent =
                `${minutes}m ${seconds.toString().padStart(2, '0')}s`;
        }

        // Update countdown every second
        setInterval(updateCountdown, 1000);
        updateCountdown();
    }
}

function setupHistoryModalEvents() {
    // Check if historyData is defined and show-details elements exist
    if (typeof historyData !== 'undefined') {
        document.querySelectorAll('.show-details').forEach(button => {
            button.addEventListener('click', function() {
                const historyId = this.getAttribute('data-history-id');
                const historyItem = historyData.find(item => item.id == historyId);

                if (historyItem && historyItem.maintenance_data && historyItem.maintenance_data.length > 0) {
                    let tableContent = `
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Date</th>
                                <th>Issue</th>
                                <th>Downtime</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>`;

                    historyItem.maintenance_data.forEach(item => {
                        tableContent += `
                        <tr>
                            <td class="fw-bold">${item.date}</td>
                            <td>${item.issue}</td>
                            <td>${item.downtime}</td>
                            <td>
                                <span class="badge rounded-pill ${item.status.toLowerCase() === 'complete' ? 'bg-success' : 'bg-warning'}">
                                    ${item.status}
                                </span>
                            </td>
                        </tr>`;
                    });

                    tableContent += `
                        </tbody>
                    </table>`;

                    document.getElementById('historyModalBody').innerHTML = tableContent;
                } else {
                    document.getElementById('historyModalBody').innerHTML = '<div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>No maintenance data available for this entry.</div>';
                }
            });
        });
    }
}

function setupForceRefreshButton() {
    const refreshButton = document.getElementById('force-refresh');
    if (refreshButton) {
        refreshButton.addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '<i class="bi bi-arrow-repeat me-2"></i><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Checking...';
            location.reload();
        });
    }
}