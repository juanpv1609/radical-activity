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
    public const API_PANET='https://soporte.gruporadical.com/proactivanet/api/'; //PRODUCCION
    //public const API_PANET='http://10.1.11.99/proactivanet/api/'; //LOCAL
    public const PANET_KEY='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJwYW5ldC5hZG1pbiIsIm92ciI6ImZhbHNlIiwiYXV0IjoiMCIsIm5iZiI6MTY1NDYxMjIxMiwiZXhwIjoxNjg2MTQ4MjEyLCJpYXQiOjE2NTQ2MTIyMTIsImlzcyI6InByb2FjdGl2YW5ldCIsImF1ZCI6ImFwaSJ9.cCb5Dn4eyFiY9dEKC7Pb9OOA92uyR71pDMrCG0Rdx5k';


    public function verifyTicket(Request $request){

        //$host = env("API_QRADAR");
        $base = self::API_PANET.'Incidents';
        $customer_id = $request->input('customer_id');
        $code = $request->input('code');
        $comment = $request->input('comment');
        $type_id = $request->input('type');


        $ticket = null;
            # code...
        $response = Http::withHeaders([
                                    'Authorization' => self::PANET_KEY,
                                    ])->withOptions(['verify' => false])->accept('application/json')
                                    ->get($base.'?Code='.$code.'&PadTypes_id='.$type_id);
                //$response['comment'] = $item['comment'];
                //$ticket = $response['PawSvcAuthUsers_idCreator']['Id'];
        if ($response->successful()) { //busqueda de ID del ticket
            $Id=array_column($response->json(), 'Id');
            $PawSvcAuthUsers_idCreator=array_column($response->json(), 'PawSvcAuthUsers_idCreator');
            $Status=array_column($response->json(), 'Status');

            $ticket = [
                'Id' => $Id[0],
                'code' => $code,
                'PawSvcAuthUsers_idCreator' => $PawSvcAuthUsers_idCreator[0],
                'comment' => $comment,
                'status' => $Status[0]
            ];
            return ($ticket);
        }else{
            return $response->status();

        }
                //dd($response);



    }
    public function verifyTicket2(Request $request){

        //$host = env("API_QRADAR");
        $base = self::API_PANET.'Incidents';
        $customer_id = $request->input('customer_id');
        $code = $request->input('code');
        $comment = $request->input('comment');
        $type_id = $request->input('type');


        $ticket = null;
            # code...
        $response = Http::withHeaders([
                                    'Authorization' => self::PANET_KEY,
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

        $base = self::API_PANET.'Incidents';
        $customer_id = $request->input('customer_id');
        $code = $request->input('code');
        $comment = $request->input('comment');
        $type_id = $request->input('type');
        $ticket = null;
            # code...
        $response = Http::withHeaders([
                                    'Authorization' => self::PANET_KEY,
                                    ])->withOptions(['verify' => false])->accept('application/json')
                                    ->get($base.'?Code='.$code.'&PadTypes_id='.$type_id);
                //$response['comment'] = $item['comment'];
                //$ticket = $response['PawSvcAuthUsers_idCreator']['Id'];
        $Id                         = array_column($response->json(), 'Id');
        $PawSvcAuthUsers_idCreator  = "666b0093-467e-419c-aa9c-f07080c7cf0b"; //ID de usuario Nivel1
        $Status                     = array_column($response->json(), 'Status');
         $response2 = Http::withHeaders([
                'Authorization' => self::PANET_KEY,
                    ])->withOptions(['verify' => false])->accept('application/json')
                    ->put($base.'/'.$Id[0].'/close', [
                        "PawSvcAuthUsers_id" => $PawSvcAuthUsers_idCreator,
                        "Resolution" => true,
                        "ClosingComments" => $comment
                    ]);
            if ($response2->successful()) { //cierre del ticket
                $ticket = [
                    'Id' => $Id[0],
                    'code' => $code,
                    'PawSvcAuthUsers_idCreator' => $PawSvcAuthUsers_idCreator,
                    'comment' => $comment,
                    'status' => $response2->successful(),
                    'status_name' =>  "Closed"
                    ];

            } else {
                $ticket = [
                    'Id' => $Id[0],
                    'code' => $code,
                    'PawSvcAuthUsers_idCreator' => $PawSvcAuthUsers_idCreator,
                    'comment' => $comment,
                    'status' => $response->successful(),
                    'status_name' => $Status[0]
                    ];

            }

        return ($ticket);


        /* if (count($arrayResult)>0) { //guarda en la bdd
            # code...
            $panet = new Panet([
                'usuario' => $request->input('usuario'),
                'accion' => 'cierre de tickets',
                'cliente' => $request->input('customer_name'),
                'count_cierre' => count($arrayResult),
            ]);
            $panet->save();
        } */


       // return ($arrayResult);


    }
    public function closeTicket2(Request $request){

        //$host = env("API_QRADAR");
        $base = self::API_PANET.'Incidents';
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
                                    'Authorization' => self::PANET_KEY,
                                    ])->withOptions(['verify' => false])->accept('application/json')
                                    ->get($base.'?Code='.$item['code'].'&PadCustomers_id='.$customer_id.'&Status=New,Assigned&Archived=false&PadTypes_id='.$type_id);
                //$response['comment'] = $item['comment'];
                //$ticket = $response['PawSvcAuthUsers_idCreator']['Id'];
            if ($response->successful()) { //busqueda correcta de ID del ticket
                $Id                         = array_column($response->json(), 'Id');
                $PawSvcAuthUsers_idCreator  = array_column($response->json(), 'PawSvcAuthUsers_idCreator');
                $Status                     = array_column($response->json(), 'Status');

                $response2 = Http::withHeaders([
                    'Authorization' => self::PANET_KEY,
                        ])->withOptions(['verify' => false])->accept('application/json')
                        ->put($base.'/'.$Id[0].'/close',[
                            "PawSvcAuthUsers_id" => $PawSvcAuthUsers_idCreator[0],
                            "Resolution" => true,
                            "ClosingComments" => $item['comment']
                        ]);
                if ($response2->successful()) { //cierre del ticket
                        $ticket = [
                        'Id' => $Id[0],
                        'code' => $item['code'],
                        'PawSvcAuthUsers_idCreator' => $PawSvcAuthUsers_idCreator[0],
                        'comment' => $item['comment'],
                        'status' => $response2->successful()
                        ];
                        array_push($arrayResult, $ticket);
                    }else{
                        return $response2->status();

                    }
                } else { // si no encuentra el ticket
                    break;
                }




            }
        if (count($arrayResult)>0) { //guarda en la bdd
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
        $url = self::API_PANET.'Customers?$sort=Name&$fields=Name';


            # code...
            $response = Http::withHeaders([
                'Authorization' => self::PANET_KEY,
                    ])->withOptions(['verify' => false])->accept('application/json')
                    ->get($url);



        return $response->body();

    }
    public function getTypes(){
        $url = self::API_PANET.'Types?$sort=Name&$fields=Name';


            # code...
            $response = Http::withHeaders([
                'Authorization' => self::PANET_KEY,
                    ])->withOptions(['verify' => false])->accept('application/json')
                    ->get($url);



        return $response->body();

    }

}
