<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2/5/2018
 * Time: 9:29 AM
 */
namespace App\Http\Controllers\Backend;


use App\Core\FaceBookSdk;
use App\Http\Controllers\Controller;
use App\Models\AdAccount;
use App\Models\GaAccountStatistic;
use Illuminate\Http\Request;

class AdAccountController extends Controller
{


    public function index(Request $request){

        $query = new AdAccount();

        $ad_accounts = $query->paginate(25);

        return view('backend.ad_account.index', [
            'ad_accounts' => $ad_accounts
        ]);
    }

    /**
     * @param $ad_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @desc: Cập nhật chi tiêu cho kênh quảng cáo
     */
    public function updateSpend($ad_id){
        $ad_account = AdAccount::whereId($ad_id)->first();

        if(!$ad_account){
            echo "<pre>"; print_r('Tài khoản không tồn tại'); echo "</pre>";die;
        }

        if($ad_account->channel == 'FA'){
            echo "<pre>"; print_r('Tài khoản kênh Facebook được cập nhật tự động. '); echo "</pre>";die;
        }

        $ad_account_statistics = new GaAccountStatistic();
        $ad_account_statistics = $ad_account_statistics->where('ad_id', $ad_account->id);
        $ad_account_statistics = $ad_account_statistics->orderBy('day_log', 'desc');

        $ad_account_statistics = $ad_account_statistics->paginate(20);

//        echo "<pre>"; print_r($ad_account_statistics); echo "</pre>";die;


        return view('backend.ad_account.update_spend', compact('ad_account', 'ad_account_statistics'));
    }

    public function storeSpend($ad_id, Request $request){
        $ad_account = AdAccount::whereId($ad_id)->first();
        if(!$ad_account){
            return redirect()->back()->withFlashDanger('Tài khoản không tồn tại');
        }

        $valodator = \Validator::make($request->all(), [
            'day_log' => 'required',
            'spend' => 'required',
        ]);

        if($valodator->fails()){
            return redirect()->back()->withErrors($valodator)->withInput();
        }

        $dateOj = new \DateTime($request->get('day_log'));
        $day_log = $dateOj->format('Y-m-d');

        $accountStatistic = new GaAccountStatistic();

        $accountStatistic->log_statistic($ad_account, $day_log, $request->all());


        return redirect()->back()->withFlashSuccess('Cập nhật chi tiêu thành công');

    }



    public function detail($ad_id, $ad_name){

        $query = \DB::table('leads')
            ->select('date_created', \DB::raw('count(*) as total'))
            ->where('utm_medium', $ad_name);
        $query->orderBy('date_created', 'DESC');

        $query->groupBy('date_created');

        $results= $query->paginate(20);

        return view('backend.ads.detail', compact('ad_id', 'ad_name', 'results'));

    }







}