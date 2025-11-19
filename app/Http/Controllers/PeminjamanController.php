<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Product;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    /**
     * History peminjaman user login
     */
    public function myPeminjaman(Request $request)
    {
        $user = $request->user();

        $peminjaman = Peminjaman::with('product', 'location')
            ->where('user_id', $user->id)
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json([
            'message' => 'History peminjaman Anda',
            'data' => $peminjaman
        ]);
    }

    /**
     * Semua peminjaman aktif (status = dipinjam) untuk admin/global
     */
    public function activePeminjaman()
    {
        $peminjaman = Peminjaman::with('product', 'user', 'location')
            ->where('status', 'dipinjam')
            ->orderBy('start_date', 'asc')
            ->get();

        return response()->json([
            'message' => 'Daftar peminjaman aktif',
            'data' => $peminjaman
        ]);
    }

    /**
     * Buat peminjaman baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id'   => 'required|exists:products,id',
            'location_id'  => 'required|exists:location,id',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after_or_equal:start_date',
            'pin_code'     => 'required|string',
            'note'         => 'nullable|string',
            'qty'          => 'required|integer|min:1',
            'override_pin' => 'nullable|string', // opsional, untuk pinjam dari user lain
        ]);

        $product = Product::findOrFail($request->product_id);

        // Hitung total yang sedang dipinjam dari aset
        $totalDipinjam = Peminjaman::where('product_id', $product->id)
            ->where('status', 'dipinjam')
            ->sum('qty');

        $stokTersedia = $product->qty - $totalDipinjam;

        // Kalau stok masih cukup → pinjam dari aset
        if ($request->qty <= $stokTersedia) {
            $product->qty -= $request->qty; // kurangi stok
            $product->save();

            $peminjaman = Peminjaman::create([
                'user_id'     => $request->user()->id,
                'product_id'  => $product->id,
                'location_id' => $request->location_id,
                'start_date'  => $request->start_date,
                'end_date'    => $request->end_date,
                'pin_code'    => $request->pin_code,
                'note'        => $request->note,
                'qty'         => $request->qty,
                'status'      => 'dipinjam',
            ]);

            return response()->json([
                'message' => 'Peminjaman berhasil dari aset',
                'data' => $peminjaman
            ]);
        }

        // Kalau stok habis → cek override PIN
        if ($request->has('override_pin')) {
            $peminjamanAktif = Peminjaman::where('product_id', $product->id)
                ->where('status', 'dipinjam')
                ->where('pin_code', $request->override_pin)
                ->first();

            if (!$peminjamanAktif) {
                return response()->json([
                    'message' => "Stok habis dan PIN override salah. Stok tersisa: $stokTersedia"
                ], 400);
            }

            // Otomatis kembalikan peminjam sebelumnya
            $peminjamanAktif->status = 'dikembalikan';
            $peminjamanAktif->save();

            // Kembalikan stok peminjam sebelumnya ke aset
            $product->qty += $peminjamanAktif->qty;
            $product->save();

            // Kurangi stok sesuai qty user baru
            $product->qty -= $request->qty;
            $product->save();

            $peminjaman = Peminjaman::create([
                'user_id'     => $request->user()->id,
                'product_id'  => $product->id,
                'location_id' => $request->location_id,
                'start_date'  => $request->start_date,
                'end_date'    => $request->end_date,
                'pin_code'    => $request->pin_code,
                'note'        => $request->note,
                'qty'         => $request->qty,
                'status'      => 'dipinjam',
            ]);

            return response()->json([
                'message' => 'Peminjaman berhasil menggunakan override PIN, peminjam sebelumnya otomatis dikembalikan',
                'data' => $peminjaman
            ]);
        }

        // Kalau stok habis dan tidak ada override PIN
        return response()->json([
            'message' => "Stok tidak cukup. Stok tersisa: $stokTersedia"
        ], 400);
    }

    /**
     * Kembalikan produk
     */
    public function return(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        if (
            $request->user()->id !== $peminjaman->user_id &&
            $request->pin_code !== $peminjaman->pin_code
        ) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $peminjaman->status = 'dikembalikan';
        $peminjaman->save();

        // Tambahkan stok kembali ke produk
        $product = Product::find($peminjaman->product_id);
        $product->qty += $peminjaman->qty;
        $product->save();

        return response()->json([
            'message' => 'Produk berhasil dikembalikan',
            'data' => $peminjaman
        ]);
    }

    /**
     * Cek PIN untuk override stok 0
     */
    public function checkPin(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'pin_code'   => 'required|string',
        ]);

        $peminjamanAktif = Peminjaman::where('product_id', $request->product_id)
            ->where('status', 'dipinjam')
            ->where('pin_code', $request->pin_code)
            ->first();

        return $peminjamanAktif
            ? response()->json(['message' => 'PIN valid', 'can_override' => true])
            : response()->json(['message' => 'PIN salah', 'can_override' => false], 400);
    }
}
