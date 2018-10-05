<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\InstrumentRepository;
use App\Instrument;


class InstrumentsController extends Controller
{
    
    /**
     * The instrument repository instance.
     *
     * @var InstrumentRepository
     */
    //protected $instrumentRepository;

    
    /**
     * Create a new controller instance.
     *
     * @param  InstrumentRepository  $instrument
     * @return void
     */
  public function __construct(InstrumentRepository $instrument)
   {  
       $this->instrumentRepository = $instrument;

    
 }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
     
        $instruments = $this->instrumentRepository->GetAllInstruments();

        return response()->json([
            'success'   => true,
            'meta'      => [
                'total' => count($instruments)
            ],
            'instruments'    => $this->prepareList($instruments)
        ]);
    }



    private function prepareItem($record){
        if($record){
            $instrument = [
                'id'    => $record->id,
                'name'  => $record->name,
                'count' => 0
            ];

            if(property_exists($record,'total')){
                $genre['count'] = $record->total;
            }
        }else{
            $instrument = [];
        }

        return $instrument;
    }

    private function prepareList($records){
        $instruments = [];

        foreach ($records as $instrument) {
            array_push($instruments,$this->prepareItem($instrument));
        }
        return $instruments;
    }


    
}
