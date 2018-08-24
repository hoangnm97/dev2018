<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2/5/2018
 * Time: 9:29 AM
 */
namespace App\Http\Controllers\Backend;


use App\Core\FaceBookSdk;
use App\Core\Report\MarketingReport;
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
use Maatwebsite\Excel\Excel;

class MarketingReportController extends Controller
{


    private function _getBreakMarketer(){
        $marketers = \DB::table('ad_ads')
            ->select(DB::raw('DISTINCT mkt_ad_person'))
            ->get();

        $marketer_list = [
            '' => '--chọn marketer--'
        ];

        foreach ($marketers as $marketer){
            if(empty($marketer->mkt_ad_person)){
                continue;
            } else {
                $marketer_list[$marketer->mkt_ad_person] = $marketer->mkt_ad_person;
            }
        }
        return $marketer_list;


    }

    private function _getBreakCode(){
        $codes = \DB::table('ad_ads')
            ->select(DB::raw('DISTINCT mkt_ad_code'))
            ->get();

        $code_list = [
            '' => '--chọn khóa học--'
        ];
        foreach ($codes as $code){
            if(empty($code->mkt_ad_code)){
                $code_list['EMPTY'] = 'EMPTY';
            } else {
                $code_list[$code->mkt_ad_code] = $code->mkt_ad_code;
            }
        }

        return $code_list;
    }

    private function _getBreakChannel(){
        $channels = \DB::table('leads')
            ->select(DB::raw('DISTINCT mkt_channel'))
            ->get();

        $channel_list = [];
        foreach ($channels as $channel){
            $channel_list[$channel->mkt_channel] = $channel->mkt_channel;
        }

        return $channel_list;
    }

    private function _getBreakTypes($type){
        if($type == 'marketer')
            return $this->_getBreakMarketer();
        if($type == 'course')
            return $this->_getBreakCode();
        if($type == 'channel')
            return $this->_getBreakChannel();
        return [];
    }



    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @desc: Báo cáo tổng quan facebook
     */
    public function general(Request $request){


        $time_end = !empty($request->get('time_end')) ? $request->get('time_end') : date('Y-m-d', time());
        $request->request->set('time_end', $time_end);

        $time_start = $request->get('time_start');

        if(empty($time_start)){
            $today = new Carbon($time_end);
            $time_start = $today->modify('-7 days')->toDateString();
            $request->request->set('time_start', $time_start);
        }

        $type_selected = $request->get('type', 'course');


        $break_types = $this->_getBreakTypes($type_selected);

        $break = $request->get('break');

        $is_detail = false;

        $dataReports = [];
        if(!empty($break)){
            $is_detail = true;

            if($type_selected == 'course'){

                $dataReports[$break]['sumary'] = $this->_getSumaryByCode($request, $break);

                // Lấy tất cả các kênh mà có lead về từ với code này
                $channels = \DB::table('leads')
                    ->select(DB::raw('DISTINCT mkt_channel'))
                    ->where('mkt_code', $break)
                    ->get();

                $breakDataReports = [];
                foreach ($channels as $channel){
                    $breakDataReports[$channel->mkt_channel] = $this->_getSumaryCodeByChannel($request, $break, $channel->mkt_channel);
                }

//                echo "<pre>"; print_r($type_selected); echo "</pre>"; die;
//                echo "<pre>"; print_r($breakDataReports); echo "</pre>"; die;

                $dataReports[$break]['channels'] = $breakDataReports;
            }
            else if($type_selected == 'marketer'){

                $dataReports[$break]['sumary'] = $this->_getSumaryByMarketer($request, $break);

                // Lấy tất cả các Mã KH mà marketer này đang chạy
                $codes = \DB::table('ad_ads')
                    ->select(DB::raw('DISTINCT mkt_ad_code'))
                    ->where('mkt_ad_person', $break)
                    ->get();

                $breakDataReports = [];
                foreach ($codes as $code){
                    $breakDataReports[$code->mkt_ad_code] = $this->_break_by_code_marketer($request, $code->mkt_ad_code, $break);
                }
                $dataReports[$break]['code'] = $breakDataReports;
            }

        } else {

            if($type_selected == 'course'){
                foreach ($break_types as $key => $break_type){
                    if(empty($key))
                        continue;

                    $dataReports[$break_type] = $this->_getSumaryByCode($request, $break_type);
                }
            }
            elseif($type_selected == 'marketer'){

                foreach ($break_types as $key => $break_type){
                    if(empty($key))
                        continue;
                    $dataReports[$break_type] = $this->_getSumaryByMarketer($request, $break_type);
                }
            }
            elseif ($type_selected == 'channel'){
//                echo "<pre>"; print_r(111); echo "</pre>"; die;
            }
            else {
//                echo "<pre>"; print_r(1111); echo "</pre>"; die;
            }


        }

//        echo "<pre>"; print_r($dataReports); echo "</pre>"; die;

        javascript()->put([
            'time_start' => date('Y-m-d',strtotime($time_start)),
            'time_end' => date('Y-m-d',strtotime($time_end))
        ]);

        $day_start = new \DateTime($time_start);
        $day_start->modify('-1 day');

        $sumaryReports = [];

        $sumary_params = [
            'date_start' => $time_start,
            'date_end' => $time_end,
        ];


        /** Lấy báo cáo tổng */
        $sumaryReports = MarketingReport::sumaryReport($sumary_params);

        $log_days = [];

        $day_count = round((strtotime($time_end) - strtotime($time_start)) / (60 * 60 * 24));

        if ($day_count == 0)
            $day_count = 1;
        for ($i = 0; $i < $day_count; $i++){

            $log_day = $day_start->modify('+1' . 'days')->format('Y-m-d');

            $log_days[] = $log_day;
        }

        $fbGerenalReport = FbAdStatistic::getAllStatistic($log_days);
        $fbGerenalReport->sum_lead = Lead::getSumLead($log_days);


        $codeReports = \DB::table('leads')
            ->select(DB::raw('DISTINCT mkt_code'))
            ->where('mkt_channel', 'FA')
            ->get();


//        javascript()->put([
//            'report_break_url' => route('backend.marketing.break_by_channel')
//        ]);


        $reportTypes = [
            'course' => 'Khóa học',
//            'channel' => 'Kênh',
            'marketer' => 'Marketer',
        ];

        $filter = [];
        foreach ($reportTypes as $key => $value){
            $filter[$key] = [
                'key' => $key,
                'value' => $value,
                'childs' => $this->_getBreakTypes($key)
            ];
        }

        javascript()->put([
            'filter_json' => json_encode($filter)
        ]);

//        echo "<pre>"; print_r($sumaryReports); echo "</pre>"; die;


        return view('backend.marketing_report.general', [
            'type_selected' => $type_selected,
            'time_start' => $time_start,
            'time_end' => $time_end,
            'is_detail' => $is_detail,
            'break' => $break,
            'dataReports' => $dataReports,
            'sumaryReports' => $sumaryReports,
            'fbGerenalReport' => $fbGerenalReport,
            'codeReports' => $codeReports,
            'reportTypes' => $reportTypes,
            'break_types' => $break_types,
        ]);
    }


    /**
     * @param Request $request
     * @param $code
     * @return array
     * @desc: Lấy báo cáo theo chiều khóa học (code)
     */
    private function _getSumaryByCode(Request $request, $code){

        $time_start = !empty($request->get('time_start')) ? $request->get('time_start'): date('Y-m-d', time());
        $time_end = !empty($request->get('time_end')) ? $request->get('time_end') : date('Y-m-d', time());

        $day_start = new Carbon($time_start);
        $day_start->modify('-1 days');


        $statistic = \DB::table('fb_ad_statistics')
            ->select(\DB::raw('SUM(spend) AS sum_spend, SUM(reach) as sum_reach, SUM(impression) as sum_imporession, SUM(clicks) as sum_clicks, SUM(inline_link_clicks) as sum_inline_link_clicks, SUM(unique_inline_link_clicks) as sum_unique_inline_link_clicks'))
            ->join('ad_ads', 'fb_ad_statistics.ad_ads_id', '=', 'ad_ads.id')
            ->where('ad_ads.mkt_ad_code', $code)
            ->whereBetween('fb_ad_statistics.day_log',[$time_start, $time_end])
            ->first();

        $faTotalLogs = [
            'total' => [
                'statistic' => [

                    'sum_spend' => $statistic->sum_spend,
                    'sum_reach' => $statistic->sum_reach,
                    'sum_imporession' => $statistic->sum_imporession,
                    'sum_clicks' => $statistic->sum_clicks,
                    'sum_inline_link_clicks' => $statistic->sum_inline_link_clicks,
                    'sum_unique_inline_link_clicks' => $statistic->sum_unique_inline_link_clicks,
                ],
                'lead_count' => \DB::table('leads')
                    ->where('mkt_code', $code)
                    ->where('status', Lead::STATUS_LIVE)
                    ->whereBetween('date_created',[$time_start, $time_end])
                    ->count(),
                'l9_count' => \DB::table('leads')
                    ->where('mkt_code', $code)
                    ->where('status', Lead::STATUS_LIVE)
                    ->where('in_cancel', 0)
                    ->where('sale_success', 1)
                    ->whereBetween('date_created',[$time_start, $time_end])
                    ->count(),
                'revenue' => \DB::table('leads')
                    ->where('mkt_code', $code)
                    ->where('status', Lead::STATUS_LIVE)
                    ->where('in_cancel', 0)
                    ->where('sale_success', 1)
                    ->whereBetween('date_created',[$time_start, $time_end])
                    ->sum('lead_origin_price')
            ]
        ];
        $log_days = [];

        $day_count = round((strtotime($time_end) - strtotime($time_start)) / (60 * 60 * 24));

        if ($day_count == 0)
            $day_count = 1;

        for ($i = 0; $i < $day_count; $i++){

            $log_day = $day_start->modify('+1' . 'days')->format('Y-m-d');

            if(strtotime($log_day) == time() ){
                break;
            }

            $params = [
                'code' => $code,
                'day_log' => [$log_day, $log_day]
            ];

            $faTotalLogs['days'][$log_day]['statistic'] = FbAdStatistic::getStatisticByCode($params);

            $faTotalLogs['days'][$log_day]['lead_count'] = \DB::table('leads')
                ->where('mkt_code', $code)
                ->where('status', Lead::STATUS_LIVE)
                ->where('date_created',$log_day)
                ->count();

            $log_days[] = $log_day;
        }

        return $faTotalLogs;
    }


    /**
     * @param Request $request
     * @param $marketer
     * @desc: Lấy báo cáo theo chiều marketer
     */
    private function _getSumaryByMarketer(Request $request, $marketer){
        $time_start = !empty($request->get('time_start')) ? $request->get('time_start'): date('Y-m-d', time());
        $time_end = !empty($request->get('time_end')) ? $request->get('time_end') : date('Y-m-d', time());

        $day_start = new Carbon($time_start);
        $day_start->modify('-1 days');

        $sumaryReport = [];

        $day_count = round((strtotime($time_end) - strtotime($time_start)) / (60 * 60 * 24));

        if ($day_count == 0)
            $day_count = 1;

        for ($i = 0; $i < $day_count; $i++){
            $log_day = $day_start->modify('+1' . 'days')->format('Y-m-d');

            if(strtotime($log_day) == time() ){
                break;
            }

            $sumaryReport[$log_day]['statistic'] = FbAdStatistic::getStatisticByMarketer($marketer, [$log_day, $log_day]);

            $sumaryReport[$log_day]['lead_count'] = \DB::table('leads')
                ->where('mkt_person', $marketer)
                ->where('status', Lead::STATUS_LIVE)
                ->where('date_created',$log_day)
                ->count();
        }

        return $sumaryReport;
    }


    /**
     * @param Request $request
     * @param $code
     * @param $channel
     * @return array
     * @desc: Break mã khóa học theo kênh
     */
    private function _getSumaryCodeByChannel(Request $request, $code, $channel){

        $time_start = !empty($request->get('time_start')) ? $request->get('time_start'): date('Y-m-d', time());
        $time_end = !empty($request->get('time_end')) ? $request->get('time_end') : date('Y-m-d', time());

        $day_start = new Carbon($time_start);
        $day_start->modify('-1 days');

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

            $params = [
                'code' => $code,
                'channel' => $channel,
                'day_log' => [$log_day, $log_day]
            ];

            $faTotalLogs[$log_day]['statistic'] = FbAdStatistic::getStatisticByCode($params);

            $faTotalLogs[$log_day]['lead_count'] = \DB::table('leads')
                ->where('mkt_channel', $channel)
                ->where('mkt_code', $code)
                ->where('status', Lead::STATUS_LIVE)
                ->where('date_created',$log_day)
                ->count();

            $log_days[] = $log_day;
        }

        return $faTotalLogs;
    }

    /**
     * @param Request $request
     * @param string $code
     * @return \Illuminate\Http\JsonResponse
     * @desc: Chia nhỏ chi tiết theo kênh
     */
    public function break_by_channel(Request $request, $code = ''){

        $time_start = !empty($request->get('time_start')) ? $request->get('time_start'): date('Y-m-d', time());
        $time_end = !empty($request->get('time_end')) ? $request->get('time_end') : date('Y-m-d', time());

        $day_start = new Carbon($time_start);
        $day_start->modify('-1 days');

        // Lấy tất cả các kênh mà có lead về từ với code này
        $channels = \DB::table('leads')
            ->select(DB::raw('DISTINCT mkt_channel'))
            ->where('mkt_code', $code)
            ->get();

        $breakDataReports = [];
        foreach ($channels as $channel){
            $breakDataReports[$channel->mkt_channel] = $this->_getSumaryCodeByChannel($request, $code, $channel->mkt_channel);
        }

        $html = view('backend.marketing_report._break_by_channel', [
            'breakDataReports' => $breakDataReports
        ])->render();
        return \Response::json([
            'success' => true,
            'html' => $html
        ]);
    }


    private function _break_by_code_marketer(Request $request, $code, $marketer){
        $time_start = !empty($request->get('time_start')) ? $request->get('time_start'): date('Y-m-d', time());
        $time_end = !empty($request->get('time_end')) ? $request->get('time_end') : date('Y-m-d', time());

        $day_start = new Carbon($time_start);
        $day_start->modify('-1 days');

        $day_count = round((strtotime($time_end) - strtotime($time_start)) / (60 * 60 * 24));
        if ($day_count == 0)
            $day_count = 1;

        $faTotalLogs = [];
        for ($i = 0; $i < $day_count; $i++){

            $log_day = $day_start->modify('+1' . 'days')->format('Y-m-d');

            if(strtotime($log_day) == time() ){
                break;
            }

            $params = [
                'code' => $code,
                'marketer' => $marketer
            ];

            $faTotalLogs[$log_day]['statistic'] = FbAdStatistic::getStatistics($params, [$log_day, $log_day]);

            $faTotalLogs[$log_day]['lead_count'] = \DB::table('leads')
                ->where('mkt_person', $marketer)
                ->where('mkt_code', $code)
                ->where('status', Lead::STATUS_LIVE)
                ->where('date_created',$log_day)
                ->count();
        }

        return $faTotalLogs;
    }

    /**
     * @param Request $request
     * @param $marketer
     * @return \Illuminate\Http\JsonResponse
     * @desc: Báo cáo cho chi tiết cho marketer theo khóa học
     */
    public function break_by_marketer(Request $request, $marketer){
        $time_start = !empty($request->get('time_start')) ? $request->get('time_start'): date('Y-m-d', time());
        $time_end = !empty($request->get('time_end')) ? $request->get('time_end') : date('Y-m-d', time());

        $day_start = new Carbon($time_start);
        $day_start->modify('-1 days');


        // Lấy tất cả các Mã KH mà marketer này đang chạy
        $codes = \DB::table('ad_ads')
            ->select(DB::raw('DISTINCT mkt_ad_code'))
            ->where('mkt_ad_person', $marketer)
            ->get();

        $breakDataReports = [];
        foreach ($codes as $code){
            $breakDataReports[$code->mkt_ad_code] = $this->_break_by_code_marketer($request, $code->mkt_ad_code, $marketer);
        }


        $html = view('backend.marketing_report._break_by_marketer', [
            'marketer' => $marketer,
            'breakDataReports' => $breakDataReports,
            'time_start' => $time_start,
            'time_end' => $time_end
        ])->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    private function _break_detail_marketer_by_code_channel(Request $request, $marketer, $code, $channel){
        $time_start = !empty($request->get('time_start')) ? $request->get('time_start'): date('Y-m-d', time());
        $time_end = !empty($request->get('time_end')) ? $request->get('time_end') : date('Y-m-d', time());

        $day_start = new Carbon($time_start);
        $day_start->modify('-1 days');

        $day_count = round((strtotime($time_end) - strtotime($time_start)) / (60 * 60 * 24));
        if ($day_count == 0)
            $day_count = 1;

        $faTotalLogs = [];
        for ($i = 0; $i < $day_count; $i++){

            $log_day = $day_start->modify('+1' . 'days')->format('Y-m-d');

            if(strtotime($log_day) == time() ){
                break;
            }

            $params = [
                'code' => $code,
                'marketer' => $marketer
            ];

            $faTotalLogs[$log_day]['statistic'] = FbAdStatistic::getStatistics($params, [$log_day, $log_day]);

            $faTotalLogs[$log_day]['lead_count'] = \DB::table('leads')
                ->where('mkt_person', $marketer)
                ->where('mkt_code', $code)
                ->where('status', Lead::STATUS_LIVE)
                ->where('date_created',$log_day)
                ->count();
        }

        return $faTotalLogs;
    }

    /**
     * @param Request $request
     * @param $marketer
     * @return \Illuminate\Http\JsonResponse
     * @desc: Break chi tiết của marketer theo mã khóa học với kênh
     */
    public function break_by_marketer_code(Request $request, $marketer, $code){


        $channels = DB::table('leads')
            ->select(DB::raw('DISTINCT mkt_channel'))
            ->where('mkt_person', $marketer)
            ->where('mkt_code', $code)
            ->get();


        $breakDataReports = [];
        foreach ($channels as $channel){
            $breakDataReports[$channel->mkt_channel] = $this->_break_detail_marketer_by_code_channel($request, $marketer, $code, $channel->mkt_channel);
        }


        $html = view('backend.marketing_report._break_by_marketer_code_channel', [
            'marketer'          => $marketer,
            'code'              => $code,
            'breakDataReports'  => $breakDataReports
        ])->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @desc: Render import báo cáo các qua Excel
     */

    public function import_report_external(Request $request){

        if($request->ajax()){

//            echo "<pre>"; print_r($request->all()); echo "</pre>"; die;

            if($request->hasFile('file')){
                $path = $request->file('file')->getRealPath();
                $data = \Excel::load($path, function ($reader){})->get();

                foreach ($data as $row){
                    $ad_name = isset($row->ad_name) ? $row->ad_name : '';
                    $nameArr = explode('_', $ad_name);

                    $ad_data = [
                        'ad_name'           => $ad_name,
                        'mkt_ad_code'       => (isset($nameArr[1]) && !empty($nameArr[1])) ? $nameArr[1] : '',
                        'mkt_ad_person'     => (isset($nameArr[2]) && !empty($nameArr[2])) ? $nameArr[2] : '',
                        'mkt_ad_type'       => (isset($nameArr[3]) && !empty($nameArr[3])) ? $nameArr[3] : '',
                        'mkt_landing'       => (isset($nameArr[4]) && !empty($nameArr[4])) ? $nameArr[4] : '',
                        'mkt_ad_group'      => (isset($nameArr[5]) && !empty($nameArr[5])) ? $nameArr[5] : '',
                        'mkt_ad_id'         => (isset($nameArr[6]) && !empty($nameArr[6])) ? $nameArr[6] : '',
                    ];

                    $ad_account = AdAccount::whereChannel($nameArr[0])->first();
                    if($ad_account){
                        $ad_data['ad_account_id']       = $ad_account->id;
                        $ad_data['ad_account_channel']  = $ad_account->channel;
                    } else {
                        echo "<pre>"; print_r('Không tìm thấy tài khoản quản cáo tương ứng trên hệ thống'); echo "</pre>"; die;
                    }

                    /** @var  $ad AdAds */
                    $ad = AdAds::insertNewAd($ad_data);

                    if($ad){
                        $day_log = new Carbon($row->date);
                        $ad->getFbAdStatistics()->updateOrCreate([
                            'ad_id'     => $ad->ad_id,
                            'day_log'   => $day_log->toDateString(),
                        ], [
                            'spend'                 => $row->spend,
                            'impression'            => $row->c1,
                            'inline_link_clicks'    => $row->c2,
                            'json_data'             => json_encode($row)
                        ]);
                    }

                }

            }

            return response()->json([
                'success' => true,
            ]);
        }

        javascript()->put([
            'import_data_link' => route('backend.marketing.import_external')
        ]);

        return view('backend.marketing_report.import_report_external');
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


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @desc: pivot table l9 (dhtmlx)
     */
    public function l9_pivot(Request $request){

        $date_start = $request->get('time_start');
        $date_end = $request->get('time_end');
        $date_filter = $request->get('date_filter', 'created_at');
        if(!empty($date_start)){
            $temp = \DateTime::createFromFormat('d/m/Y', $date_start);
            $date_start = $temp->format('Y-m-d');
        } else {
            $date_start = date('Y-m-d', time());
        }

        if(!empty($date_end)){
            $temp = \DateTime::createFromFormat('d/m/Y', $date_end);
            $date_end = $temp->format('Y-m-d');
        } else {
            $date_end = date('Y-m-d', time());
        }

        if(!empty($date_start) && !empty($date_end)) {
            javascript()->put([
                'time_start' => date('Y-m-d', strtotime($date_start)),
                'time_end' => date('Y-m-d', strtotime($date_end))
            ]);
        }

        $time_start = $date_start . ' 00:00:00';
        $time_end = $date_end . ' 23:59:59';

        $marketers = \DB::table('leads')
            ->select(DB::raw('DISTINCT mkt_person'))
            ->whereBetween($date_filter, [$time_start, $time_end])
            ->get();


        $dataReports = [];


        foreach ($marketers as $marketer){
            $params = [
                'mkt_person' => !empty($marketer->mkt_person) ? $marketer->mkt_person : null,
                $date_filter => [
                    'time_start' => $time_start,
                    'time_end' => $time_end,
                ]
            ];

            $mktCodes = Lead::getMktCodes($params);

            foreach ($mktCodes as $mktCode){
                $params['mkt_code'] = $mktCode;


                $break_l1_count = Lead::l1Count($params);
                $break_l4_count = Lead::l4Count($params);
                $break_l7_count = Lead::l7Count($params);
                $break_l9_count = Lead::l9Count($params);
                $break_l94_count = Lead::l94Count($params);
                $break_l97_count = Lead::l97Count($params);

                $break_l94_per_l4 = ($break_l4_count > 0) ? round(($break_l94_count/$break_l4_count)*100, 2) : 0;
                $break_l97_per_l7 = ($break_l7_count > 0) ? round(($break_l97_count/$break_l7_count)*100, 2) : 0;
                $break_l9_per_l1 = ($break_l1_count > 0) ? round(($break_l9_count/$break_l1_count)*100, 2) : 0;
                $dataReports[] = [
                    'column' => 'HEO',
                    'mkt_person' => !empty($marketer->mkt_person) ? $marketer->mkt_person : 'Unknow',
                    'mkt_code' => $mktCode,
                    'l1_count' => $break_l1_count,
                    'l4_count' => $break_l4_count,
                    'l7_count' => $break_l7_count,
                    'l9_count' => $break_l9_count,
                    'l94_count' => $break_l94_count,
                    'l97_count' => $break_l97_count,
                    'break_l94_per_l4' => $break_l94_per_l4,
                    'break_l97_per_l7' => $break_l97_per_l7,
                    'break_l9_per_l1' => $break_l9_per_l1,
                    'l4_revenue' => Lead::l4_revenue($params),
                    'l7_revenue' => Lead::l7_revenue($params),
                    'l9_revenue' => Lead::l9_revenue($params)
                ];

            }

        }

//        echo "<pre>"; print_r($dataReports); echo "</pre>"; die;

        javascript()->put([
            'report_pivot_dataset' => json_encode($dataReports),
            'time_start' => date('Y-m-d', strtotime($date_start)),
            'time_end' => date('Y-m-d', strtotime($date_end))
        ]);

        $date_filters = [
            'created_at' => 'Created Time',
            'sale_success_updated' => 'L9 Time'
        ];


        return view('backend.marketing_report.l9_pivot', [
            'date_filters' => $date_filters
        ]);
    }

}