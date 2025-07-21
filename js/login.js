// Execute immediately since content is loaded dynamically
(function() {
    setTimeout(function() {
        const loginForm = $('#login-form');
        
        if (loginForm.length && !loginForm.attr('data-bound')) {
            loginForm.attr('data-bound', 'true'); // Prevent double binding
            
            loginForm.submit(function (e) {
                e.preventDefault(); // prevent normal form submit

                const email = $('#email').val();
                const password = $('#password').val();

                console.log("Submitting login..."); // Debug log

                $.ajax({
                    url: 'backend/login.php',
                    method: 'POST',
                    data: { email: email, password: password },
                    success: function (response) {
                        console.log("Login response:", response); // Debug log
                        try {
                            let res = JSON.parse(response);

                            if (res.status === 'success') {
                                // Save user data and session to localStorage
                                localStorage.setItem('session_token', res.session_token);
                                localStorage.setItem('user_id', res.user.id);
                                localStorage.setItem('user_name', res.user.name);
                                localStorage.setItem('email', res.user.email);

                                alert('Login successful! Redirecting to profile...');
                                // Redirect to profile page using loadPage for SPA
                                loadPage('profile');
                            } else {
                                alert(res.message);
                            }
                        } catch (e) {
                            console.error('JSON parse error:', e);
                            console.error('Raw response:', response);
                            alert('Login error. Please try again.');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Login error:', error);
                        alert('Something went wrong. Please try again.');
                    }
                });
            });
        }
    }, 100);
})();
