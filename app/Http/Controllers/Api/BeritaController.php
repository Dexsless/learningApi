<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
            'judul' => 'required|unique:beritas',
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
            $berita->judul = $request->judul;
            $berita->deskripsi = $request->deskripsi;
            $berita->slug = Str::slug($request->judul);
            if ($request->hasFile('foto')) {
                $image = $request->file('foto');
                $filename = Hash::make($image) . '.' . $image->getClientOriginalExtension();
                $location = public_path('images/berita');
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
    public function show($id)
{
    $berita = Berita::with('kategori', 'tag', 'user')->find($id);

    if ($berita) {
        $res = [
            'success' => true,
            'message' => 'Detail berita',
            'data' => $berita
        ];
        $req = 200;
    } else {
        $res = [
            'success' => false,
            'message' => 'Berita not found',
            'data' => null
        ];
        $req = 404;
    }

    return response()->json($res, $req);
}
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|unique:beritas,judul,' . $id,
            'deskripsi' => 'required',
            'foto' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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
            $berita = Berita::find($id);
            if (!$berita) {
                $res = [
                    'success' => false,
                    'message' => 'Berita not found',
                    'data' => null
                ];
                return response()->json($res, 404);
            }

            $berita->judul = $request->judul;
            $berita->deskripsi = $request->deskripsi;
            $berita->slug = Str::slug($request->judul);

            if ($request->hasFile('foto')) {
                // Delete the old image if exists
                if ($berita->foto && file_exists(public_path('images/berita/' . $berita->foto))) {
                    unlink(public_path('images/berita/' . $berita->foto));
                }

                $image = $request->file('foto');
                $filename = Hash::make($image) . '.' . $image->getClientOriginalExtension();
                $location = public_path('images/berita/');
                $image->move($location, $filename);
                $berita->foto = $filename;
            }

            $berita->id_user = $request->id_user;
            $berita->id_kategori = $request->id_kategori;
            $berita->save();

            // Update tags
            $berita->tag()->sync($request->tag);

            $res = [
                'success' => true,
                'message' => 'Data berita Terupdate',
                'data' => $berita
            ];
            return response()->json($res, 200);
        } catch (\Exception $e) {
            $res = [
                'success' => false,
                'message' => 'Terjadi kesalahan',
                'errors' => $e->getMessage()
            ];
            return response()->json($res, 500);
        }
    }
    public function destroy($id)
    {
        try {
            $berita = Berita::find($id);
            if (!$berita) {
                return response()->json([
                    'success' => false,
                    'message' => 'Berita not found',
                    'data' => null
                ], 404);
            }

            // Delete the image file if it exists
            if ($berita->foto && file_exists(public_path('images/berita/' . $berita->foto))) {
                unlink(public_path('images/berita/' . $berita->foto));
            }

            // Detach related tags
            $berita->tag()->detach();

            // Delete the Berita record
            $berita->delete();

            $res = [
                'success' => true,
                'message' => 'Data berita Terhapus',
                'data' => null
            ];
            return response()->json($res, 200);
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
