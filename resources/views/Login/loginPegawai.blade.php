<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Login Pegawai</title>

  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    crossorigin="anonymous"
  />

  <!-- FontAwesome -->
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    crossorigin="anonymous"
  />

  <style>
    body, html {
      height: 100%;
      margin: 0;
      overflow: hidden; /* Prevent scrolling */
    }

    #video-bg {
      position: fixed;
      top: 0; left: 0;
      width: 100vw;
      height: 100vh;
      object-fit: cover;
      z-index: -1;
    }

    .login-form-bg {
      background-color: rgba(255, 255, 255, 0.9);
    }

    .custom-heading-color {
      color: rgb(24, 134, 4) !important;
      font-weight: bold;
    }

    .toggle-password {
      cursor: pointer;
      user-select: none;
    }

    .login-button button:hover {
      background-color: #006666;
    }
  </style>
</head>
<body>
  @include('layouts.navbarVideo')

  <video autoplay muted loop id="video-bg">
    <source src="{{ asset('ReUseMartVid.mp4') }}" type="video/mp4" />
    Your browser does not support the video tag.
  </video>

  <div class="d-flex justify-content-center align-items-center vh-100 position-relative" style="z-index: 1;">
    <div class="col-11 col-sm-8 col-md-6 col-lg-4 login-form-bg p-4 rounded shadow">
      <h2 class="text-center custom-heading-color mb-4">Login Pegawai</h2>
      <form id="loginForm" autocomplete="off">
        <div class="mb-3">
          <label for="username" class="form-label"><strong>Username</strong></label>
          <input type="text" class="form-control" id="username" autocomplete="username" required />
        </div>
        <div class="mb-3">
          <label for="password" class="form-label"><strong>Password</strong></label>
          <div class="input-group">
            <input type="password" class="form-control" id="password" autocomplete="current-password" required />
            <button class="btn btn-outline-secondary toggle-password" type="button" id="togglePassword">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
        </div>
        <div class="d-grid gap-2 login-button">
          <button type="submit" class="btn btn-success" id="loginButton">Login</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    crossorigin="anonymous"
  ></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      const passwordField = document.getElementById('password');
      const togglePasswordButton = document.getElementById('togglePassword');

      if (togglePasswordButton && passwordField) {
        togglePasswordButton.addEventListener('click', function () {
          const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
          passwordField.setAttribute('type', type);
          this.querySelector('i').classList.toggle('fa-eye');
          this.querySelector('i').classList.toggle('fa-eye-slash');
        });
      }

      document.querySelector('#loginForm').addEventListener('submit', async function(event) {
        event.preventDefault();

        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value.trim();

        if (!username || !password) {
          alert('Mohon untuk mengisi username dan password.');
          return;
        }

        try {
          const response = await fetch(`http://127.0.0.1:8000/api/pegawai/login`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ username, password })
          });

          const resData = await response.json();

          if (!response.ok) {
            alert(resData.message || 'Login failed');
            return;
          }

          alert('Login successful!');
          const IdJabatan = resData.data.pegawai.idJabatan;
          const namaPegawai = resData.data.pegawai.namaPegawai;

          localStorage.setItem('auth_token', resData.data.token);

          if(IdJabatan==1){
            alert('Login As Owner!');
            localStorage.setItem('user_role', 'owner');
            window.location.href = "/pegawaidata";
          } else if(IdJabatan==2){
            alert('Login As Admin!');
            localStorage.setItem('user_role', 'admin');
            window.location.href = 'http://127.0.0.1:8000/organisasi';
          } else if(IdJabatan==3){
            alert('Login As Pegawai Gudang!');
            localStorage.setItem('user_role', 'gudang');
            localStorage.setItem('namaPegawai', namaPegawai);
            window.location.href = "/pegawaidata";
          } else if(IdJabatan==4){
            alert('Login As Kurir!');
            localStorage.setItem('user_role', 'kurir');
            window.location.href = "/pegawaidata";
          } else if(IdJabatan==5){
            alert('Login As CS!');
            localStorage.setItem('user_role', 'cs');
            window.location.href = "/pegawaidata";
          } else if(IdJabatan==6){
            alert('Login As Hunter!');
            localStorage.setItem('user_role', 'hunter');
            window.location.href = "/pegawaidata";
          }
          localStorage.setItem('pegawaiData', JSON.stringify(resData.data.pegawai));
        } catch (error) {
          console.error('Error:', error);
          alert('An error occurred: ' + error.message);
        }
      });
    });
  </script>
</body>
</html>
