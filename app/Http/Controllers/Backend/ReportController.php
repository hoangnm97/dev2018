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
use App\Models\AdAds;
use App\Models\FbAdStatistic;
use App\Models\Lead;
use App\Models\LeadCodStatus;
use Carbon\Carbon;
use function GuzzleHttp\Psr7\build_query;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{

    public function lead(Request $request){

        $chanel_selected = $request->get('channel');

//        echo "<pre>"; print_r($chanel_selected); echo "</pre>"; die;

        $leadModel = new Lead();

        $channel_fillers = $leadModel->getChannel();

        $query = \DB::table('leads')
            ->select('id', 'utm_medium', 'mkt_channel', \DB::raw('count(*) as total'));
//            ->select('id', 'utm_medium', \DB::raw('SELECT `id`, `utm_medium`, COUNT(*) AS total FROM `leads` GROUP BY `utm_medium` LIMIT 20 OFFSET 0'));

        if(!empty($chanel_selected) && is_array($chanel_selected)){
            $query->whereIn('mkt_channel', $chanel_selected);
        }
        $query->groupBy('utm_medium');


        $results= $query->paginate(20);

//        echo "<pre>"; print_r($results); echo "</pre>"; die;

        return view('backend.report.lead', compact('results', 'channel_fillers'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @desc: Báo cáo tổng quan facebook
     */
    public function fbReportGeneral(Request $request){

        $time_start = !empty($request->get('time_start')) ? $request->get('time_start'): date('Y-m-d', time());
        $time_end = !empty($request->get('time_end')) ? $request->get('time_end') : date('Y-m-d', time());

        if(!empty($time_start) && !empty($time_end)){
            javascript()->put([
                'time_start' => date('Y-m-d',strtotime($time_start)),
                'time_end' => date('Y-m-d',strtotime($time_end))
            ]);
        }

        $day_start = new \DateTime($time_start);
        $day_start->modify('-1 day');

        $faTotalLogs = [];

        $log_days = [];

        $day_count = round((strtotime($time_end) - strtotime($time_start)) / (60 * 60 * 24));


        if ($day_count == 0)
            $day_count = 1;


        for ($i = 0; $i < $day_count; $i++){

            $log_day = $day_start->modify('+1' . 'days')->format('Y-m-d');

            if(strtotime($log_day) == time() ){
                break;
            }

            $faTotalLogs[$log_day]['statistic'] = FbAdStatistic::getAllStatisticByDay($log_day);

            $faTotalLogs[$log_day]['lead_count'] = Lead::getSumLeadOfDay($log_day);

            $log_days[] = $log_day;
        }



        $fbGerenalReport = FbAdStatistic::getAllStatistic($log_days);
        $fbGerenalReport->sum_lead = Lead::getSumLead($log_days);


        $codeReports = \DB::table('leads')
            ->select(DB::raw('DISTINCT mkt_code'))
            ->where('mkt_channel', 'FA')
            ->get();
        javascript()->put([
            'report_by_code_url' => route('backend.report.fb_by_code', ['mkt' => ''])
        ]);

        return view('backend.report.fb_general', compact(
            'faTotalLogs',
            'fbGerenalReport',
            'codeReports'
        ));
    }


    /**
     * @param $mkt_code
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @báo cáo tổng quan theo mã quản cáo(mã khóa học)
     */
    public function fbBycode($mkt_code = '', Request $request){

        $time_start = !empty($request->get('time_start')) ? $request->get('time_start'): date('Y-m-d', time());
        $time_end = !empty($request->get('time_end')) ? $request->get('time_end') : date('Y-m-d', time());

        $day_start = new \DateTime($time_start);
        $day_start->modify('-1 day');


        $adByCodes = AdAds::getAdByMktCode($mkt_code);

        $adIds = [];

        foreach ($adByCodes as $adByCode){
            $adIds[] = $adByCode->ad_id;
        }


        $faTotalLogs = [];

        $log_days = [];

        $day_count = round((strtotime($time_end) - strtotime($time_start)) / (60 * 60 * 24));

        if ($day_count == 0)
            $day_count = 1;

//        echo "<pre>"; print_r($day_count); echo "</pre>"; die;

        for ($i = 0; $i < $day_count; $i++){

            $log_day = $day_start->modify('+1' . 'days')->format('Y-m-d');

            if(strtotime($log_day) == time()){
                break;
            }

            $faTotalLogs[$log_day]['statistic'] = FbAdStatistic::getAllStatisticByDay($log_day, $adIds);

            $faTotalLogs[$log_day]['lead_count'] = \DB::table('leads')
                ->where('status', Lead::STATUS_LIVE)
                ->where('date_created',$log_day)
                ->where('mkt_channel', 'FA')
                ->where('mkt_code', $mkt_code)
                ->count();

            $log_days[] = $log_day;
        }

//        echo "<pre>"; print_r($log_days); echo "</pre>"; die;



        $fbGerenalReport = \DB::table('fb_ad_statistics')
            ->select(\DB::raw('SUM(spend) AS sum_spend, SUM(reach) as sum_reach, SUM(impression) as sum_imporession, SUM(clicks) as sum_clicks, SUM(inline_link_clicks) as sum_inline_link_clicks, SUM(unique_inline_link_clicks) as sum_unique_inline_link_clicks'))
            ->whereIn('ad_id', $adIds)
            ->whereBetween('day_log', [$time_start, $time_end])
            ->first();

        $fbGerenalReport->sum_lead = \DB::table('leads')
            ->where('status', Lead::STATUS_LIVE)
            ->where('mkt_channel', 'FA')
            ->where('mkt_code', $mkt_code)
            ->whereIn('date_created', $log_days)
            ->count();


        if( !empty($request->get('test')) ){

            echo "<pre>"; print_r($fbGerenalReport); echo "</pre>";
            echo "<pre>"; print_r($faTotalLogs); echo "</pre>";

            echo "<pre>"; print_r($adIds); echo "</pre>";
            echo "<pre>"; print_r($log_days); echo "</pre>";
            die;
        }


        return response()->json([
            'success' => true,
            'report_html' => \View::make('backend.report.fb_by_code', [
                'faTotalLogs' => $faTotalLogs,
                'fbGerenalReport' => $fbGerenalReport,
                'mkt_code' => $mkt_code
            ])->render()
        ]);

    }


    public function facebookAdOptimal(Request $request){

        $time_start = !empty($request->get('time_start')) ? $request->get('time_start'): date('Y-m-d', time());
        $time_end = !empty($request->get('time_end')) ? $request->get('time_end') : date('Y-m-d', time());
        $ad_name = $request->get('ad_name', null);
        $mkt_ad_code = $request->get('mkt_ad_code', null);
        $mkt_ad_person = $request->get('mkt_ad_person', null);
        $sort = !empty($request->get('sort', null)) ? $request->get('sort') : 'spend_descending';


        if(!empty($time_start) && !empty($time_end)){
            javascript()->put([
                'time_start' => date('Y-m-d',strtotime($time_start)),
                'time_end' => date('Y-m-d',strtotime($time_end))
            ]);
        }


//        $result = DB::table('ad_ads')
//            ->join('')



        $query = DB::table('ad_ads');
//            ->where('status', 'ACTIVE');
//            ->where('effective_status', 'ACTIVE');

        if(!empty($ad_name)){
            $query->where('ad_name','LIKE',"%{$ad_name}%");
        }

        if(!empty($mkt_ad_code)){
            $query->where('mkt_ad_code', $mkt_ad_code);
        }

        if(!empty($mkt_ad_person)){
            $query->where('mkt_ad_person', $mkt_ad_person);
        }


        $fbAds = $query->get();
        $reports = [];

        $fa_ad_ids = [];

        $report_sum_lead = 0;

        foreach ($fbAds as $fbAd){

            $fb_ad_statistics = DB::table('fb_ad_statistics')
                ->select(DB::raw('SUM(spend) AS sum_spend, SUM(reach) as sum_reach, SUM(impression) as sum_impression, SUM(clicks) as sum_clicks, SUM(inline_link_clicks) as sum_inline_link_clicks, SUM(unique_inline_link_clicks) as sum_unique_inline_link_clicks'))
                ->where('ad_id', $fbAd->ad_id)
                ->whereBetween('day_log', [$time_start, $time_end])
                ->first();

            $params = [
                'time_start' => $time_start,
                'time_end' => $time_end
            ];

            $lead_count = \App\Models\Lead::countLeadByMedium($fbAd->ad_name, $params);

            $fb_ad_statistics->c1_price = ($fb_ad_statistics->sum_impression > 0 && $fb_ad_statistics->sum_spend > 0) ? $fb_ad_statistics->sum_spend / $fb_ad_statistics->sum_impression : $fb_ad_statistics->sum_spend;
            $fb_ad_statistics->c2_price = ($fb_ad_statistics->sum_inline_link_clicks > 0 && $fb_ad_statistics->sum_spend > 0) ? $fb_ad_statistics->sum_spend / $fb_ad_statistics->sum_inline_link_clicks : $fb_ad_statistics->sum_spend;
            $fb_ad_statistics->c2_per_c1 = ($fb_ad_statistics->sum_inline_link_clicks > 0 && $fb_ad_statistics->sum_impression > 0) ? round( ($fb_ad_statistics->sum_inline_link_clicks / $fb_ad_statistics->sum_impression) * 100, 2) : 0;
            $fb_ad_statistics->c3_price = ($lead_count > 0 && $fb_ad_statistics->sum_spend > 0) ? $fb_ad_statistics->sum_spend / $lead_count : $fb_ad_statistics->sum_spend;
            $fb_ad_statistics->c3_per_c2 = ($lead_count > 0 && $fb_ad_statistics->sum_inline_link_clicks > 0) ? round( ($lead_count / $fb_ad_statistics->sum_inline_link_clicks) * 100, 2) : 0;



            if($fb_ad_statistics->sum_spend > 0 || $lead_count > 0){

                $fa_ad_ids[] = $fbAd->ad_id;

                $reports[] = [
                    'ad_info' => $fbAd,
                    'lead_count' => $lead_count,
                    'statistics' => (array) $fb_ad_statistics
                ];

                $report_sum_lead+= $lead_count;
            }

        }

        $report_sum_statistic = DB::table('fb_ad_statistics')
            ->select(DB::raw('SUM(spend) AS sum_spend, SUM(reach) as sum_reach, SUM(impression) as sum_impression, SUM(clicks) as sum_clicks, SUM(inline_link_clicks) as sum_inline_link_clicks, SUM(unique_inline_link_clicks) as sum_unique_inline_link_clicks'))
            ->whereIn('ad_id', $fa_ad_ids)
            ->whereBetween('day_log', [$time_start, $time_end])
            ->first();

        $sum_report = [
            'report_sum_lead' => $report_sum_lead,
            'report_sum_statistic' => $report_sum_statistic
        ];


        return view('backend.report.fb_optimal', [
            'sum_report' => $sum_report,
            'reports' => $reports,
            'ad_name' => $ad_name,
            'mkt_ad_code' => $mkt_ad_code,
            'mkt_ad_person' => $mkt_ad_person,
            'sort' => $sort
        ]);
    }

}