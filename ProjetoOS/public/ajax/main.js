document.addEventListener("DOMContentLoaded", function () {
    const page = window.location.pathname.split("/").pop();
    const navLinks = document.querySelectorAll(".nav-link");
    navLinks.forEach(link => {
        if (link.getAttribute("href") === page) {
            link.classList.add("active");
        }
    });

    document.getElementById("sidebarToggle").addEventListener("click", function () {
        const sidebar = document.getElementById("sidebar");
        sidebar.classList.toggle("d-none");
    });
});