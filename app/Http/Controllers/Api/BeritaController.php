<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BeritaController extends Controller
{
    public function index()
    {
        $berita = Berita::with('kategori', 'tag', 'user')->get();
        $res = [
            'success' => true,
            'message' => 'Data berita',
            'data' => $berita
        ];
        return response()->json($res, 200);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_berita' => 'required|unique:beritas',
            'deskripsi' => 'required',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'id_user' => 'required',
            'id_kategori' => 'required',
            'tag' => 'required|array',
        ]);

        if ($validator->fails()) {
            $res = [
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors' => $validator->errors()
            ];
            return response()->json($res, 422);
        }

        try {
            $berita = new Berita();
            $berita->nama_berita = $request->nama_berita;
            $berita->deskripsi = $request->deskripsi;
            $berita->slug = Str::slug($request->nama_berita);
            if ($request->hasFile('foto')) {
                $image = $request->file('foto');
                $filename = random_int(100000, 999999) . '.' . $image->getClientOriginalExtension();
                $location = public_path('images/berita' . $filename);
                $image->move($location, $filename);
                $berita->foto = $filename;
            }
            $berita->id_user = $request->id_user;
            $berita->id_kategori = $request->id_kategori;
            $berita->save();
            // melampirkan banyak tag
            $berita->tag()->attach($request->tag);
            // mengembalikan data
            $res = [
                'success' => true,
                'message' => 'Data berita Tersimpan',
                'data' => $berita
            ];
            return response()->json($res, 201);
        } catch (\Exception $e) {
            $res = [
                'success' => false,
                'message' => 'Terjadi kesalahan',
                'errors' => $e->getMessage()
            ];
            return response()->json($res, 500);
        }
    }
}
