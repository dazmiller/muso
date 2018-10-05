<?php

namespace App\Repositories;

use App\Instrument;
use Carbon\Carbon;
use DB;
use Config;
use Storage;
use Log;

class InstrumentRepository{

    /**
     * Find a Instrument by ID
     *
     * @param  Integer  $id
     * @return Instrument record
     */
    public function findInstrument($id){

            Instrument::select(DB::raw("
                        id,name"))
                ->where('instruments.id',$id)
                ->orderBy('instruments.name','DESC')
                ->first();
    }


    /**
     * Find all Instruments
     *
     * @return Collection instruments
     */
    public function GetAllInstruments(){
        $query= Instrument::select(DB::raw("
                        id,name"))
                ->orderBy('instruments.name','DESC');
                return $query->get();

    }


}
