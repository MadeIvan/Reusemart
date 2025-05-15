@extends('Pegawai.ViewCS.MainNavbarCS')

@section('content')
<h2>Data Penitip</h2>
<form id="penitipForm">
    <input type="hidden" id="currentPenitipId">
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="namaPenitip" class="form-label">Nama Penitip</label>
            <input type="text" class="form-control" id="namaPenitip" required>
        </div>
        <div class="col-md-4">
            <label for="nik" class="form-label">NIK</label>
            <input type="text" class="form-control" id="nik" required>
        </div>
        <div class="col-md-4">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" required>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label for="idTopSeller" class="form-label">ID Top Seller</label>
            <input type="text" class="form-control" id="idTopSeller" disabled>
        </div>
        <div class="col-md-4">
            <label for="idDompet" class="form-label">ID Dompet</label>
            <input type="text" class="form-control" id="idDompet" disabled>
        </div>
    </div>

    <div class="row">
        <div class="col-12 btn-container">
            <button type="submit" class="btn btn-success">Save</button>
            <button type="button" class="btn btn-danger" id="deleteButton">Delete</button>
            <button type="button" class="btn btn-primary" id="registerButton">Register Penitip</button>
        </div>
    </div>
</form>

<div id="registerOverlay" class="overlay"></div>
<div class="register-form-container" id="registerFormContainer">
    <h4>Register New Penitip</h4>
    <form id="registerForm">
        <div class="mb-3">
            <label for="registerNamaPenitip" class="form-label">Nama Penitip</label>
            <input type="text" class="form-control" id="registerNamaPenitip" required>
        </div>
        <div class="mb-3">
            <label for="registerNik" class="form-label">NIK</label>
            <input type="text" class="form-control" id="registerNik" required>
        </div>
        <div class="mb-3">
            <label for="registerUsername" class="form-label">Username</label>
            <input type="text" class="form-control" id="registerUsername" required>
        </div>
        <div class="mb-3">
            <label for="registerPassword" class="form-label">Password</label>
            <input type="password" class="form-control" id="registerPassword" required>
        </div>
        <div class="mb-3">
            <button type="submit" class="btn btn-success">Register</button>
            <button type="button" class="btn btn-secondary" id="closeRegisterForm">Close</button>
        </div>
    </form>
</div>

<div class="toast-container">
    <div class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true" id="successToast">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">
                Action completed successfully!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<div class="container mt-4">
    <h3>Penitip Data</h3>
    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search by username or name">

    <div id="penitipTableContainer">
        <table class="table table-bordered" id="penitipTable">
            <thead>
                <tr>
                    <th>ID Penitip</th>
                    <th>ID Top Seller</th>
                    <th>ID Dompet</th>
                    <th>Username</th>
                    <th>Nama Penitip</th>
                    <th>NIK</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <!-- Data will be populated here from the server -->
            </tbody>
        </table>
    </div>
</div>
@endsection
