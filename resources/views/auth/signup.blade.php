<x-layout title="Sign Up">
    <div class="auth-container" style="max-height: 90vh; overflow-y: auto;">
        <h2>Sign Up</h2>
        <form id="signupForm">
            <div class="mb-3">
                <label for="firstName" class="form-label">First Name</label>
                <input type="text" class="form-control" id="firstName" name="firstName" required>
            </div>
            <div class="mb-3">
                <label for="lastName" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="lastName" name="lastName" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">No Telp</label>
                <input type="tel" class="form-control" id="phone" name="phone" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3 position-relative">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control pe-5" id="password" name="password" required>
                <span class="password-toggle-icon" onclick="togglePassword()">
                    <i id="toggle-icon" class="fa-solid fa-eye-slash"></i>
                </span>
            </div>
            <div class="mb-3">
                <label for="confirmPassword" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                <small id="passwordMismatch" class="text-danger d-none">Passwords do not match.</small>
            </div>
            <br>
            <button type="submit" class="btn btn-primary w-100">Sign Up</button>
        </form>
        <div class="toggle-link mt-2">
            <span>Already have an account? <a href="/login">Login</a></span>
        </div>
    </div>

    @push('scripts')
        <script>
            function togglePassword() {
                const passwordInput = document.getElementById("password");
                const toggleIcon = document.getElementById("toggle-icon");

                const isPasswordVisible = passwordInput.type === "text";
                passwordInput.type = isPasswordVisible ? "password" : "text";

                toggleIcon.classList.toggle("fa-eye");
                toggleIcon.classList.toggle("fa-eye-slash");
            }

            $('#signupForm').on('submit', async function(e) {
                e.preventDefault();

                const firstName = $('#firstName').val();
                const lastName = $('#lastName').val();
                const phone = $('#phone').val();
                const email = $('#email').val();
                const password = $('#password').val();
                const confirmPassword = $('#confirmPassword').val();

                const mismatchWarning = $('#passwordMismatch');

                if (password !== confirmPassword) {
                    mismatchWarning.removeClass('d-none');
                    return;
                } else {
                    mismatchWarning.addClass('d-none');
                }

                try {
                    const accountRef = window.db.collection('account').where('email', '==', email);
                    const querySnapshot = await accountRef.get();

                    if (!querySnapshot.empty) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Email already in use',
                            text: 'Please use a different email address.',
                        }).then(() => {
                            $('#email').val('').focus();
                            $('#password').val('');
                            $('#confirmPassword').val('');
                        });
                        return;
                    }

                    const hashedPassword = CryptoJS.SHA256(password).toString();

                    await db.collection('account').add({
                        first_name: firstName,
                        last_name: lastName,
                        phone,
                        email,
                        password: hashedPassword,
                        role: 'user'
                    });

                    Swal.fire({
                        icon: 'success',
                        title: 'Account created!',
                        text: 'Redirecting to login...',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '/login';
                    });

                } catch (error) {
                    console.error("Error during signup:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong. Please try again later.',
                    });
                }
            });
        </script>
    @endpush
</x-layout>
