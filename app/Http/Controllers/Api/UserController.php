<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $user = User::all();
        $res = [
            'success' => true,
            'message' => 'Data user',
            'data' => $user
        ];
        return response()->json($res, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|min:8'
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
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = $request->password;
            $user->save();
            $res = [
                'success' => true,
                'message' => 'Data user Tersimpan',
                'data' => $user
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
            $user = User::findOrFail($id);
            $res = [
                'success' => true,
                'message' => 'Data user Ditemukan',
                'data' => $user
            ];
            return response()->json($res, 200);
        } catch (\Exception $e) {
            $res = [
                'success' => false,
                'message' => 'Data user Tidak Ditemukan',
                'errors' => $e->getMessage()
            ];
            return response()->json($res, 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|min:8'
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
            $user = User::findOrFail($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = $request->password;
            $user->save();
            $res = [
                'success' => true,
                'message' => 'Data user Terubah',
                'data' => $user
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
            $user = User::findOrFail($id);
            $user->delete();

            $res = [
                'success' => true,
                'message' => 'Data user Berhasil Dihapus'
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
