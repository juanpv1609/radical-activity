<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Panet;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;

class PanetController extends Controller
{

    public function verifyTicket(Request $request){

        //$host = env("API_QRADAR");
        $base = env("API_PANET").'Incidents';
        $customer_id = $request->input('customer_id');
        $code = $request->input('code');
        $comment = $request->input('comment');
        $type_id = $request->input('type');


        $ticket = null;
            # code...
        $response = Http::withHeaders([
                                    'Authorization' => env("PANET_KEY"),
                                    ])->withOptions(['verify' => false])->accept('application/json')
                                    ->get($base.'?Code='.$code.'&PadCustomers_id='.$customer_id.'&Archived=false&PadTypes_id='.$type_id)->json();
                //$response['comment'] = $item['comment'];
                //$ticket = $response['PawSvcAuthUsers_idCreator']['Id'];
        $Id=array_column($response, 'Id');
        $PawSvcAuthUsers_idCreator=array_column($response, 'PawSvcAuthUsers_idCreator');
        $Status=array_column($response, 'Status');




        $ticket = [
            'Id' => $Id[0],
            'code' => $code,
            'PawSvcAuthUsers_idCreator' => $PawSvcAuthUsers_idCreator[0],
            'comment' => $comment,
            'status' => $Status[0]
        ];
                //dd($response);






        return ($ticket);


    }
    public function closeTicket(Request $request){

        //$host = env("API_QRADAR");
        $base = env("API_PANET").'Incidents';
        $arrayRequest = $request->input('tickets');
        $customer_id = $request->input('customer_id');
        $type_id = $request->input('type_id');

        $arrayResult =[];
        $temp = [];
        $ticket = null;
        $arrayError=[];
        $cerrados=0;
         foreach ($arrayRequest as $item) {
            $response = Http::withHeaders([
                                    'Authorization' => env("PANET_KEY"),
                                    ])->withOptions(['verify' => false])->accept('application/json')
                                    ->get($base.'?Code='.$item['code'].'&PadCustomers_id='.$customer_id.'&Status=New,Assigned&Archived=false&PadTypes_id='.$type_id);
                //$response['comment'] = $item['comment'];
                //$ticket = $response['PawSvcAuthUsers_idCreator']['Id'];
            if ($response->successful()) {
                $Id                         = array_column($response->json(), 'Id');
                $PawSvcAuthUsers_idCreator  = array_column($response->json(), 'PawSvcAuthUsers_idCreator');
                $Status                     = array_column($response->json(), 'Status');

                $response2 = Http::withHeaders([
                    'Authorization' => env("PANET_KEY"),
                        ])->withOptions(['verify' => false])->accept('application/json')
                        ->put($base.'/'.$Id[0].'/close',[
                            "PawSvcAuthUsers_id" => $PawSvcAuthUsers_idCreator[0],
                            "Resolution" => true,
                            "ClosingComments" => $item['comment']
                        ]);
                if ($response2->successful()) {
                        $ticket = [
                        'Id' => $Id[0],
                        'code' => $item['code'],
                        'PawSvcAuthUsers_idCreator' => $PawSvcAuthUsers_idCreator[0],
                        'comment' => $item['comment'],
                        'status' => $response2->successful()
                        ];
                        array_push($arrayResult, $ticket);
                    }
                } else {
                    break;
                }




            }
        if (count($arrayResult)>0) {
            # code...
            $panet = new Panet([
                'usuario' => $request->input('usuario'),
                'accion' => 'cierre de tickets',
                'cliente' => $request->input('customer_name'),
                'count_cierre' => count($arrayResult),
            ]);
            $panet->save();
        }


        return ($arrayResult);


    }
    public function getCustomers(){
        $url = env("API_PANET").'Customers?$sort=Name&$fields=Name';


            # code...
            $response = Http::withHeaders([
                'Authorization' => env("PANET_KEY"),
                    ])->withOptions(['verify' => false])->accept('application/json')
                    ->get($url);



        return $response->body();

    }
    public function getTypes(){
        $url = env("API_PANET").'Types?$sort=Name&$fields=Name';


            # code...
            $response = Http::withHeaders([
                'Authorization' => env("PANET_KEY"),
                    ])->withOptions(['verify' => false])->accept('application/json')
                    ->get($url);



        return $response->body();

    }

}
