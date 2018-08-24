<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2/5/2018
 * Time: 9:29 AM
 */
namespace App\Http\Controllers\Backend;


use App\Core\FaceBookSdk;
use App\Core\VtigerCRM\VtigerClient;
use App\Events\Backend\UpdateLeadEvent;
use App\Http\Controllers\Controller;
use App\Models\AdAccount;
use App\Models\AdAccountStatistic;
use App\Models\AdAds;
use App\Models\FbAdStatistic;
use App\Models\Lead;
use App\Models\LeadInteraction;
use App\Models\TaskSchedule;
use App\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;

class AppController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @desc: Dashboard Admin
     */
    public function index(){
        return view('backend.app.index');
    }


    public function test(Request $request){


        $interaction_data = [
            'id' => 17691,
            's0' =>1,
        ];

        $interaction = new LeadInteraction();

        $xx = $interaction->updateOrCreate([
            'id' => 17691
        ], [
            's0' => 2
        ]);

        echo "<pre>"; print_r($xx); echo "</pre>"; die;




        $medium = 'EMPTY';

        $parser = parser_utm_medium($medium);

        echo "<pre>"; print_r($parser); echo "</pre>"; die;


        $abc = round((66/141)*100, 2);

        echo "<pre>"; print_r($abc); echo "</pre>";die;


        $client = new Client();
        $res = $client->request('GET', 'https://hocexcel.online/api/course/listing');
        $course_upsale_availables = [];

        if($res->getStatusCode() === 200){
            $result = $res->getBody();
            $course_upsale_availables = json_decode($result, true);
        }

        echo "<pre>"; print_r($course_upsale_availables); echo "</pre>"; die;



       $sdk = new FaceBookSdk();
       $sdk->getAccessToken();


        $params = [
            'time_start' => '2018-05-23',
            'time_end' => '2018-05-23',
//            'filtering' => [
//                [
//                    'field' => 'ad.status',
//                    'operator' => 'IN',
//                    'value' => [
//                        'ACTIVE'
//                    ]
//                ]
//            ]
        ];

        $ads = $sdk->getAdInsights($params);

        $data = $ads->getBody();
        $data = json_decode($data);
        echo "<pre>"; print_r($data); echo "</pre>"; die;

    }

    public function update(Request $request)
    {

        $leads = Lead::where('update_status', 'pending')
            ->select(['id', 'date_created', 'hour_created', 'update_status'])
            ->take(500)
            ->get();

        if(count($leads) == 0){
            echo "<pre>"; print_r('----------> DOne!'); echo "</pre>"; die;
        }

        /** @var  $lead Lead*/
        foreach ($leads as $lead){
            $lead->lead_cod_status()->update([
                'lead_date_created' => date('Y-m-d', strtotime($lead->date_created))
            ]);
            $lead->update_status = 'done';
            $lead->update();
        }

        echo "<pre>"; print_r('updated: ' . count($leads)); echo "</pre>";

        echo '<script>setTimeout(function(){ window.location.href="'. route('app.update') .'"; }, 2000); </script>';



    }




    public function ad_account(){

        $sdk = new FaceBookSdk();
        $account = $sdk->getAccount();

//        $adAccount = new AdAccount();
//        $adAccount->account_id = $account['id'];
//        $adAccount->acc = $account['age'];
//        $adAccount->agency_client_declaration = $account['agency_client_declaration'];
//
//
//        echo "<pre>"; print_r($account->getData()); echo "</pre>"; die;
//

        $adAccount = $account->getData();

        return view('backend.app.ad_account', [
            'adAccount' => $adAccount
        ]);

    }

    public function ad_campaign(){
        $sdk = new FaceBookSdk();
        $adCampaigns = $sdk->getCampaigns();

//        echo "<pre>"; print_r($adCampaigns); echo "</pre>"; die;

        return view('backend.app.ad_campaign', [
            'adCampaigns' => $adCampaigns
        ]);
    }

    public function ad_sets(){

        $sdk = new  FaceBookSdk();
        $adSets = $sdk->getAdsets();

//        echo "<pre>"; print_r($adSets); echo "</pre>"; die;

        return view('backend.app.ad_sets', [
            'adSets' => $adSets
        ]);
    }

    public function ad_ads(){
        $sdk = new  FaceBookSdk();
        $ads = $sdk->getAds();

        echo "<pre>"; print_r($ads); echo "</pre>"; die;

        return view('backend.app.ad_ads', [
            'ads' => $ads
        ]);
    }

    public function listen(Request $request){

        \Log::info('listening from vtiger crm.................');
        if($request->isMethod('POST')){

            $leadModel = new Lead();
            $leadModel->insertLead($request);
        }

        return response()->json([
            'success' => true,
            'message' => 'Thành công'
        ]);

    }


}