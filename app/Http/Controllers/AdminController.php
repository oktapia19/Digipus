<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\KategoriBuku;
use App\Models\Peminjaman;
use App\Models\Petugas;
use App\Models\Role;
use App\Models\User;
use App\Services\AppNotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function dashboard()
    {
        $peminjamans = Peminjaman::with('user', 'book')->latest()->get();
        $peminjamansWaitingReturn = Peminjaman::with('user', 'book')
            ->where('status', 'waiting_return')
            ->latest()
            ->get();

        return view('admin.dashboard', [
            'totalBuku' => Book::where('status', 'disetujui')->count(),
            'totalPeminjam' => Peminjaman::whereNotNull('user_id')->distinct('user_id')->count('user_id'),
            'totalPetugas' => Petugas::count(),
            'peminjamans' => $peminjamans,
            'peminjamansWaitingReturn' => $peminjamansWaitingReturn,
        ]);
    }

    public function dataBuku()
    {
        $books = Book::with(['kategoris', 'reviews.user'])
            ->where('status', 'disetujui')
            ->latest()
            ->get();
        return view('admin.books.index', compact('books'));
    }

    public function pendingBooks()
    {
        $pendingBooks = Book::with('kategoris')
            ->whereIn('status', ['menunggu_konfirmasi', 'menunggu_hapus', 'disetujui', 'ditolak'])
            ->orderByRaw("CASE 
                WHEN status = 'menunggu_konfirmasi' THEN 1
                WHEN status = 'menunggu_hapus' THEN 2
                WHEN status = 'ditolak' THEN 3
                WHEN status = 'disetujui' THEN 4
                ELSE 5 END")
            ->latest()
            ->get();

        return view('admin.books.pending', compact('pendingBooks'));
    }

    public function approveBuku($id)
    {
        $book = Book::findOrFail($id);
        $judul = $book->judul;

        if ($book->status === 'menunggu_hapus') {
            if ($book->cover) {
                Storage::disk('public')->delete($book->cover);
            }

            $book->kategoris()->detach();
            $book->delete();

            AppNotificationService::toPetugas(
                'Permintaan hapus disetujui',
                "Buku '{$judul}' telah dihapus oleh admin.",
                '/petugas/books'
            );

            return back()->with('success', 'Permintaan hapus disetujui. Buku dihapus permanen.');
        }

        $book->update(['status' => 'disetujui']);
        AppNotificationService::toPetugas(
            'Buku disetujui admin',
            "Buku '{$book->judul}' sudah disetujui.",
            '/petugas/books'
        );
        return back()->with('success', 'Buku disetujui.');
    }

    public function rejectBuku($id)
    {
        $book = Book::findOrFail($id);

        if ($book->status === 'menunggu_hapus') {
            $book->update(['status' => 'disetujui']);
            AppNotificationService::toPetugas(
                'Permintaan hapus ditolak',
                "Permintaan hapus buku '{$book->judul}' ditolak admin.",
                '/petugas/books'
            );
            return back()->with('success', 'Permintaan hapus ditolak. Buku tetap aktif.');
        }

        $book->update(['status' => 'ditolak']);
        AppNotificationService::toPetugas(
            'Buku ditolak admin',
            "Buku '{$book->judul}' ditolak admin.",
            '/petugas/books'
        );
        return back()->with('success', 'Buku ditolak.');
    }

    public function createBuku()
    {
        $kategori = KategoriBuku::all();
        return view('admin.books.create', compact('kategori'));
    }

    public function storeBuku(Request $request)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'isbn' => 'required|digits:8|unique:books,isbn',
            'penulis' => 'nullable|string|max:255',
            'penerbit' => 'nullable|string|max:255',
            'tahun' => 'nullable|integer|min:1900|max:' . date('Y'),
            'stok' => 'required|integer|min:0',
            'sinopsis' => 'nullable|string',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'kategori' => 'required|array',
            'kategori.*' => 'exists:kategori_buku,id',
        ]);

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }

        $data['status'] = 'disetujui';

        $book = Book::create($data);
        $book->kategoris()->sync($request->kategori);

        return redirect()->route('admin.books.index')
            ->with('success', 'Buku berhasil ditambahkan.');
    }

    public function editBuku(Book $book)
    {
        $kategori = KategoriBuku::all();
        $book->load('kategoris');

        return view('admin.books.edit', compact('book', 'kategori'));
    }

    public function updateBuku(Request $request, Book $book)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'isbn' => ['required', 'digits:8', Rule::unique('books', 'isbn')->ignore($book->id)],
            'penulis' => 'nullable|string|max:255',
            'penerbit' => 'nullable|string|max:255',
            'tahun' => 'nullable|integer|min:1900|max:' . date('Y'),
            'stok' => 'required|integer|min:0',
            'sinopsis' => 'nullable|string',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'kategori' => 'nullable|array',
            'kategori.*' => 'exists:kategori_buku,id',
        ]);

        if ($request->hasFile('cover')) {
            if ($book->cover) {
                Storage::disk('public')->delete($book->cover);
            }
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }

        $book->update($data);
        $book->kategoris()->sync($request->kategori ?? []);

        return redirect()->route('admin.books.index')
            ->with('success', 'Buku berhasil diupdate.');
    }

    public function deleteBuku(Book $book)
    {
        if ($book->cover) {
            Storage::disk('public')->delete($book->cover);
        }

        $book->kategoris()->detach();
        $book->delete();

        return back()->with('success', 'Buku dihapus.');
    }

    public function showBuku(Book $book)
    {
        return redirect()->route('admin.books.edit', $book);
    }

    public function exportPdf(Request $request)
    {
        $books = Book::with('kategoris')->where('status', 'disetujui')->get();
        $paperColor = $this->mapPaperColor($request->query('paper', 'white'));
        $mode = $request->query('mode', 'download');

        $pdf = Pdf::loadView('pdf.books-report', compact('books', 'paperColor'))
            ->setPaper('A4');

        if ($mode === 'print') {
            return $pdf->stream('laporan-buku-' . now()->format('d-m-Y') . '.pdf');
        }

        return $pdf->download('laporan-buku-' . now()->format('d-m-Y') . '.pdf');
    }

    public function exportPendingPdf(Request $request)
    {
        $books = Book::with('kategoris')
            ->whereIn('status', ['menunggu_konfirmasi', 'menunggu_hapus', 'disetujui', 'ditolak'])
            ->orderByRaw("CASE 
                WHEN status = 'menunggu_konfirmasi' THEN 1
                WHEN status = 'menunggu_hapus' THEN 2
                WHEN status = 'ditolak' THEN 3
                WHEN status = 'disetujui' THEN 4
                ELSE 5 END")
            ->get();
        $paperColor = $this->mapPaperColor($request->query('paper', 'white'));
        $mode = $request->query('mode', 'download');

        $pdf = Pdf::loadView('pdf.pending-books-report', compact('books', 'paperColor'))
            ->setPaper('A4');

        if ($mode === 'print') {
            return $pdf->stream('laporan-konfirmasi-buku-' . now()->format('d-m-Y') . '.pdf');
        }

        return $pdf->download('laporan-konfirmasi-buku-' . now()->format('d-m-Y') . '.pdf');
    }

    public function dataUser()
    {
        $users = User::with('role')->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        return redirect()
            ->route('admin.users.index')
            ->with('error', 'Penambahan user dinonaktifkan. Admin hanya bisa print dan hapus user.');
    }

    public function storeUser(Request $request)
    {
        return redirect()
            ->route('admin.users.index')
            ->with('error', 'Penambahan user dinonaktifkan. Admin hanya bisa print dan hapus user.');
    }

    public function destroyUser(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Tidak bisa hapus akun sendiri.');
        }

        $user->delete();
        return back()->with('success', 'User dihapus.');
    }

    public function exportUsersPdf(Request $request)
    {
        $users = User::all();
        $paperColor = $this->mapPaperColor($request->query('paper', 'white'));
        $mode = $request->query('mode', 'download');

        $pdf = Pdf::loadView('pdf.users-report', compact('users', 'paperColor'))
            ->setPaper('A4');

        if ($mode === 'print') {
            return $pdf->stream('laporan-user-' . now()->format('d-m-Y') . '.pdf');
        }

        return $pdf->download('laporan-user-' . now()->format('d-m-Y') . '.pdf');
    }

    public function dataPetugas()
    {
        $petugas = Petugas::latest()->paginate(10);
        return view('admin.petugas.index', compact('petugas'));
    }

    public function createPetugas()
    {
        return view('admin.petugas.create');
    }

    public function storePetugas(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'email' => 'required|email|unique:petugas',
            'password' => 'required|min:6',
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string',
        ]);

        $payload = [
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telepon,
        ];

        if (Schema::hasTable('roles') && Schema::hasColumn('petugas', 'role_id')) {
            $payload['role_id'] = Role::firstOrCreate(['name' => 'petugas'])->id;
        }

        Petugas::create($payload);

        return redirect()->route('admin.petugas.index')
            ->with('success', 'Petugas ditambahkan.');
    }

    public function editPetugas($id)
    {
        $petugas = Petugas::findOrFail($id);
        return view('admin.petugas.edit', compact('petugas'));
    }

    public function updatePetugas(Request $request, $id)
    {
        $petugas = Petugas::findOrFail($id);

        $request->validate([
            'nama' => 'required',
            'email' => ['required', 'email', Rule::unique('petugas', 'email')->ignore($petugas->id)],
            'password' => 'nullable|min:6',
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string',
        ]);

        $data = [
            'nama' => $request->nama,
            'email' => $request->email,
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telepon,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $petugas->update($data);

        return redirect()->route('admin.petugas.index')
            ->with('success', 'Petugas berhasil diupdate.');
    }

    public function destroyPetugas($id)
    {
        $petugas = Petugas::findOrFail($id);
        $petugas->delete();
        return back()->with('success', 'Petugas dihapus.');
    }

    public function exportPetugasPdf(Request $request)
    {
        $petugas = Petugas::all();
        $paperColor = $this->mapPaperColor($request->query('paper', 'white'));
        $mode = $request->query('mode', 'download');

        $pdf = Pdf::loadView('pdf.petugas-report', compact('petugas', 'paperColor'))
            ->setPaper('A4');

        if ($mode === 'print') {
            return $pdf->stream('laporan-petugas-' . now()->format('d-m-Y') . '.pdf');
        }

        return $pdf->download('laporan-petugas-' . now()->format('d-m-Y') . '.pdf');
    }

    private function mapPaperColor(string $paper): string
    {
        return match ($paper) {
            'cream' => '#FFF9EC',
            'gray' => '#F3F4F6',
            default => '#FFFFFF',
        };
    }

    public function profile()
    {
        $admin = auth('admin')->user();
        $petugasCount = Petugas::count();
        $userCount = User::count();
        $borrowedBooks = Peminjaman::whereIn('status', ['confirmed', 'waiting_return'])->count();
        $returnedBooks = Peminjaman::where('status', 'returned')->count();
        $lateBooks = $this->buildLateBorrowItems(5000)->count();

        return view('admin.profile', compact(
            'admin',
            'petugasCount',
            'userCount',
            'borrowedBooks',
            'returnedBooks',
            'lateBooks'
        ));
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $admin = auth('admin')->user();

        if ($admin->photo) {
            Storage::disk('public')->delete('profile/admin/' . $admin->photo);
        }

        $filename = time() . '_' . $admin->id . '.' . $request->file('photo')->getClientOriginalExtension();
        $request->file('photo')->storeAs('profile/admin', $filename, 'public');

        $admin->update([
            'photo' => $filename,
        ]);

        return back()->with('success', 'Foto profil admin berhasil diperbarui.');
    }

    public function denda()
    {
        $perHari = 5000;
        $items = $this->buildLateBorrowItems($perHari);

        return view('admin.denda', compact('items', 'perHari'));
    }

    private function buildLateBorrowItems(int $perHari)
    {
        $today = \Carbon\Carbon::today();

        return Peminjaman::with('user', 'book')
            ->whereNotNull('tanggal_kembali')
            ->whereIn('status', ['confirmed', 'waiting_return', 'returned'])
            ->get()
            ->map(function ($p) use ($today, $perHari) {
                $due = \Carbon\Carbon::parse($p->tanggal_kembali)->startOfDay();
                $returnDate = $p->status === 'returned'
                    ? \Carbon\Carbon::parse($p->tanggal_pengembalian ?? $p->updated_at)->startOfDay()
                    : $today->copy()->startOfDay();
                $dendaTambahan = (int) ($p->denda_tambahan ?? 0);

                if ($returnDate->lte($due) && $dendaTambahan <= 0) {
                    return null;
                }

                $lateDays = $returnDate->gt($due) ? $due->diffInDays($returnDate) : 0;
                $p->late_days = $lateDays;
                $p->denda = ($lateDays * $perHari) + $dendaTambahan;
                $p->return_date = $p->status === 'returned' ? ($p->tanggal_pengembalian ?? $p->updated_at) : null;
                return $p;
            })
            ->filter()
            ->values();
    }
}
