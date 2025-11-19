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
     * Semua peminjaman aktif (status = dipinjam)
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
            'product_id'  => 'required|exists:products,id',
            'location_id' => 'required|exists:location,id',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'pin_code'    => 'required|string',
            'note'        => 'nullable|string',
            'qty'         => 'required|integer|min:1',

            // override
            'id_pinjam'    => 'nullable|exists:peminjaman,id',
            'override_pin' => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);

        /**
         * Hitung stok tersedia dari aset
         */
        $totalDipinjam = Peminjaman::where('product_id', $product->id)
            ->where('status', 'dipinjam')
            ->sum('qty');

        $stokTersedia = $product->qty - $totalDipinjam;

        /**
         * ======================================================
         * 1. PINJAM DARI ASET (stok cukup & tanpa id_pinjam)
         * ======================================================
         */
        if (!$request->id_pinjam && $request->qty <= $stokTersedia) {

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

            // KURANGI stok aset
            $product->qty -= $request->qty;
            $product->save();

            return response()->json([
                'message' => 'Peminjaman berhasil dari aset',
                'data'    => $peminjaman
            ]);
        }

        /**
         * ======================================================
         * 2. PINJAM DARI USER LAIN (override)
         * requires: id_pinjam + override_pin
         * ======================================================
         */
        if ($request->id_pinjam && $request->override_pin) {

            $old = Peminjaman::where('id', $request->id_pinjam)
                ->where('product_id', $product->id)
                ->where('status', 'dipinjam')
                ->first();

            if (!$old) {
                return response()->json([
                    'message' => 'ID peminjaman tidak valid atau barang sudah dikembalikan.'
                ], 400);
            }

            // cek PIN
            if ($old->pin_code !== $request->override_pin) {
                return response()->json([
                    'message' => 'PIN override salah.'
                ], 400);
            }

            // kembalikan pinjaman lama
            $old->status = 'dikembalikan';
            $old->save();

            // buat pinjaman baru (stok TIDAK berubah!)
            $new = Peminjaman::create([
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
                'message' => 'Peminjaman berhasil dari user lain (override PIN)',
                'data'    => $new
            ]);
        }

        /**
         * ======================================================
         * 3. Stok habis & tidak ada override
         * ======================================================
         */
        return response()->json([
            'message' => "Stok habis. Gunakan id_pinjam + override_pin untuk meminjam dari user lain."
        ], 400);
    }

    /**
     * Kembalikan produk
     */
    public function return(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        // hanya peminjam asli atau yang tahu PIN yg boleh return
        if (
            $request->user()->id !== $peminjaman->user_id &&
            $request->pin_code !== $peminjaman->pin_code
        ) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        // ubah status
        $peminjaman->status = 'dikembalikan';
        $peminjaman->save();

        // TAMBAH stok aset kembali
        $product = Product::find($peminjaman->product_id);
        $product->qty += $peminjaman->qty;
        $product->save();

        return response()->json([
            'message' => 'Produk berhasil dikembalikan',
            'data' => $peminjaman
        ]);
    }
}