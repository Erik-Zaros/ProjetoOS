document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".toggle-menu").forEach(menu => {
        menu.addEventListener("click", function (e) {
            e.preventDefault();
            let submenu = this.nextElementSibling;
            submenu.classList.toggle("submenu-open");

            if (submenu.style.display === "block") {
                submenu.style.display = "none";
            } else {
                submenu.style.display = "block";
            }
        });
    });

    const page = window.location.pathname.split("/").pop();
    document.querySelectorAll(".nav-link").forEach(link => {
        if (link.getAttribute("href") === page) {
            link.classList.add("active");

            let submenu = link.closest(".submenu");
            if (submenu) {
                submenu.style.display = "block";
            }
        }
    });

    document.getElementById("sidebarSearch").addEventListener("keyup", function () {
        let filter = this.value.toLowerCase();
        document.querySelectorAll("#menuSidebar .nav-item").forEach(item => {
            let text = item.textContent.toLowerCase();
            item.style.display = text.includes(filter) ? "block" : "none";
        });
    });
});

document.getElementById("sidebarToggle").addEventListener("click", function () {
    const sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("d-none");
});
