<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">

        <title>{{ $title ?? 'IoT Toilet Monitoring' }}</title>

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
        {{-- Font Awesome --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

        <style>
            body {
                background: url("{{ asset('css/images/background.jpg') }}") no-repeat center center fixed;
                background-size: cover;
            }
        </style>
    </head>

    <body>
        <div class="main-wrapper">
            {{ $slot }}
        </div>
        {{-- Bootstrap & Popper --}}
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
        </script>
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        {{-- SWAL Alert --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- CryptoJS for password hashing -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.2.0/crypto-js.min.js"></script>
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

            $$(document).ready(function() {
                const email = localStorage.getItem('userEmail');
                const role = localStorage.getItem('userRole');

                if (email) {
                    if (role === 'admin') {
                        window.location.href = '/admin-dashboard';
                    } else {
                        window.location.href = '/user-dashboard';
                    }
                }
            });
        </script>
        @stack('scripts')
    </body>

</html>
