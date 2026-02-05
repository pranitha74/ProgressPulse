document.addEventListener("DOMContentLoaded", function () {
    const sidebarItems = document.querySelectorAll(".sidebar ul li");
    
    sidebarItems.forEach(item => {
        item.addEventListener("click", function () {
            sidebarItems.forEach(i => i.classList.remove("active"));
            this.classList.add("active");
        });
    });
    
    const logoutButton = document.querySelector(".logout");
    logoutButton.addEventListener("click", function () {
        alert("Logging out...");
    });

    const searchInput = document.querySelector("header input");
    searchInput.addEventListener("keyup", function (event) {
        console.log("Searching for:", event.target.value);
    });
});
