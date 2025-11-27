<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Pengguna (Admin)</title>
</head>
<body>
    <h2>Manajemen Pengguna</h2>
    
    @if (session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div style="color: red;">{{ session('error') }}</div>
    @endif

    <h3>Daftar Supplier (Filter Pending)</h3>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status Approval</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td style="color: {{ $user->is_approved ? 'green' : 'red' }};">
                        {{ $user->is_approved ? 'Approved' : 'PENDING' }}
                    </td>
                    <td>
                        @if ($user->role === 'Supplier' && !$user->is_approved)
                            <form action="{{ route('users.approve_supplier', $user) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" onclick="return confirm('Setujui Supplier ini?')" 
                                        style="color: blue;">Approve</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    {{ $users->links() }}
</body>
</html>