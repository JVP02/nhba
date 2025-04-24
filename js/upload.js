document.addEventListener("DOMContentLoaded", function () {
    const adminGallery = document.getElementById("admin-gallery");
    const repoUrl = "https://api.github.com/repos/JVP02/nhb/contents/asset/gallery/community";
    const uploadForm = document.getElementById("upload-form");
    const uploadStatus = document.getElementById("upload-status");

    // Fetch and display images
    fetch(repoUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error("Failed to fetch images from the repository.");
            }
            return response.json();
        })
        .then(data => {
            data.forEach(file => {
                if (file.type === "file" && /\.(jpg|jpeg|png|gif)$/i.test(file.name)) {
                    const card = document.createElement("div");
                    card.className = "admingallery-card";

                    card.innerHTML = `
                        <img src="${file.download_url}" alt="${file.name}">
                        <div class="admingallery-card-content">
                            <h3>${file.name}</h3>
                            <p>No description available.</p>
                            <small>Unknown date</small>
                        </div>
                    `;

                    adminGallery.appendChild(card);
                }
            });
        })
        .catch(error => {
            console.error("Error loading images:", error);
            adminGallery.innerHTML = "<p>Failed to load images. Please try again later.</p>";
        });

    // Handle image upload
    uploadForm.addEventListener("submit", function (event) {
        event.preventDefault();
        const fileInput = document.getElementById("image-file");
        const file = fileInput.files[0];

        if (!file) {
            uploadStatus.textContent = "Please select a file to upload.";
            return;
        }

        const reader = new FileReader();
        reader.onload = function () {
            const base64Content = reader.result.split(",")[1];
            const uploadUrl = `https://api.github.com/repos/JVP02/nhb/contents/asset/gallery/community/${file.name}`;

            fetch(uploadUrl, {
                method: "PUT",
                headers: {
                    "Authorization": "Bearer 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", // Replace with a valid token
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    message: `Add ${file.name}`,
                    content: base64Content
                })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Failed to upload the image.");
                    }
                    return response.json();
                })
                .then(() => {
                    uploadStatus.textContent = "Image uploaded successfully!";
                    fileInput.value = ""; // Clear the input
                })
                .catch(error => {
                    console.error("Error uploading image:", error);
                    uploadStatus.textContent = "Failed to upload the image. Please try again.";
                });
        };
        reader.readAsDataURL(file);
    });
});