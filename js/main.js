// const sidebar = document.getElementById('sidebar');
// const toggleButton = document.getElementById('sidebarToggle');
// const mainContent = document.querySelector('.main-content');

// toggleButton.addEventListener('click', () => {
//     const isCollapsed = sidebar.classList.toggle('collapsed');
//     mainContent.style.marginLeft = isCollapsed ? '0' : '0';
// });

// JavaScript to toggle sidebar
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const toggleBtn = document.querySelector('.toggle-btn');

    sidebar.classList.toggle('collapsed');

    // Update the toggle button arrow direction
    if (sidebar.classList.contains('collapsed')) {
        toggleBtn.innerHTML = '⮞'; // Point right when closed
    } else {
        toggleBtn.innerHTML = '⮜'; // Point left when open
    }
}