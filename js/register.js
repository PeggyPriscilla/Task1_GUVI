// Execute immediately since content is loaded dynamically
(function() {
    // Wait a bit for DOM to be ready, then bind event
    setTimeout(function() {
        const form = document.getElementById("registerForm");
        
        if (form && !form.hasAttribute('data-bound')) {
            form.setAttribute('data-bound', 'true'); // Prevent double binding
            
            form.addEventListener("submit", function (e) {
                e.preventDefault();

                const name = document.getElementById("reg_name").value.trim();
                const email = document.getElementById("reg_email").value.trim();
                const password = document.getElementById("reg_password").value.trim();

                if (!name || !email || !password) {
                    alert("Please fill in all fields.");
                    return;
                }

                console.log("Submitting registration..."); // Debug log

                $.ajax({
                    type: "POST",
                    url: "backend/register.php",
                    data: {
                        name: name,
                        email: email,
                        password: password
                    },
                    success: function (response) {
                        console.log("Registration response:", response); // Debug log
                        try {
                            const res = JSON.parse(response);
                            if (res.status === "success") {
                                // Clear the form
                                document.getElementById("reg_name").value = '';
                                document.getElementById("reg_email").value = '';
                                document.getElementById("reg_password").value = '';
                                
                                alert("Registration successful! Redirecting to login...");
                                loadPage("login");
                            } else {
                                alert("Error: " + res.message);
                            }
                        } catch (e) {
                            console.error('JSON parse error:', e);
                            console.error('Raw response:', response);
                            alert("Registration error. Please try again.");
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX error:', error);
                        alert("Something went wrong. Please try again.");
                    }
                });
            });
        }
    }, 100);
})();
