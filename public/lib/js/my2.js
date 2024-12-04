$(document).ready(function () {
    // Extract the user ID from the URL
    const params = new URLSearchParams(window.location.search);
    const userId = params.get('id');

    // Extract the user details from the nav data attributes
    const userName = $('#userData').data('user-name');
    const userGender = $('#userData').data('user-gender');
    const userBirthday = $('#userData').data('user-birthday');
    const userAge = $('#userData').data('user-age');

    // Function to format the date
    function formatDateTo12Hour(dateString) {
        const date = new Date(dateString);
        const options = { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric', 
            hour: 'numeric', 
            minute: '2-digit',  
            hour12: true 
        };
        return date.toLocaleString('en-US', options);
    }

    // Update the profile information dynamically
    $('#userName').text('Name: ' + userName);
    $('#userGender').text('Gender: ' + userGender);
    $('#userBirthday').text('Birthday: ' + userBirthday);
    $('#userAge').text('Age: ' + userAge + ' years old');

    if (userId) {
        // Fetch posts for the user
        $.ajax({
            url: "/edma/src/controller/fetch_profilepost.php", // Update with your PHP file path
            type: "GET",
            data: { id: userId },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    const posts = response.posts;
                    let postsHTML = "";

                    posts.forEach(post => {
                        // Format the date here inside the loop
                        const formattedDate = formatDateTo12Hour(post.created_at);

                        // Check for the file type and display accordingly
                        let fileDisplay = "";
                        if (post.file_name) {
                            const fileExtension = post.file_type.split('/')[1]; // Extract file extension (e.g., 'jpg', 'mp4', 'pdf')

                            // Display images
                            if (['gif', 'png', 'jpeg', 'jpg'].includes(fileExtension)) {
                                fileDisplay = `  
                                    <img src="../../public/lib/images/posts/${post.file_name}" alt="Image" class="img-fluid rounded mb-3 full-width-media" style="max-width: 100%; height: auto;">
                                `;
                            }
                            // Display videos (MP4)
                            else if (fileExtension === 'mp4') {
                                fileDisplay = `  
                                    <video controls class="w-100 rounded mb-3 full-width-media">
                                        <source src="../../public/lib/images/posts/${post.file_name}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                `;
                            }
                            // Display audio (MP3 or WAV)
                            else if (['mpeg', 'wav'].includes(fileExtension)) {
                                fileDisplay = `  
                                    <audio controls class="w-100 rounded mb-3 full-width-media">
                                        <source src="../../public/lib/images/posts/${post.file_name}" type="audio/${fileExtension}">
                                        Your browser does not support the audio element.
                                    </audio>
                                `;
                            }
                            // Display documents (PDF, DOCX, PPTX)
                            else if (['pdf', 'docx', 'pptx'].includes(fileExtension)) {
                                fileDisplay = `  
                                    <div class="document-file">
                                        <a href="../../public/lib/images/posts/${post.file_name}" target="_blank" class="btn btn-link">
                                            <strong>${post.file_name.replace(/^\d+_/, '')}</strong>
                                        </a>
                                    </div>
                                `;
                            }
                        }

                        // Create post HTML with file display
                        postsHTML += `  
                            <div class="card mb-3" style="max-width: 540px; margin: auto;">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="../../public/lib/images/user_profile/${post.image_name}" alt="${post.name}" class="img-fluid rounded-circle" style="width: 40px; height: 40px; margin-right: 10px;">
                                        <h6 class="card-title text-truncate user-name" data-user-id="${post.u_id}" style="cursor: pointer;">
                                            ${post.name || 'Unknown'}
                                        </h6>
                                    </div>
                                    <p class="card-text text-truncate">${post.content}</p>
                                    <small class="text-muted d-block mb-3">Posted on ${formattedDate}</small>
                                    ${fileDisplay} <!-- Insert file display here -->
                                </div>
                            </div>
                        `;
                    });

                    // Append posts to the container
                    $('#postsContainer').html(postsHTML);
                } else {
                    $('#postsContainer').html(`<p>${response.message}</p>`);
                }
            },
            error: function (error) {
                console.error("Error fetching posts:", error);
                $('#postsContainer').html(`<p>Failed to load posts. Please try again later.</p>`);
            }
        });
    } else {
        $('#postsContainer').html(`<p>No user ID found in the URL.</p>`);
    }
});
