<x-layout title="Login">
    <div class="auth-container">
        <h2>Login</h2>
        <form id="loginForm">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3 position-relative">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control pe-5" id="password" name="password" required>
                <span class="password-toggle-icon" onclick="togglePassword()">
                    <i id="toggleIcon" class="fa-solid fa-eye-slash"></i>
                </span>
            </div>
            <br>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        <div class="toggle-link">
            <span>Don't have an account? <a href="/signup">Sign Up</a></span>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                const savedRole = localStorage.getItem("userRole");
                const savedEmail = localStorage.getItem("userEmail");
                if (savedEmail) {
                    if (savedRole === 'admin') {
                        console.log('admin');
                        //window.location.href = '/admin-dashboard';
                    } else {
                        console.log('user');
                        //window.location.href = '/user-dashboard';
                    }
                } else {
                    return;
                }
            });

            function togglePassword() {
                const passwordInput = document.getElementById("password");
                const toggleIcon = document.getElementById("toggleIcon");

                const isPasswordVisible = passwordInput.type === "text";
                passwordInput.type = isPasswordVisible ? "password" : "text";

                toggleIcon.classList.toggle("fa-eye");
                toggleIcon.classList.toggle("fa-eye-slash");
            }

            $('#loginForm').on('submit', async function(e) {
                e.preventDefault();
                const email = $('#email').val();
                const password = $('#password').val();

                const accountRef = window.db.collection("account").where("email", "==", email).limit(1);
                const querySnapshot = await accountRef.get();

                if (!querySnapshot.empty) {
                    const user = querySnapshot.docs[0].data();
                    if (user.password === CryptoJS.SHA256(password).toString()) {
                        localStorage.setItem('userEmail', user.email);
                        localStorage.setItem('userRole', user.role);

                        $.ajax({
                            url: '/set-role',
                            type: 'POST',
                            contentType: 'application/json',
                            data: JSON.stringify({
                                role: user.role
                            }),
                            success: function(res) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Login Successful',
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    window.location.href = res.savedRole === 'admin' ?
                                        '/admin-dashboard' : '/user-dashboard';
                                });
                            },
                            error: function() {
                                Swal.fire('Error', 'Login Failed');
                            }
                        });
                    } else {
                        Swal.fire('Error', 'Invalid password', 'error');
                    }
                } else {
                    Swal.fire('Error', 'User not found', 'error');
                }
            });
        </script>
    @endpush
</x-layout>
