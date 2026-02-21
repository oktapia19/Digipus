<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Book;
use App\Services\AppNotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class PeminjamanController extends Controller
{
    private const DENDA_PER_HARI = 5000;
    private const DENDA_KERUSAKAN = 5000;
    private const MAX_ACTIVE_BORROW = 2;

    public function create(Request $request, Book $book)
    {
        $userId = (int) $request->user()->id;
        if ($this->hasReachedActiveBorrowLimit($userId)) {
            return redirect()
                ->route('books.show', $book->id)
                ->with('error', 'Maksimal peminjaman 2 buku aktif. Kembalikan buku terlebih dahulu.');
        }

        return view('peminjaman.create', compact('book'));
    }

    public function store(Request $request, Book $book)
    {
        $userId = (int) $request->user()->id;
        if ($this->hasReachedActiveBorrowLimit($userId)) {
            return redirect()
                ->route('books.show', $book->id)
                ->with('error', 'Maksimal peminjaman 2 buku aktif. Kembalikan buku terlebih dahulu.');
        }

        $request->validate([
            'nama_lengkap' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string|max:50',
            'durasi' => 'required|integer|min:1',
            'durasi_satuan' => 'required|in:hari,jam',
            'agree' => 'required|accepted',
        ]);

        $user = $request->user();

        // update user's contact if provided
        if ($request->filled('no_telepon') || $request->filled('alamat') || $request->filled('nama_lengkap')) {
            $user->update([
                'no_telepon' => $request->input('no_telepon') ?? $user->no_telepon,
                'alamat' => $request->input('alamat') ?? $user->alamat,
                'nama_lengkap' => $request->input('nama_lengkap') ?? $user->nama_lengkap,
            ]);
        }

        $durasi = (int) $request->input('durasi', 1);
        $durasiSatuan = $request->input('durasi_satuan', 'hari');

        if ($durasiSatuan === 'hari' && $durasi > 12) {
            return back()->withErrors(['durasi' => 'Maksimal durasi 12 hari'])->withInput();
        }
        if ($durasiSatuan === 'jam' && $durasi > 24) {
            return back()->withErrors(['durasi' => 'Maksimal durasi 24 jam'])->withInput();
        }

        $tanggalPinjam = now();
        $tanggalKembali = $durasiSatuan === 'jam'
            ? \Carbon\Carbon::parse($tanggalPinjam)->addDay()
            : \Carbon\Carbon::parse($tanggalPinjam)->addDays($durasi);

        $p = Peminjaman::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'durasi' => $durasi,
            'durasi_satuan' => $durasiSatuan,
            'tanggal_kembali' => $tanggalKembali->toDateString(),
            'tanggal_pinjam' => $tanggalPinjam,
            'status' => 'pending',
            'kode' => strtoupper(Str::random(8)),
            'alamat' => $request->input('alamat') ?? $user->alamat,
            'no_telepon' => $request->input('no_telepon') ?? $user->no_telepon,
        ]);

        AppNotificationService::toAllStaff(
            'Permintaan peminjaman baru',
            "User {$user->email} mengajukan pinjam buku: {$book->judul}",
            '/admin/dashboard'
        );
        AppNotificationService::toUser(
            $user->id,
            'Peminjaman menunggu konfirmasi',
            "Permintaan pinjam buku {$book->judul} sedang diproses petugas/admin.",
            '/riwayat'
        );

        return redirect()->route('peminjaman.index')->with('success', 'Permintaan peminjaman dikirim. Tunggu konfirmasi admin.');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $list = Peminjaman::with(['book', 'review'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        // Stats
        $totalPinjam = $list->count();
        $menungguKonfirmasi = $list->where('status', 'pending')->count();
        $sudahDikembalikan = $list->where('status', 'returned')->count();
        
        return view('peminjaman.index', compact('list', 'totalPinjam', 'menungguKonfirmasi', 'sudahDikembalikan'));
    }

    public function show(Peminjaman $peminjaman)
    {
        $peminjaman->load('book', 'user');
        return view('peminjaman.show', compact('peminjaman'));
    }

    public function receipt(Peminjaman $peminjaman)
    {
        // Only the user who borrowed the book or admin/petugas can view the receipt
        if ($peminjaman->user_id !== auth()->id() && !auth('admin')->check() && !auth('petugas')->check()) {
            abort(403);
        }
        
        $peminjaman->load('book', 'user');
        return view('peminjaman.receipt', compact('peminjaman'));
    }

    public function receiptPdf(Peminjaman $peminjaman)
    {
        if ($peminjaman->user_id !== auth()->id() && !auth('admin')->check() && !auth('petugas')->check()) {
            abort(403);
        }

        $peminjaman->load('book', 'user');

        $pdf = Pdf::loadView('peminjaman.receipt-pdf', compact('peminjaman'))->setPaper('a4', 'portrait');
        return $pdf->download('bukti-peminjaman-' . $peminjaman->kode . '.pdf');
    }

    public function return(Peminjaman $peminjaman)
    {
        if($peminjaman->user_id !== auth()->id()){
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // status changed to waiting_return instead of returned
        $peminjaman->update(['status' => 'waiting_return']);

        AppNotificationService::toAllStaff(
            'Permintaan pengembalian buku',
            "User meminta verifikasi pengembalian: {$peminjaman->book->judul}",
            '/admin/dashboard'
        );
        AppNotificationService::toUser(
            (int) $peminjaman->user_id,
            'Pengembalian menunggu verifikasi',
            "Pengembalian buku {$peminjaman->book->judul} sedang menunggu verifikasi.",
            '/riwayat'
        );

        return response()->json(['message' => 'Permintaan pengembalian terkirim. Tunggu verifikasi admin.']);
    }

    // confirm by admin or petugas
    public function confirm(Request $request, Peminjaman $peminjaman)
    {
        // allow admin or petugas to confirm via their guards
        if (! auth('admin')->check() && ! auth('petugas')->check()) {
            abort(403);
        }

        $peminjaman->update([
            'status' => 'confirmed',
        ]);

        AppNotificationService::toUser(
            (int) $peminjaman->user_id,
            'Peminjaman dikonfirmasi',
            "Peminjaman buku anda berhasil. Buku: {$peminjaman->book->judul}.",
            '/riwayat'
        );

        // generate a placeholder receipt text file (could be replaced by PDF/image generator)
        $content = "Bukti Peminjaman - Kode: {$peminjaman->kode}\nUser: {$peminjaman->user->email}\nBook: {$peminjaman->book->judul}\nStatus: confirmed\n";
        $path = 'user/receipts/'.$peminjaman->kode.'.txt';
        Storage::disk('public')->put($path, $content);
        $peminjaman->update(['receipt_path' => $path]);

        return redirect()->back()->with('success', 'Peminjaman dikonfirmasi.');
    }

    // reject by admin or petugas
    public function reject(Request $request, Peminjaman $peminjaman)
    {
        // allow admin or petugas to reject via their guards
        if (! auth('admin')->check() && ! auth('petugas')->check()) {
            abort(403);
        }

        $peminjaman->update([
            'status' => 'rejected',
        ]);

        AppNotificationService::toUser(
            (int) $peminjaman->user_id,
            'Peminjaman ditolak',
            "Peminjaman buku {$peminjaman->book->judul} ditolak.",
            '/riwayat'
        );

        return redirect()->back()->with('success', 'Peminjaman dibatalkan.');
    }

    // confirm return by admin or petugas (verify code)
    public function returnConfirm(Request $request, Peminjaman $peminjaman)
    {
        // allow admin or petugas to confirm return via their guards
        if (! auth('admin')->check() && ! auth('petugas')->check()) {
            abort(403);
        }

        $request->validate([
            'kode_verifikasi' => 'required|string',
            'tanggal_pengembalian' => 'required|date',
            'kondisi_buku' => 'required|in:baik,rusak',
        ]);

        // verify code matches the original kode (trim whitespace, case-insensitive)
        $inputKode = strtoupper(trim($request->input('kode_verifikasi')));
        $kodeAsli = strtoupper(trim($peminjaman->kode));
        
        if ($inputKode !== $kodeAsli) {
            return response()->json(['success' => false, 'message' => 'Kode tidak sesuai!']);
        }

        $tanggalPengembalian = \Carbon\Carbon::parse($request->input('tanggal_pengembalian'))->startOfDay();
        $today = \Carbon\Carbon::today();
        if ($tanggalPengembalian->lt($today)) {
            return response()->json(['success' => false, 'message' => 'Tanggal pengembalian tidak boleh mundur!']);
        }

        $kondisiBuku = $request->input('kondisi_buku');
        $dendaTambahan = $kondisiBuku === 'rusak' ? self::DENDA_KERUSAKAN : 0;

        // update status to returned (safe when new columns are not migrated yet)
        $updatePayload = [
            'status' => 'returned',
            'tanggal_pengembalian' => $tanggalPengembalian,
        ];
        if (Schema::hasColumn('peminjamans', 'kondisi_buku')) {
            $updatePayload['kondisi_buku'] = $kondisiBuku;
        }
        if (Schema::hasColumn('peminjamans', 'denda_tambahan')) {
            $updatePayload['denda_tambahan'] = $dendaTambahan;
        }
        $peminjaman->update($updatePayload);

        AppNotificationService::toUser(
            (int) $peminjaman->user_id,
            'Pengembalian berhasil',
            "Pengembalian buku anda berhasil. Buku: {$peminjaman->book->judul}.",
            '/riwayat'
        );

        [$lateDays, $dendaTelat] = $this->calculateLateFee($peminjaman, $tanggalPengembalian);
        $dendaTotal = $dendaTelat + $dendaTambahan;
        $this->sendReturnFeeNotification($peminjaman, $lateDays, $dendaTelat, $dendaTambahan, $dendaTotal);

        $message = 'Pengembalian buku dikonfirmasi!';
        if ($dendaTotal > 0) {
            $parts = [];
            if ($dendaTelat > 0) {
                $parts[] = 'telat ' . $lateDays . ' hari (Rp ' . number_format($dendaTelat, 0, ',', '.') . ')';
            }
            if ($dendaTambahan > 0) {
                $parts[] = 'kerusakan buku (Rp ' . number_format($dendaTambahan, 0, ',', '.') . ')';
            }
            $message .= ' Denda: ' . implode(' + ', $parts) . '. Total Rp ' . number_format($dendaTotal, 0, ',', '.') . '.';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'late_days' => $lateDays,
            'denda' => $dendaTotal,
            'denda_telat' => $dendaTelat,
            'denda_tambahan' => $dendaTambahan,
            'kondisi_buku' => $kondisiBuku,
        ]);
    }

    // verify return by code (untuk admin/petugas - cari peminjaman berdasarkan kode)
    public function verifyByCode(Request $request)
    {
        if (! auth('admin')->check() && ! auth('petugas')->check()) {
            abort(403);
        }

        $request->validate([
            'kode_verifikasi' => 'required|string',
            'tanggal_pengembalian' => 'required|date',
            'kondisi_buku' => 'required|in:baik,rusak',
        ]);

        $tanggalPengembalian = \Carbon\Carbon::parse($request->input('tanggal_pengembalian'))->startOfDay();
        $today = \Carbon\Carbon::today();
        if ($tanggalPengembalian->lt($today)) {
            return response()->json(['success' => false, 'message' => 'Tanggal pengembalian tidak boleh mundur!']);
        }

        $kondisiBuku = $request->input('kondisi_buku');
        $dendaTambahan = $kondisiBuku === 'rusak' ? self::DENDA_KERUSAKAN : 0;

        $inputKode = strtoupper(trim($request->input('kode_verifikasi')));
        
        // Find peminjaman by kode
        $peminjaman = Peminjaman::where('status', 'waiting_return')
            ->where('kode', 'LIKE', $inputKode)
            ->first();

        if (!$peminjaman) {
            return response()->json(['success' => false, 'message' => 'Kode tidak ditemukan atau sudah dikonfirmasi!']);
        }

        // Update status to returned (safe when new columns are not migrated yet)
        $updatePayload = [
            'status' => 'returned',
            'tanggal_pengembalian' => $tanggalPengembalian,
        ];
        if (Schema::hasColumn('peminjamans', 'kondisi_buku')) {
            $updatePayload['kondisi_buku'] = $kondisiBuku;
        }
        if (Schema::hasColumn('peminjamans', 'denda_tambahan')) {
            $updatePayload['denda_tambahan'] = $dendaTambahan;
        }
        $peminjaman->update($updatePayload);

        AppNotificationService::toUser(
            (int) $peminjaman->user_id,
            'Pengembalian berhasil',
            "Pengembalian buku anda berhasil. Buku: {$peminjaman->book->judul}.",
            '/riwayat'
        );

        [$lateDays, $dendaTelat] = $this->calculateLateFee($peminjaman, $tanggalPengembalian);
        $dendaTotal = $dendaTelat + $dendaTambahan;
        $this->sendReturnFeeNotification($peminjaman, $lateDays, $dendaTelat, $dendaTambahan, $dendaTotal);

        $message = 'Pengembalian buku dikonfirmasi!';
        if ($dendaTotal > 0) {
            $parts = [];
            if ($dendaTelat > 0) {
                $parts[] = 'telat ' . $lateDays . ' hari (Rp ' . number_format($dendaTelat, 0, ',', '.') . ')';
            }
            if ($dendaTambahan > 0) {
                $parts[] = 'kerusakan buku (Rp ' . number_format($dendaTambahan, 0, ',', '.') . ')';
            }
            $message .= ' Denda: ' . implode(' + ', $parts) . '. Total Rp ' . number_format($dendaTotal, 0, ',', '.') . '.';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'late_days' => $lateDays,
            'denda' => $dendaTotal,
            'denda_telat' => $dendaTelat,
            'denda_tambahan' => $dendaTambahan,
            'kondisi_buku' => $kondisiBuku,
        ]);
    }

    private function hasReachedActiveBorrowLimit(int $userId): bool
    {
        $activeCount = Peminjaman::where('user_id', $userId)
            ->whereIn('status', ['pending', 'confirmed', 'waiting_return'])
            ->count();

        return $activeCount >= self::MAX_ACTIVE_BORROW;
    }

    private function calculateLateFee(Peminjaman $peminjaman, \Carbon\Carbon $tanggalPengembalian): array
    {
        $due = \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->startOfDay();
        if ($tanggalPengembalian->lte($due)) {
            return [0, 0];
        }

        $lateDays = $due->diffInDays($tanggalPengembalian);
        $denda = $lateDays * self::DENDA_PER_HARI;

        return [$lateDays, $denda];
    }

    private function sendReturnFeeNotification(
        Peminjaman $peminjaman,
        int $lateDays,
        int $dendaTelat,
        int $dendaTambahan,
        int $dendaTotal
    ): void {
        if ($dendaTotal <= 0) {
            return;
        }

        $parts = [];
        if ($dendaTelat > 0) {
            $parts[] = 'telat ' . $lateDays . ' hari: Rp ' . number_format($dendaTelat, 0, ',', '.');
        }
        if ($dendaTambahan > 0) {
            $parts[] = 'kerusakan buku: Rp ' . number_format($dendaTambahan, 0, ',', '.');
        }

        AppNotificationService::toUser(
            (int) $peminjaman->user_id,
            'Denda pengembalian',
            'Rincian denda (' . implode(' + ', $parts) . '). Total: Rp ' . number_format($dendaTotal, 0, ',', '.') . '. Silakan berikan uang denda ke petugas/admin.',
            '/riwayat'
        );
    }
}
