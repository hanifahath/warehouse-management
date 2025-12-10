<x-guest-layout>

<form method="POST" action="{{ route('supplier.register') }}">
    @csrf
    <input name="name" placeholder="Nama" required>
    <input name="email" placeholder="Email" type="email" required>
    <input name="password" placeholder="Password" type="password" required>
    <input name="password_confirmation" placeholder="Konfirmasi Password" type="password" required>
    <input type="hidden" name="role" value="Supplier">

    <input name="company_name" placeholder="Nama Perusahaan" required>
    <input name="company_address" placeholder="Alamat Perusahaan" required>
    <input name="phone" placeholder="No Telepon" required>

    <button type="submit">Daftar</button>
</form>

</x-guest-layout>
