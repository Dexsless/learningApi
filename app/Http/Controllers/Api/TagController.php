<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index()
    {
        $tag = Tag::all();
        $res = [
            'success' => true,
            'message' => 'Data tag',
            'data' => $tag
        ];
        return response()->json($res, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'nama_tag' => 'required|unique:tags'
        ]);
        if($validator->fails()){
            $res = [
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors' => $validator->errors()
            ];
            return response()->json($res, 422);
        }

        try {
            $tag = new Tag();
            $tag->nama_tag = $request->nama_tag;
            $tag->slug = Str::slug($request->nama_tag);
            $tag->save();
            $res = [
                'success' => true,
                'message' => 'Data tag Tersimpan',
                'data' => $tag
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
        try {
            $tag = Tag::findOrFail($id);
            $res = [
                'success' => true,
                'message' => 'Data tag Ditemukan',
                'data' => $tag
            ];
            return response()->json($res, 200);
        } catch (\Exception $e) {
            $res = [
                'success' => false,
                'message' => 'Data tag Tidak Ditemukan',
                'errors' => $e->getMessage()
            ];
            return response()->json($res, 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'nama_tag' => 'required'
        ]);
        if($validator->fails()){
            $res = [
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors' => $validator->errors()
            ];
            return response()->json($res, 422);
        }

        try {
            $tag = Tag::findOrFail($id);
            $tag->nama_tag = $request->nama_tag;
            $tag->slug = Str::slug($request->nama_tag);
            $tag->save();
            $res = [
                'success' => true,
                'message' => 'Data tag Tersimpan',
                'data' => $tag
            ];
            return response()->json($res, 200);
        } catch (\Exception $e) {
            $res = [
                'success' => false,
                'message' => 'Terjadi kesalahan',
                'errors' => $e->getMessage()
            ];
            return response()->json($res, 404);
        }
    }
    public function destroy($id)
    {
        try {
            $tag = Tag::findOrFail($id);
            $tag->delete();

            $res = [
                'success' => true,
                'message' => 'Data tag Berhasil Dihapus'
            ];
            return response()->json($res, 200);
        } catch (\Exception $e) {
            $res = [
                'success' => false,
                'message' => 'Terjadi kesalahan',
                'errors' => $e->getMessage()
            ];
            return response()->json($res, 404);
        }
    }
}
