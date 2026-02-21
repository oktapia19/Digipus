<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\KategoriBuku;
use App\Models\Peminjaman;
use App\Models\User;
use App\Services\AppNotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PetugasController extends Controller
{
    public function dashboard()
    {
        $peminjamans = Peminjaman::with('user', 'book')->latest()->get();
        $peminjamansWaitingReturn = Peminjaman::with('user', 'book')
            ->where('status', 'waiting_return')
            ->latest()
            ->get();
        $borrowedBooks = $peminjamans
            ->whereIn('status', ['confirmed', 'waiting_return'])
            ->count();
        $returnedBooks = $peminjamans
            ->where('status', 'returned')
            ->count();
        $lateBooks = $this->buildLateBorrowItems(5000)->count();

        return view('petugas.dashboard', compact(
            'peminjamans',
            'peminjamansWaitingReturn',
            'borrowedBooks',
            'returnedBooks',
            'lateBooks'
        ));
    }

    public function books()
    {
        $books = Book::with('kategoris')->latest()->paginate(10);
        return view('petugas.books.index', compact('books'));
    }

    public function create()
    {
        $kategori = KategoriBuku::all();
        return view('petugas.books.create', compact('kategori'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul' => 'required|string',
            'isbn' => 'required|digits:8|unique:books,isbn',
            'penulis' => 'nullable|string',
            'penerbit' => 'nullable|string',
            'tahun' => 'nullable|integer',
            'stok' => 'required|integer|min:0',
            'sinopsis' => 'nullable|string',
            'cover' => 'nullable|image',
            'kategori' => 'nullable|array',
        ]);

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('uploads/covers', 'public');
        }

        $data['status'] = 'menunggu_konfirmasi';

        $book = Book::create($data);

        if ($request->kategori) {
            $book->kategoris()->sync($request->kategori);
        }

        AppNotificationService::toAdmins(
            'Permintaan buku baru dari petugas',
            "Buku '{$book->judul}' menunggu konfirmasi admin.",
            '/admin/books/pending'
        );

        return redirect()
            ->route('petugas.books.index')
            ->with('success', 'Buku berhasil ditambahkan dan menunggu persetujuan admin.');
    }

    public function edit(Book $book)
    {
        $kategori = KategoriBuku::all();
        return view('petugas.books.edit', compact('book', 'kategori'));
    }

    public function update(Request $request, Book $book)
    {
        if (in_array($book->status, ['menunggu_konfirmasi', 'menunggu_hapus'], true)) {
            return back()->with('error', 'Buku masih dalam proses konfirmasi admin.');
        }

        $data = $request->validate([
            'judul' => 'required|string',
            'isbn' => ['required', 'digits:8', Rule::unique('books', 'isbn')->ignore($book->id)],
            'penulis' => 'nullable|string',
            'penerbit' => 'nullable|string',
            'tahun' => 'nullable|integer',
            'stok' => 'required|integer|min:0',
            'sinopsis' => 'nullable|string',
            'cover' => 'nullable|image',
            'kategori' => 'nullable|array',
        ]);

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('uploads/covers', 'public');
        }

        $data['status'] = 'menunggu_konfirmasi';
        $book->update($data);

        if ($request->kategori) {
            $book->kategoris()->sync($request->kategori);
        }

        AppNotificationService::toAdmins(
            'Permintaan edit buku dari petugas',
            "Perubahan buku '{$book->judul}' menunggu konfirmasi admin.",
            '/admin/books/pending'
        );

        return redirect()
            ->route('petugas.books.index')
            ->with('success', 'Perubahan buku dikirim dan menunggu persetujuan admin.');
    }

    public function destroy(Book $book)
    {
        if ($book->status === 'menunggu_konfirmasi') {
            return back()->with('error', 'Buku masih dalam proses konfirmasi admin.');
        }

        if ($book->status === 'menunggu_hapus') {
            return back()->with('error', 'Buku ini sudah dalam antrian hapus.');
        }

        $book->update(['status' => 'menunggu_hapus']);

        AppNotificationService::toAdmins(
            'Permintaan hapus buku dari petugas',
            "Buku '{$book->judul}' diajukan untuk dihapus.",
            '/admin/books/pending'
        );

        return back()->with('success', 'Permintaan hapus dikirim ke admin.');
    }

    public function show(Book $book)
    {
        return view('petugas.books.show', compact('book'));
    }

    public function users()
    {
        $users = User::latest()->paginate(10);
        return view('petugas.users.index', compact('users'));
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

    public function profile()
    {
        $petugas = auth('petugas')->user();
        $userCount = User::count();
        $borrowedBooks = Peminjaman::whereIn('status', ['confirmed', 'waiting_return'])->count();
        $returnedBooks = Peminjaman::where('status', 'returned')->count();
        $lateBooks = $this->buildLateBorrowItems(5000)->count();

        return view('petugas.profile', compact(
            'petugas',
            'userCount',
            'borrowedBooks',
            'returnedBooks',
            'lateBooks'
        ));
    }

    public function denda()
    {
        $perHari = 5000;
        $items = $this->buildLateBorrowItems($perHari);

        return view('petugas.denda', compact('items', 'perHari'));
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

    public function updateProfile(Request $request)
    {
        $petugas = auth('petugas')->user();

        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('petugas', 'email')->ignore($petugas->id)],
            'alamat' => 'nullable|string|max:255',
            'no_telepon' => 'nullable|string|max:30',
        ]);

        $petugas->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $petugas = auth('petugas')->user();

        if ($petugas->photo) {
            Storage::disk('public')->delete('profile/petugas/' . $petugas->photo);
        }

        $filename = time() . '_' . $petugas->id . '.' . $request->file('photo')->getClientOriginalExtension();
        $request->file('photo')->storeAs('profile/petugas', $filename, 'public');

        $petugas->update([
            'photo' => $filename,
        ]);

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    private function mapPaperColor(string $paper): string
    {
        return match ($paper) {
            'cream' => '#FFF9EC',
            'gray' => '#F3F4F6',
            default => '#FFFFFF',
        };
    }
}
