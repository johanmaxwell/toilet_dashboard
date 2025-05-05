<x-layout title="Sign Up">
    <div class="auth-container">
        <h2>Sign Up</h2>
        <form id="signupForm">
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
            <br>
            <button type="submit" class="btn btn-primary w-100">Sign Up</button>
        </form>
        <div class="toggle-link">
            <span>Already have an account? <a href="/login">Login</a></span>
        </div>
    </div>

    @push('scripts')
        <script>
            function togglePassword() {
                const passwordInput = document.getElementById("password");
                const toggleIcon = document.getElementById("toggleIcon");

                const isPasswordVisible = passwordInput.type === "text";
                passwordInput.type = isPasswordVisible ? "password" : "text";

                toggleIcon.classList.toggle("fa-eye");
                toggleIcon.classList.toggle("fa-eye-slash");
            }

            $('#signupForm').on('submit', async function(e) {
                e.preventDefault();

                const email = $('#email').val();
                const password = $('#password').val();

                try {
                    const accountRef = window.db.collection('account').where('email', '==', email);
                    const querySnapshot = await accountRef.get();

                    if (!querySnapshot.empty) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Email already in use',
                            text: 'Please use a different email address.',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#email').val('').focus();
                                $('#password').val('');
                            }
                        })

                        return;
                    }

                    const hashedPassword = CryptoJS.SHA256(password).toString();

                    await db.collection('account').add({
                        email: email,
                        password: hashedPassword,
                        role: 'user'
                    })

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
