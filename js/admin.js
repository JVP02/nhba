document.addEventListener("DOMContentLoaded", function () {
    // Simulated data for demonstration
    const uploads = [
        { name: "Image1.jpg", url: "https://via.placeholder.com/150" },
        { name: "Image2.jpg", url: "https://via.placeholder.com/150" },
        { name: "Image3.jpg", url: "https://via.placeholder.com/150" },
    ];

    const users = [
        { id: 1, name: "John Doe", email: "john@example.com", role: "Admin" },
        { id: 2, name: "Jane Smith", email: "jane@example.com", role: "Editor" },
        { id: 3, name: "Bob Johnson", email: "bob@example.com", role: "Viewer" },
    ];

    // Load statistics
    document.getElementById("total-uploads").textContent = uploads.length;
    document.getElementById("active-users").textContent = users.length;
    document.getElementById("pending-approvals").textContent = "5";

    // Load recent uploads
    const uploadsGrid = document.getElementById("recent-uploads");
    uploads.forEach(upload => {
        const card = document.createElement("div");
        card.className = "upload-card";
        card.innerHTML = `
            <img src="${upload.url}" alt="${upload.name}">
            <p>${upload.name}</p>
        `;
        uploadsGrid.appendChild(card);
    });

    // Load user list
    const userList = document.getElementById("user-list");
    users.forEach(user => {
        const row = document.createElement("tr");
        row.innerHTML = `
            <td>${user.id}</td>
            <td>${user.name}</td>
            <td>${user.email}</td>
            <td>${user.role}</td>
            <td><button class="action-btn edit-btn" data-id="${user.id}">Edit</button> <button class="action-btn delete-btn" data-id="${user.id}">Delete</button></td>
        `;
        userList.appendChild(row);
    });

    // Handle edit button click
    document.querySelectorAll(".edit-btn").forEach(button => {
        button.addEventListener("click", function () {
            const userId = this.getAttribute("data-id");
            const newName = prompt("Enter the new name for the user:");
            const newEmail = prompt("Enter the new email for the user:");
            const newRole = prompt("Enter the new role for the user:");

            if (newName && newEmail && newRole) {
                fetch("edit_user.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        id: userId,
                        name: newName,
                        email: newEmail,
                        role: newRole
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("User updated successfully!");
                        location.reload(); // Reload the page to reflect changes
                    } else {
                        alert("Failed to update user: " + data.message);
                    }
                })
                .catch(error => {
                    console.error("Error updating user:", error);
                    alert("An error occurred while updating the user.");
                });
            }
        });
    });

    // Handle delete button click
    document.querySelectorAll(".delete-btn").forEach(button => {
        button.addEventListener("click", function () {
            const userId = this.getAttribute("data-id");
            if (confirm(`Are you sure you want to delete user with ID: ${userId}?`)) {
                fetch("../delete_user.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ id: userId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("User deleted successfully!");
                        location.reload(); // Reload the page to reflect changes
                    } else {
                        alert("Failed to delete user: " + data.message);
                    }
                })
                .catch(error => {
                    console.error("Error deleting user:", error);
                    alert("An error occurred while deleting the user.");
                });
            }
        });
    });
});
