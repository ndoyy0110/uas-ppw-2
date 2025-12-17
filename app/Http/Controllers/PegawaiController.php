<?php

namespace App\Http\Controllers;

use App\Helpers\RecaptchaHelper;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\Pekerjaan;
use Illuminate\Support\Facades\Validator;

class PegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $keyword = $request->get('keyword');
        $data = Pegawai::when($keyword, function ($query) use ($keyword) {
            $query->where('nama', 'like', "%{$keyword}%")
                ->orWhere('email', 'like', "%{$keyword}%");
        })->paginate(10);

        return view('pegawai.index', compact('data'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function add() {
        $pekerjaan = Pekerjaan::orderBy('nama')->get();
        return view('pegawai.add', compact('pekerjaan'));
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $recaptchaResponse = RecaptchaHelper::verify($request->input('g-recaptcha-response'));

         $validator = Validator::make($request->all(), [
            'pekerjaan_id' => 'required|exists:pekerjaan,id',
            'nama' => 'required|string',
            'email' => 'required|email|unique:pegawai',
            'gender' => 'required|in:male,female',
        ]);

        if ($validator->fails()) return redirect()->back()->with($validator->errors()->all());

        $data = new Pegawai();
        $data->pekerjaan_id = $request->pekerjaan_id;
        $data->nama = $request->nama;
        $data->email = $request->email;
        $data->gender = $request->gender;
        $data->is_active = $request->has('is_active');

        if ($data->save()) {
            return redirect()->route('pegawai.index')->with('success', 'Data berhasil ditambahkan');
        } else {
            return redirect()->route('pegawai.index')->with('error', 'Data tidak tersimpan');
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $data = Pegawai::findOrFail($request->id);
        $pekerjaan = Pekerjaan::orderBy('nama')->get();
        return view('pegawai.edit', compact('data', 'pekerjaan'));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request) {

        $data = Pegawai::findOrFail($request->id);

        $validator = Validator::make($request->all(), [
            'pekerjaan_id' => 'required|exists:pekerjaan,id',
            'nama' => 'required|string',
            'email' => 'required|email|unique:pegawai,email,' . $data->id,
            'gender' => 'required|in:male,female',
        ]);

        if ($validator->fails()) return redirect()->back()->with($validator->errors()->all());

        $data->pekerjaan_id = $request->pekerjaan_id;
        $data->nama = $request->nama;
        $data->email = $request->email;
        $data->gender = $request->gender;
        $data->is_active = $request->has('is_active');

        if ($data->save()) {
            return redirect()->route('pegawai.index')->with('success', 'Data tersimpan');
        } else {
            return redirect()->route('pegawai.index')->with('error', 'Data tidak tersimpan');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
         Pegawai::findOrFail($request->id)->delete();
        return redirect()->route('pegawai.index')->with('success', 'Data terhapus');
    }
}
