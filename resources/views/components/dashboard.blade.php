<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">

        <title>{{ $title . ' Dashboard' }}</title>

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
        {{-- Font Awesome --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
        {{-- Datatables --}}
        <link rel="stylesheet" href="https://cdn.datatables.net/2.3.0/css/dataTables.bootstrap5.min.css" />

        <style>
            <style>body {
                background-color: #f8f9fa;
            }

            .navbar-brand {
                font-weight: bold;
            }

            .table-container {
                margin-top: 30px;
            }

            tbody td {
                text-align: center;
            }
        </style>
    </head>

    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">{{ $title }} Dashboard</a>
                <div class="d-flex ms-auto">
                    <button class="btn btn-outline-light" id="logoutBtn"><i
                            class="fas fa-sign-out-alt me-2"></i>Logout</button>
                </div>
            </div>
        </nav>

        <div class="container table-container">
            {{ $slot }}
        </div>
        {{-- Bootstrap & Popper --}}
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
        </script>
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
        {{-- SWAL Alert --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- CryptoJS for password hashing -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.2.0/crypto-js.min.js"></script>
        {{-- Datatables --}}
        <script src="https://cdn.datatables.net/2.3.0/js/dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/2.3.0/js/dataTables.bootstrap5.min.js"></script>
        {{-- Midtrans --}}
        <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.client_key') }}"></script>
        <!-- Firebase SDK -->
        <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
        <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-firestore-compat.js"></script>
        <script src="https://www.gstatic.com/firebasejs/9.22.1/firebase-auth-compat.js"></script>
        <script>
            const firebaseConfig = {
                apiKey: "{{ config('services.firebase.apiKey') }}",
                authDomain: "{{ config('services.firebase.authDomain') }}",
                projectId: "{{ config('services.firebase.projectId') }}",
                storageBucket: "{{ config('services.firebase.storageBucket') }}",
                messagingSenderId: "{{ config('services.firebase.messagingSenderId') }}",
                appId: "{{ config('services.firebase.appId') }}",
                measurementId: "{{ config('services.firebase.measurementId') }}"
            };

            firebase.initializeApp(firebaseConfig);
            const db = firebase.firestore();
            const auth = firebase.auth();

            auth.signInAnonymously();
            window.db = db;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#logoutBtn').on('click', function() {
                localStorage.clear();

                $.ajax({
                    url: '/logout',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content')
                    },
                    success: function() {
                        Swal.fire({
                            title: "Are you sure?",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Yes"
                        }).then((result) => {
                            window.location.href = '/login';
                        });
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Logout Error');
                    }
                });
            });
        </script>
        @stack('scripts')
    </body>

</html>
