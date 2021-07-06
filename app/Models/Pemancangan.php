<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemancangan extends Model{
    
    protected $table = 'rekap_data';

    
    public function proses_izin(){
        return $this->belongsTo(Prosesizin::class, 'no_registrasi','no_registrasi');
    }
    
}

?>