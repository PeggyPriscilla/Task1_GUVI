$(document).ready(function () {
    const sessionToken = localStorage.getItem('session_token');
    const userId = localStorage.getItem('user_id');
    const userName = localStorage.getItem('user_name');
    const email = localStorage.getItem('email');

    // Check if user is logged in
    if (!sessionToken || !userId || !email) {
        // If no session found, redirect back to login
        loadPage('login');
        return;
    }

    // Display user information
    $('#user-name-display').text(userName || 'User');
    $('#user-email-display').text(email);

    // Load existing profile data
    loadProfileData();

    // Handle profile form submission
    $('#profile-form').on('submit', function (e) {
        e.preventDefault();
        updateProfile();
    });

    // Logout handler
    $('#logout-btn').on('click', function () {
        logout();
    });

    function loadProfileData() {
        $.ajax({
            url: 'backend/get_profile.php',
            method: 'POST',
            data: {
                session_token: sessionToken,
                user_id: userId
            },
            success: function (response) {
                try {
                    const res = JSON.parse(response);
                    if (res.status === 'success' && res.profile) {
                        const profile = res.profile;
                        $('#profile_age').val(profile.age || '');
                        $('#profile_dob').val(profile.dob || '');
                        $('#profile_contact').val(profile.contact || '');
                        $('#profile_city').val(profile.city || '');
                        $('#profile_address').val(profile.address || '');
                        $('#profile_occupation').val(profile.occupation || '');
                        $('#profile_company').val(profile.company || '');
                    }
                } catch (e) {
                    console.log('Profile data not found or empty - this is normal for new users');
                }
            },
            error: function (xhr, status, error) {
                console.log('Profile load error (normal for new users):', error);
            }
        });
    }

    function updateProfile() {
        const profileData = {
            session_token: sessionToken,
            user_id: userId,
            age: $('#profile_age').val(),
            dob: $('#profile_dob').val(),
            contact: $('#profile_contact').val(),
            city: $('#profile_city').val(),
            address: $('#profile_address').val(),
            occupation: $('#profile_occupation').val(),
            company: $('#profile_company').val()
        };

        $.ajax({
            url: 'backend/update_profile.php',
            method: 'POST',
            data: profileData,
            success: function (response) {
                try {
                    const res = JSON.parse(response);
                    if (res.status === 'success') {
                        alert('Profile updated successfully!');
                    } else {
                        alert('Error updating profile: ' + res.message);
                    }
                } catch (e) {
                    alert('Error updating profile. Please try again.');
                }
            },
            error: function (xhr, status, error) {
                console.error('Profile update error:', error);
                alert('Something went wrong. Please try again.');
            }
        });
    }

    function logout() {
        // Clear session from Redis
        $.ajax({
            url: 'backend/logout.php',
            method: 'POST',
            data: { session_token: sessionToken },
            complete: function() {
                // Clear localStorage regardless of backend response
                localStorage.clear();
                // Redirect to login
                loadPage('login');
            }
        });
    }
});
