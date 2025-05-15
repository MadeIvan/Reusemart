@extends('Pegawai.ViewCS.MainNavbarCS')

@section('content')
    {{-- Home Section --}}
    <h1>Welcome</h1>
    <p>You are logged in as <strong>Customer Services</strong>.</p>

    <hr>

    {{-- CS Profile Section --}}
    <h2>Customer Services Profile</h2>
    <table class="table table-bordered w-50">
        <tr>
            <th>ID Pegawai</th>
            <td>0000</td>
        </tr>
        <tr>
            <th>Nama Pegawai</th>
            <td>Made Ivan</td>
        </tr>
        <tr>
            <th>Tanggal Lahir</th>
            <td>1231231231</td>
        </tr>
    </table>
@endsection

