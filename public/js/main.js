$(document).ready(function () {
  const currentPath = window.location.pathname
    .split("/")
    .pop()
    .replace(/\.php$/, '');

  document.querySelectorAll(".nav-link").forEach(link => {
    const href = link.getAttribute("href")?.replace(/\.php$/, '');

    if (href === currentPath) {
      link.classList.add("active");

      const treeItem = link.closest(".has-treeview");
      if (treeItem) {
        treeItem.classList.add("menu-open");
        const parentLink = treeItem.querySelector(":scope > .nav-link");
        if (parentLink) parentLink.classList.add("active");
      }
    }
  });
});
