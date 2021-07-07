<?php

namespace App\Http\Controllers;

use App\Models\Geolocation;
use App\Models\Pemancangan;
use Illuminate\Http\Request;
use App\Helpers;
use App\Helpers\App;

class PemancanganController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function all_data(){
        try {
            $data = Pemancangan::leftJoin('proses_izin as a','a.no_registrasi','=', 'rekap_data.no_registrasi')
                ->where('a.kode_proses', 2)
                ->where('jenis_permohonan','like','%IMB%')
                ->orWhere('jenis_permohonan','')
                ->orWhere('jenis_permohonan',null)
                ->get();
        return response()->json([
            'success' => true,
            'data' => $data
        ],200);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->errorInfo[2],
                'success' => false
            ]);
        } 
    }
    public function getdetail(Request $request){
        $no_registrasi = $request->no_registrasi;
        try {
            $data = Pemancangan::with('proses_izin')->where('no_registrasi', $no_registrasi)->first();
            if($data){
                return response()->json([
                    'success' => true,
                    'data' => $data
                ],200);
            }else{
                return response()->json([
                    'success' => true,
                    'data' => 'Data Tidak Ditemukan'
                ],200);

            }
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->errorInfo[2],
                'success' => false
            ],500);
        }
    }

    public function simpan_koordinat(Request $request){
        $this->validate($request,[ 
            'no_registrasi' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'image' => 'mimes:jpeg,bmp,png'
        ]);

        if ($request->hasFile('image')) {
            $original_filename = $request->file('image')->getClientOriginalName();
            $original_filename_arr = explode('.', $original_filename);
            $file_ext = end($original_filename_arr);
            $destination_path = App::public_path('uploads/images');
            $image = 'U-' . time() . '.' . $file_ext;

            if ($request->file('image')->move($destination_path, $image)) {
                $model = new Geolocation();
                $model->no_registrasi = $request->no_registrasi;
                $model->lat = $request->lat;
                $model->lng = $request->lng;
                $model->images = '/uploads/images' . $image;
                $model->status = 1;
                if($model->save()){
                    return $this->responseRequestSuccess($destination_path);
                }else{
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal Disimpan',

                    ],401);
                }
            } else {
                return $this->responseRequestError('Cannot upload file');
            }
        } else {
            return $this->responseRequestError('File not found');
        }
        
    }
    protected function responseRequestSuccess($ret = null)
    {
        return response()->json(['success' => true, 'data' => $ret], 200)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }

    protected function responseRequestError($message = 'Bad request', $statusCode = 200)
    {
        return response()->json(['status' => 'error', 'error' => $message], $statusCode)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
    
}
