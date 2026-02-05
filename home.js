document.addEventListener("DOMContentLoaded", () => {
    const loginLinks = document.querySelectorAll(".login-link");
    
    loginLinks.forEach(link => {
        link.addEventListener("click", (event) => {
            event.preventDefault();
            alert(`Redirecting to ${event.target.innerText} page...`);
        });
    });

    const ctaButton = document.querySelector(".cta-button");
    ctaButton.addEventListener("click", () => {
        alert("Sign-up functionality coming soon!");
    });
});
