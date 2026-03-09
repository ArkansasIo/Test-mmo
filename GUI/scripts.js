document.addEventListener("DOMContentLoaded", function() {
    // Example of handling button click for actions like building or fleet management
    document.querySelectorAll(".action-btn").forEach(button => {
        button.addEventListener("click", function() {
            alert("You have clicked on the action: " + button.textContent);
        });
    });
});
