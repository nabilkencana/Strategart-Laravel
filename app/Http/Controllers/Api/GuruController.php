<?php

namespace App\Http\Controllers\Api;

use App\Models\Guru;
use Illuminate\Http\Request;    
use App\Http\Controllers\Controller;    

class GuruController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $guru = Guru::all();
        return response()->json([
            'success' => true,
            'message' => 'Data guru berhasil diambil!'
        ],200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'required|string|max:255|unique:gurus,nik',
            'email' => 'required|string|max:255|unique:gurus,email',
            'no_hp' => 'required|string|max:255|unique:gurus,no_hp',
            'password' => 'required|string|max:255',
            'foto' => 'required|string|max:255',
        ]);

        $guru = Guru::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data guru berhasil ditambahkan!',
            'data' => $guru
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $guru = Guru::find($id);
        if (!$guru) {
            return response()->json([
                'success' => false,
                'message' => 'Data guru tidak ditemukan!',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data guru berhasil diambil!',
            'data' => $guru
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $guru = Guru::find($id);
        if (!$guru) {
            return response()->json([
                'success' => false,
                'message' => 'Data guru tidak ditemukan!',
            ], 404);
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'required|string|max:255|unique:gurus,nik,' . $guru->id,
            'email' => 'required|string|max:255|unique:gurus,email,' . $guru->id,
            'no_hp' => 'required|string|max:255|unique:gurus,no_hp,' . $guru->id,
            'password' => 'required|string|max:255',
            'foto' => 'required|string|max:255',
        ]);

        $guru->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data guru berhasil diupdate!',
            'data' => $guru
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $guru = Guru::find($id);
        if (!$guru) {
            return response()->json([
                'success' => false,
                'message' => 'Data guru tidak ditemukan!',
            ], 404);
        }

        $guru->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data guru berhasil dihapus!',
        ]);
    }
}
