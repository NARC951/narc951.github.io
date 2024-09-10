// Show and hide modal functions
function showModal(modalId) {
    document.getElementById(modalId).style.display = 'flex';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Additional JavaScript can go here (e.g., form validation, AJAX calls)
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Handle card purchase
    document.querySelectorAll(".purchase-card").forEach(function(element) {
        element.addEventListener("click", function(event) {
            event.preventDefault();
            const cardId = this.dataset.cardId;
            
            fetch(`cards.php?purchase=${cardId}`, { method: 'GET' })
                .then(response => response.text())
                .then(data => {
                    document.getElementById('result').innerHTML = data;
                    location.reload(); // Optionally reload or dynamically update UI
                })
                .catch(error => console.error('Error:', error));
        });
    });

    // Handle card deletion
    document.querySelectorAll(".delete-card").forEach(function(element) {
        element.addEventListener("click", function(event) {
            event.preventDefault();
            const cardId = this.dataset.cardId;
            
            fetch(`cards.php?delete=${cardId}`, { method: 'GET' })
                .then(response => response.text())
                .then(data => {
                    document.getElementById('result').innerHTML = data;
                    location.reload(); // Optionally reload or dynamically update UI
                })
                .catch(error => console.error('Error:', error));
        });
    });
});
</script>
