document.addEventListener("DOMContentLoaded", function () {
    const currentPath = window.location.pathname.split("/").pop().replace(/\.php$/, '').replace(/\/$/, '');

    document.querySelectorAll(".nav-link").forEach(link => {
        let href = link.getAttribute("href").replace(/\.php$/, '');
        if (href === currentPath) {
            link.classList.add("active");
            let parentItem = link.closest(".has-treeview");
            if (parentItem) parentItem.classList.add("menu-open");
        }
    });

    const sidebarSearch = document.getElementById("sidebarSearch");
    if (sidebarSearch) {
        sidebarSearch.addEventListener("keyup", function () {
            let filter = this.value.toLowerCase();
            document.querySelectorAll("#menuSidebar .nav-item").forEach(item => {
                let text = item.textContent.toLowerCase();
                item.style.display = text.includes(filter) ? "block" : "none";
            });
        });
    }
});
