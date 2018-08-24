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
use App\Models\LeadInteraction;
use App\Models\LeadSaleStatus;
use App\Models\LeadUpsale;
use Carbon\Carbon;
use GuzzleHttp\Client;
use function GuzzleHttp\Psr7\build_query;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{

    /**
     * @return null
     * Lấy thông tin customer care.
     */
    protected function __getCustomerCareInfo(){

        $customer_cares = LeadSaleStatus::getCustomerCare();
        $user_id = auth()->user()->id;

        $customer_care_name = null;

        foreach ($customer_cares as $customer_care){
            if($user_id == $customer_care['id']){
                $customer_care_name = $customer_care['customer_care'];
                break;
            }
        }

        return $customer_care_name;
    }


    protected function __getCallPasserUser(){

        $cp_user_map = config('crm_user.panda_user_to_cp_user');

        $curent_user = auth()->user();

        if(isset($cp_user_map[$curent_user->id])){
            return $cp_user_map[$curent_user->id];
        }
        return null;
    }

    /**
     * @param $lead
     * @return array
     */
    private function __get_data_copy(Lead $lead){

        $lead->refresh();

        $vtiger_lead = json_decode($lead->lead_json);

        $course_ids = $lead->course_ids;
        $course_upsales = $lead->lead_upsale()->get(['course_id'])->toArray();
        foreach ($course_upsales as $course_upsale){
            $course_ids = $course_ids . ',' . $course_upsale['course_id'];
        }

        $utm_term = trim($lead->utm_tern);
//        echo "<pre>"; print_r(str_replace('"', '', $utm_term)); echo "</pre>"; die;

        $combo_title = !empty($lead->combo_title) ? $lead->combo_title : $vtiger_lead->cf_769;


        $regex = '["]';
        $data_copy = [
            'name' => html_entity_decode($lead->name),
            'mobile' => $lead->phone,
            'email' => $lead->email,
            'codAddress' => html_entity_decode($lead->cod_address),
            'courseName' => $combo_title,
            'priceToPay' => $lead->getComboPrice(),
            'courseIds' => $course_ids,
            'created_time' => $lead->time_created,
            'utm_source' => preg_replace($regex, '', $lead->utm_source),
            'utm_campaign' => preg_replace($regex, '',  $lead->utm_campaign),
            'utm_content' => preg_replace($regex, '',  $lead->utm_content),
            'utm_medium' => preg_replace($regex, '',  $lead->utm_medium),
            'utm_term' => "",
            'createdTime' => $lead->time_created,
            'lead_no' => $lead->crm_lead_number,
            'lead_from' => $vtiger_lead->cf_813,
            'cust_care' => $lead->customer_care,
            'mkt_channel' => $lead->mkt_channel,
            'mkt_code' => $lead->mkt_code,
            'mkt_person' => $lead->mkt_person,
            'mkt_type' => $lead->mkt_type,
            'mkt_landing_page' => $lead->mkt_landing_page,
            'mkt_ads_group' => $lead->mkt_ad_id,
            'mkt_ads_id' => $lead->mkt_ad_group,
            'crm_url' => 'http://crm.hocexcel.online/vtigercrm/index.php?module=Leads&view=Detail&record='. $lead->crm_lead_id .'&mode=showDetailViewByMode&requestMode=full',
            'lead_id' => $lead->crm_lead_id
        ];

        return $data_copy;
    }


    private function __query_building_new(Request $request){

        $customer_care = $request->get('customer_care', 'all');
        $date_start = $request->get('time_start');
        $date_end = $request->get('time_end');
        $report_type = $request->get('report_type', null);

        $order_by = $request->get('table_order_by', 'a.id');
        $order_by = !empty($order_by) ? $order_by : 'a.id';

        $sort = $request->get('table_sort', 'DESC');
        $sort = !empty($sort) ? $sort : 'DESC';

        $id = $request->get('id', null);
        $name = $request->get('name', null);
        $phone = $request->get('phone', null);
        $email = $request->get('email', null);
        $table_customer_care = $request->get('table_customer_care', null);

        $created = $request->get('table_created', null);

        $created_filter = null;
        if($created){
            $created = explode('-', $created);

            $temp_start =  \DateTime::createFromFormat('d/m/Y', trim($created[0]));
            $temp_end = \DateTime::createFromFormat('d/m/Y', trim($created[1]));

            $created_filter = [
                'start' => $temp_start->format('Y-m-d') . ' 00:00:00',
                'end' => $temp_end->format('Y-m-d') . ' 23:59:59'
            ];

        }

//        echo "<pre>"; print_r($request->all()); echo "</pre>"; die;


        // TODO: disable time filter global
        if(empty($date_start)){
            $date_start = date('Y-m-d', time());
        }

        if(empty($date_end)){
            $date_end = date('Y-m-d', time());
        }

        $time_start = $date_start . ' 00:00:00';
        $time_end = $date_end . ' 23:59:59';

        $select = [
            'a.id', 'a.name', 'a.phone', 'a.email', 'a.time_created',
            'a.customer_care', 'a.first_customer_care', 'a.second_customer_care', 'a.access_try', 'a.access_try_end',
            'b.call_start', 'b.interaction_no', 'b.payment_method', 'b.customer_care',
            'b.s0', 'b.s0_timestamp', 'b.s1', 'b.s1_timestamp', 'b.s2', 'b.s2_timestamp', 'b.s3', 'b.s3_timestamp', 'b.s4', 'b.s4_timestamp',
            'b.s5', 'b.s5_timestamp', 'b.s6', 'b.s6_timestamp', 'b.s7', 'b.s7_timestamp', 'b.s8', 'b.s8_timestamp',
            'b.s9', 'b.s9_timestamp', 'b.s10', 'b.s10_timestamp', 'b.s11', 'b.s11_timestamp', 'b.s12', 'b.s12_timestamp',
            'b.s13', 'b.s13_timestamp', 'b.s13_flag', 'b.s14', 'b.s14_timestamp', 'b.s15', 'b.s15_timestamp', 'b.s16', 'b.s16_timestamp', 'b.s17', 'b.s17_timestamp',
            'b.s18', 'b.s18_timestamp',
            'c.call_start AS c_call_start',
            'd.interaction_count'
        ];

        $tmp_table = DB::raw("(SELECT lead_id, call_start, MAX(interaction_no) AS max_interaction_no FROM lead_interactions GROUP BY lead_id) as c");

        $join_table_count = DB::raw("(SELECT lead_id, IFNULL(COUNT(*), 0) AS interaction_count FROM lead_interactions WHERE type='out-going-call' GROUP BY lead_id) as d");

        $query = DB::table('leads as a')
            ->select($select)
            ->join('lead_interactions as b', 'a.id', '=', 'b.lead_id')
            ->join($tmp_table, function($join){
                $join->on('b.lead_id', '=', 'c.lead_id');
                $join->on('b.interaction_no', '=', 'c.max_interaction_no');
            })
            ->leftJoin($join_table_count, function($join){
                $join->on('a.id', '=', 'd.lead_id');
            })
            ->where('a.status', Lead::STATUS_LIVE)
            ->where('a.is_duplicated', 0)
            ->whereNull('a.access_try');
//            ->whereNull('b.s13_flag');


        if($report_type != 's16'){
            $query = $query->whereNull('b.s16');
        }

        if(!is_null($report_type)){
            if($report_type == 's9_success'){
                $query = $query->where('b.payment_method', LeadInteraction::PAYMENT_METHOD_CK)
                    ->where('b.s12', 1);
            }
            elseif ($report_type == 's10_success'){
                $query = $query->where('b.payment_method', LeadInteraction::PAYMENT_METHOD_COD)
                    ->where('b.s12', 1);
            }
            elseif ($report_type == 'push_ck' || $report_type == 's9'){
                $query = $query->where('b.payment_method', LeadInteraction::PAYMENT_METHOD_CK)
                    ->whereNull('b.s5')
                    ->whereNull('b.s6')
                    ->whereNull('b.s11')
                    ->whereNull('b.s12')
                    ->whereNull('b.s13')
                    ->whereNull('b.s14')
                    ->whereNull('b.s15')
                    ->whereNull('b.s16');
            }
            elseif ($report_type == 'need_actions'){
                $query = $query->whereNull('b.s5')
                    ->whereNull('b.s6')
                    ->whereNull('b.s9')
                    ->whereNull('b.s10')
                    ->whereNull('b.s11')
                    ->whereNull('b.s12')
                    ->whereNull('b.s13')
                    ->whereNull('b.s14')
                    ->whereNull('b.s15')
                    ->whereNotNull('a.customer_care')
                    ->whereNull('a.second_customer_care');
            }
            else{
                $query = $query->where('b.'. $report_type, 1)
                    ->whereNull('b.payment_method');
            }
        }


        // start filter table params
        if($created_filter){
            $query = $query->whereBetween('a.time_created', [$created_filter['start'], $created_filter['end']]);
        }

        if($id){
            $query = $query->where('a.id', $id);
        }

        if($name){
            $query = $query->where('a.name','LIKE',"%{$name}%");
        }
        if($phone){
            $query = $query->where('a.phone','LIKE',"%{$phone}%");
        }
        if($email){
            $query = $query->where('a.email','LIKE',"%{$email}%");
        }

        if($customer_care != 'all' && !empty($customer_care)){

            if($table_customer_care){
                $query = $query->where(function ($q) use($customer_care, $table_customer_care){
                    $q->where('a.customer_care', $table_customer_care);
                    $q->where('a.first_customer_care', $customer_care);
                    $q->orWhere('a.second_customer_care', $customer_care);
                });
            } else {

                $query = $query->where(function ($q) use($customer_care){
                    $q->where('a.customer_care', $customer_care);
                    $q->orWhere('a.first_customer_care', $customer_care);
                    $q->orWhere('a.second_customer_care', $customer_care);
                });
            }

        } elseif ($table_customer_care){
            $query = $query->where('a.customer_care', $table_customer_care);
        }

        $query = $query->orderBy($order_by, $sort);

//        echo "<pre>"; print_r($query->get()); echo "</pre>"; die;

//        echo "<pre>"; print_r($query->toSql()); echo "</pre>"; die;

        return $query;
    }


    /**
     * @param Request $request
     * @return $this
     * @desc: Kho đã lên S13
     */
    private function __query_s13_building(Request $request){

        $customer_care = $request->get('customer_care', 'all');
        $date_start = $request->get('time_start');
        $date_end = $request->get('time_end');
        $report_type = $request->get('report_type', null);

        $order_by = $request->get('table_order_by', 'a.id');
        $order_by = !empty($order_by) ? $order_by : 'a.id';

        $sort = $request->get('table_sort', 'DESC');
        $sort = !empty($sort) ? $sort : 'DESC';

        $id = $request->get('id', null);
        $name = $request->get('name', null);
        $phone = $request->get('phone', null);
        $email = $request->get('email', null);
        $table_customer_care = $request->get('table_customer_care', null);

        $created = $request->get('table_created', null);

        $created_filter = null;
        if($created){
            $created = explode('-', $created);

            $temp_start =  \DateTime::createFromFormat('d/m/Y', trim($created[0]));
            $temp_end = \DateTime::createFromFormat('d/m/Y', trim($created[1]));

            $created_filter = [
                'start' => $temp_start->format('Y-m-d') . ' 00:00:00',
                'end' => $temp_end->format('Y-m-d') . ' 23:59:59'
            ];

        }

//        echo "<pre>"; print_r($request->all()); echo "</pre>"; die;


        // TODO: disable time filter global
        if(empty($date_start)){
            $date_start = date('Y-m-d', time());
        }

        if(empty($date_end)){
            $date_end = date('Y-m-d', time());
        }

        $time_start = $date_start . ' 00:00:00';
        $time_end = $date_end . ' 23:59:59';

        $select = [
            'a.id', 'a.name', 'a.phone', 'a.email', 'a.time_created',
            'a.customer_care', 'a.first_customer_care', 'a.second_customer_care', 'a.access_try', 'a.access_try_end',
            'b.call_start', 'b.interaction_no', 'b.payment_method', 'b.customer_care',
            'b.s0', 'b.s0_timestamp', 'b.s1', 'b.s1_timestamp', 'b.s2', 'b.s2_timestamp', 'b.s3', 'b.s3_timestamp', 'b.s4', 'b.s4_timestamp',
            'b.s5', 'b.s5_timestamp', 'b.s6', 'b.s6_timestamp', 'b.s7', 'b.s7_timestamp', 'b.s8', 'b.s8_timestamp',
            'b.s9', 'b.s9_timestamp', 'b.s10', 'b.s10_timestamp', 'b.s11', 'b.s11_timestamp', 'b.s12', 'b.s12_timestamp',
            'b.s13', 'b.s13_timestamp', 'b.s14', 'b.s14_timestamp', 'b.s15', 'b.s15_timestamp', 'b.s16', 'b.s16_timestamp', 'b.s17', 'b.s17_timestamp',
            'b.s18', 'b.s18_timestamp',
            'c.call_start AS c_call_start'
        ];

        $tmp_table = DB::raw("(SELECT lead_id, call_start, MAX(interaction_no) AS max_interaction_no FROM lead_interactions GROUP BY lead_id) as c");

        $query = DB::table('leads as a')
            ->select($select)
            ->join('lead_interactions as b', 'a.id', '=', 'b.lead_id')
            ->join($tmp_table, function($join){
                $join->on('b.lead_id', '=', 'c.lead_id');
                $join->on('b.interaction_no', '=', 'c.max_interaction_no');
            })
            ->where('a.status', Lead::STATUS_LIVE)
            ->where('a.is_duplicated', 0);

//        if($report_type == null){
//            $query = $query->where('b.s18', 1);
//        } else {
//            $query = $query->where('b.s13_flag', 1);
//        }

        $query = $query->where('a.access_try', 1);

//        if($report_type != 's16'){
//            $query = $query->whereNull('b.s16');
//        }

        if(!is_null($report_type)){
            if($report_type == 's9_success'){
                $query = $query->where('b.payment_method', LeadInteraction::PAYMENT_METHOD_CK)
                    ->where('b.s12', 1);
            }
            elseif ($report_type == 's10_success'){
                $query = $query->where('b.payment_method', LeadInteraction::PAYMENT_METHOD_COD)
                    ->where('b.s12', 1);
            }
            elseif ($report_type == 'push_ck' || $report_type == 's9'){
                $query = $query->where('b.payment_method', LeadInteraction::PAYMENT_METHOD_CK)
                    ->whereNull('b.s5')
                    ->whereNull('b.s6')
                    ->whereNull('b.s11')
                    ->whereNull('b.s12')
                    ->whereNull('b.s13')
                    ->whereNull('b.s14')
                    ->whereNull('b.s15')
                    ->whereNull('b.s16');
            }
            elseif ($report_type == 'need_actions'){
                $query = $query->whereNull('b.s5')
                    ->whereNull('b.s6')
                    ->whereNull('b.s9')
                    ->whereNull('b.s10')
                    ->whereNull('b.s11')
                    ->whereNull('b.s12')
                    ->whereNull('b.s13')
                    ->whereNull('b.s14')
                    ->whereNull('b.s15')
                    ->whereNotNull('a.customer_care')
                    ->whereNull('a.second_customer_care');
            }
            else{
                $query = $query->where('b.'. $report_type, 1)
                    ->whereNull('b.payment_method');
            }
        }



        // start filter table params
        if($created_filter){
            $query = $query->whereBetween('a.time_created', [$created_filter['start'], $created_filter['end']]);
        }

        if($id){
            $query = $query->where('a.id', $id);
        }

        if($name){
            $query = $query->where('a.name','LIKE',"%{$name}%");
        }
        if($phone){
            $query = $query->where('a.phone','LIKE',"%{$phone}%");
        }
        if($email){
            $query = $query->where('a.email','LIKE',"%{$email}%");
        }

        if($customer_care != 'all' && !empty($customer_care)){

            if($table_customer_care){
                $query = $query->where(function ($q) use($customer_care, $table_customer_care){
                    $q->where('a.customer_care', $table_customer_care);
                    $q->where('a.first_customer_care', $customer_care);
                    $q->orWhere('a.second_customer_care', $customer_care);
                });
            } else {

                $query = $query->where(function ($q) use($customer_care){
                    $q->where('a.customer_care', $customer_care);
                    $q->orWhere('a.first_customer_care', $customer_care);
                    $q->orWhere('a.second_customer_care', $customer_care);
                });
            }

        } elseif ($table_customer_care){
            $query = $query->where('a.customer_care', $table_customer_care);
        }

        $query = $query->orderBy($order_by, $sort);

//        echo "<pre>"; print_r($query->toSql()); echo "</pre>"; die;

        return $query;
    }


    /**
     * @param Request $request
     * @return $this
     * @desc: Trả về các kho các lead cần action của cả team để ca sau có thể nhìn thấy
     */
    private function __t0_query(Request $request){


        $id = $request->get('id', null);
        $name = $request->get('name', null);
        $phone = $request->get('phone', null);
        $email = $request->get('email', null);
        $table_customer_care = $request->get('table_customer_care', null);

        $order_by = $request->get('table_order_by', 'a.id');
        $order_by = !empty($order_by) ? $order_by : 'a.id';

        $sort = $request->get('table_sort', 'DESC');
        $sort = !empty($sort) ? $sort : 'DESC';


        $created = $request->get('table_created', null);

        $created_filter = null;
        if($created){
            $created = explode('-', $created);

            $temp_start =  \DateTime::createFromFormat('d/m/Y', trim($created[0]));
            $temp_end = \DateTime::createFromFormat('d/m/Y', trim($created[1]));

            $created_filter = [
                'start' => $temp_start->format('Y-m-d') . ' 00:00:00',
                'end' => $temp_end->format('Y-m-d') . ' 23:59:59'
            ];

        }



        $date_start = $request->get('time_start');
        $date_end = $request->get('time_end');

        if(empty($date_start)){
            $date_start = date('Y-m-d', time());
        }

        if(empty($date_end)){
            $date_end = date('Y-m-d', time());
        }



        $time_start = $date_start . ' 00:00:00';
        $time_end = $date_end . ' 23:59:59';


        $tmp_select = "ib.lead_id, ib.interaction_no AS b_interaction_no, ib.call_start, ib.s0, ib.s0_timestamp, ib.s1, ib.s1_timestamp, ib.s2, ib.s2_timestamp, ib.s3, ib.s3_timestamp, ib.s4, ib.s4_timestamp, ib.s5, ib.s5_timestamp, 
        ib.s6, ib.s6_timestamp, ib.s7, ib.s7_timestamp, ib.s8, ib.s8_timestamp, ib.s9, ib.s9_timestamp, ib.s10, ib.s10_timestamp, ib.s11, ib.s11_timestamp, 
        ib.s12, ib.s12_timestamp, ib.s13, ib.s13_timestamp, ib.s14, ib.s14_timestamp, ib.s15, ib.s15_timestamp, ib.s16, ib.s16_timestamp, ib.s17, ib.s17_timestamp, ib.s18, ib.s18_timestamp";

        $tmp_table = DB::raw("( SELECT ". $tmp_select ." FROM lead_interactions ib
            INNER JOIN(
                SELECT c.lead_id, c.call_start, MAX(c.interaction_no) AS max_interaction FROM lead_interactions c
                GROUP BY c.lead_id
            ) tem_c 
            ON ib.lead_id=tem_c.lead_id AND
            ib.interaction_no=tem_c.max_interaction
        ) b ");

        $join_table_count = DB::raw("(SELECT lead_id, IFNULL(COUNT(*), 0) AS interaction_count FROM lead_interactions WHERE type='out-going-call' GROUP BY lead_id) as d");


        $select = [
            'a.id', 'a.name', 'a.phone', 'a.email', 'a.time_created', 'a.customer_care', 'a.first_customer_care', 'a.second_customer_care', 'a.access_try', 'a.access_try_end',
            'b.b_interaction_no AS interaction_no', 'b.call_start',
            'b.s0', 'b.s0_timestamp', 'b.s1', 'b.s1_timestamp', 'b.s2', 'b.s2_timestamp', 'b.s3', 'b.s3_timestamp', 'b.s4', 'b.s4_timestamp',
            'b.s5', 'b.s5_timestamp', 'b.s6', 'b.s6_timestamp', 'b.s7', 'b.s7_timestamp', 'b.s8', 'b.s8_timestamp',
            'b.s9', 'b.s9_timestamp', 'b.s10', 'b.s10_timestamp', 'b.s11', 'b.s11_timestamp', 'b.s12', 'b.s12_timestamp',
            'b.s13', 'b.s13_timestamp', 'b.s14', 'b.s14_timestamp', 'b.s15', 'b.s15_timestamp', 'b.s16', 'b.s16_timestamp', 'b.s17', 'b.s17_timestamp', 'b.s18', 'b.s18_timestamp',
            'd.interaction_count'
        ];
        $query = DB::table('leads as a')
            ->select($select)
            ->leftJoin($tmp_table, 'a.id', '=', 'b.lead_id')
            ->leftJoin($join_table_count, function($join){
                $join->on('a.id', '=', 'd.lead_id');
            })
            ->where('a.status', Lead::STATUS_LIVE)
            ->where('a.is_duplicated', 0)
            ->whereNull('b.s16');

        // start filter table params
        if($created_filter){
            $query = $query->whereBetween('a.time_created', [$created_filter['start'], $created_filter['end']]);
        }

        if($id){
            $query = $query->where('a.id', $id);
        }

        if($name){
            $query = $query->where('a.name','LIKE',"%{$name}%");
        }
        if($phone){
            $query = $query->where('a.phone','LIKE',"%{$phone}%");
        }
        if($email){
            $query = $query->where('a.email','LIKE',"%{$email}%");
        }

        if($table_customer_care){
            $query = $query->where('a.customer_care', $table_customer_care);
        }

        $query = $query->orderBy($order_by, $sort);

        return $query;

    }

    public function working(Request $request){

        $customer_care = $request->get('customer_care', 'all');
        $date_start = $request->get('time_start');
        $date_end = $request->get('time_end');
        $report_type = $request->get('report_type', null);

        $created = $request->get('table_created', null);


        $created_filter = null;
        if($created){
            $created = explode('-', $created);

            $temp_start =  \DateTime::createFromFormat('d/m/Y', trim($created[0]));
            $temp_end = \DateTime::createFromFormat('d/m/Y', trim($created[1]));

            $created_filter = [
                'start' => $temp_start->format('Y-m-d') . ' 00:00:00',
                'end' => $temp_end->format('Y-m-d') . ' 23:59:59'
            ];

        }


        if(!empty($date_start)){

        } else {
            $date_start = date('Y-m-d', time());
        }

        if(!empty($date_end)){

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

        $customer_cares = \DB::table('leads')
            ->select(DB::raw('DISTINCT customer_care'))
            ->get()
            ->toArray();
        $customer_care_list = [
            'all' => '-- Tất cả --'
        ];
        foreach ($customer_cares as $obj){
            if(is_null($obj->customer_care)){
                continue;
            }
            $customer_care_list[$obj->customer_care] = $obj->customer_care;
        }

        $params = [
            'customer_care' => $customer_care,
            'time_start'    => $time_start,
            'time_end'      => $time_end,
        ];

        $need_actions_params = [
            'customer_care' => 'all'
        ];

//        Lead::interaction_count_new($params, 's1');

        Lead::interaction_count_new($params, 's18');

        $reportData = [
            's0_count'      => Lead::interaction_count_new($params, 's0'),
            's1_count'      => Lead::interaction_count_new($params, 's1'),
            's2_count'      => Lead::interaction_count_new($params, 's2'),
            's3_count'      => Lead::interaction_count_new($params, 's3'),
            's4_count'      => Lead::interaction_count_new($params, 's4'),
            's7_count'      => Lead::interaction_count_new($params, 's7'),
            's8_count'      => Lead::interaction_count_new($params, 's8'),
            's16_count'     => Lead::interaction_count_new($params, 's16'),
            'push_ck_count' => Lead::interaction_count_new($params, 'push_ck'),
            's18_count'     => Lead::interaction_count_new($params, 's18'),

            'need_actions_repository_count' => Lead::interaction_count_new($need_actions_params, 'need_actions'),

            's5_count'      => Lead::interaction_count_new($params, 's5'),
            's6_count'      => Lead::interaction_count_new($params, 's6'),
            's9_count'      => Lead::interaction_count_new($params, 's9'),
            's10_count'     => Lead::interaction_count_new($params, 's10'),
            's11_count'     => Lead::interaction_count_new($params, 's11'),
            's13_count'     => Lead::interaction_count_new($params, 's13'),
            's14_count'     => Lead::interaction_count_new($params, 's14'),
            't0_count'      => Lead::t0_count(),

            's9_success_count' => Lead::interaction_count_new($params, 's9_success'),
            's10_success_count' => Lead::interaction_count_new($params, 's10_success'),

        ];


        $reportData['total_need_acction_count'] = $reportData['s1_count'] + $reportData['s2_count'] + $reportData['s3_count'] + $reportData['s4_count'] + $reportData['s7_count'] + $reportData['s8_count'];
        $reportData['total_result_count'] = $reportData['s5_count'] + $reportData['s6_count'] + $reportData['s9_count'] + $reportData['s10_count'] + $reportData['s11_count'] + $reportData['s13_count'] + $reportData['s14_count'];
        $reportData['total_success_count'] = $reportData['s9_success_count'] + $reportData['s10_success_count'];
        $reportData['total_count'] = $reportData['total_need_acction_count'] + $reportData['total_result_count'] + $reportData['total_success_count'];


        if ($report_type == 'T0'){
            $report_level_label = 'Kho T0';
            $lead_query = $this->__t0_query($request);
        }
        else {
            $report_level_label = $report_type;
            $lead_query = $this->__query_building_new($request);
        }


        $leads = $lead_query->paginate(15);

        $working_label = ($customer_care == 'all') ? 'Tất cả ' : $customer_care;

        $time_label = ' từ ' . date('d/m-Y', strtotime($date_start)) . ' đến ' . date('d/m-Y', strtotime($date_end));
        if($date_start == $date_end){
            $time_label = ' ngày ' . date('d/m-Y', strtotime($date_end));
        }

        $working_label = $report_level_label . '(' . $working_label . ') ';

        return view('backend.sale.working', [
            'hidden_sidebar' => true,
            'working_label' => $working_label,
            'customer_cares'    => $customer_care_list,
            'customer_care_selected' => $customer_care,
            'reportData'        => $reportData,
            'leads'             => $leads,
            'date_start'        => $date_start,
            'date_end'          => $date_end,
            'report_type' => $report_type
        ]);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @desc: Màn hình làm việc tái sử dụng lại contact đã từ chối (S13);
     */
    public function workingReSale(Request $request){


        $customer_care = $request->get('customer_care', 'all');
        $date_start = $request->get('time_start');
        $date_end = $request->get('time_end');
        $report_type = $request->get('report_type', null);

        $created = $request->get('table_created', null);


        $created_filter = null;
        if($created){
            $created = explode('-', $created);

            $temp_start =  \DateTime::createFromFormat('d/m/Y', trim($created[0]));
            $temp_end = \DateTime::createFromFormat('d/m/Y', trim($created[1]));

            $created_filter = [
                'start' => $temp_start->format('Y-m-d') . ' 00:00:00',
                'end' => $temp_end->format('Y-m-d') . ' 23:59:59'
            ];

        }


        if(!empty($date_start)){

        } else {
            $date_start = date('Y-m-d', time());
        }

        if(!empty($date_end)){

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

        $customer_cares = \DB::table('leads')
            ->select(DB::raw('DISTINCT customer_care'))
            ->get()
            ->toArray();
        $customer_care_list = [
            'all' => '-- Tất cả --'
        ];
        foreach ($customer_cares as $obj){
            if(is_null($obj->customer_care)){
                continue;
            }
            $customer_care_list[$obj->customer_care] = $obj->customer_care;
        }

        $params = [
            'customer_care' => $customer_care,
            'time_start'    => $time_start,
            'time_end'      => $time_end,
        ];

        $need_actions_params = [
            'customer_care' => 'all'
        ];


        $reportData = [
            's13_in_count'   => Lead::interaction_s13_count($params),
            's1_count'      => Lead::interaction_s13_count($params, 's1'),
            's2_count'      => Lead::interaction_s13_count($params, 's2'),
            's3_count'      => Lead::interaction_s13_count($params, 's3'),
            's4_count'      => Lead::interaction_s13_count($params, 's4'),
            's7_count'      => Lead::interaction_s13_count($params, 's7'),
            's8_count'      => Lead::interaction_s13_count($params, 's8'),
            's16_count'     => Lead::interaction_s13_count($params, 's16'),
            'push_ck_count' => Lead::interaction_s13_count($params, 'push_ck'),
            's18_count'     => Lead::interaction_s13_count($params, 's18'),

            'need_actions_repository_count' => Lead::interaction_s13_count($need_actions_params, 'need_actions'),

            's5_count'      => Lead::interaction_s13_count($params, 's5'),
            's6_count'      => Lead::interaction_s13_count($params, 's6'),
            's9_count'      => Lead::interaction_s13_count($params, 's9'),
            's10_count'     => Lead::interaction_s13_count($params, 's10'),
            's11_count'     => Lead::interaction_s13_count($params, 's11'),
            's13_count'     => Lead::interaction_s13_count($params, 's13'),
            's14_count'     => Lead::interaction_s13_count($params, 's14'),
            't0_count'      => Lead::t0_count(),

            's9_success_count' => Lead::interaction_s13_count($params, 's9_success'),
            's10_success_count' => Lead::interaction_s13_count($params, 's10_success'),

        ];


        $reportData['total_need_acction_count'] = $reportData['s1_count'] + $reportData['s2_count'] + $reportData['s3_count'] + $reportData['s4_count'] + $reportData['s7_count'] + $reportData['s8_count'];
        $reportData['total_result_count'] = $reportData['s5_count'] + $reportData['s6_count'] + $reportData['s9_count'] + $reportData['s10_count'] + $reportData['s11_count'] + $reportData['s13_count'] + $reportData['s14_count'];
        $reportData['total_success_count'] = $reportData['s9_success_count'] + $reportData['s10_success_count'];
        $reportData['total_count'] = $reportData['total_need_acction_count'] + $reportData['total_result_count'] + $reportData['total_success_count'];


        if ($report_type == 'T0'){
            $report_level_label = 'Kho T0';
            $lead_query = $this->__t0_query($request);
        }
        else {
            $report_level_label = $report_type;
            $lead_query = $this->__query_s13_building($request);
        }


        $leads = $lead_query->paginate(15);


        $working_label = ($customer_care == 'all') ? 'Tất cả ' : $customer_care;

        $time_label = ' từ ' . date('d/m-Y', strtotime($date_start)) . ' đến ' . date('d/m-Y', strtotime($date_end));
        if($date_start == $date_end){
            $time_label = ' ngày ' . date('d/m-Y', strtotime($date_end));
        }

        $working_label = $report_level_label . '(' . $working_label . ') ';

        return view('backend.sale.working_re_sale', [
            'hidden_sidebar' => true,
            'working_label' => $working_label,
            'customer_cares'    => $customer_care_list,
            'customer_care_selected' => $customer_care,
            'reportData'        => $reportData,
            'leads'             => $leads,
            'date_start'        => $date_start,
            'date_end'          => $date_end,
            'report_type' => $report_type
        ]);

    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * #desc: chi tiết lead màn hình làm việc
     */

    public function detail($id, Request $request){

        $lead = Lead::find($id);

        $interactions = $lead->lead_interactions()
            ->orderByDesc('interaction_no')
            ->get();

        $cp_user = $this->__getCallPasserUser();


        $customer_care_name = $this->__getCustomerCareInfo();

//        if(is_null($customer_care_name)){
//            echo "<pre>"; print_r('Không tìm thấy thông tin customer care. Có thể tài khoản của bạn chưa được cấu hình. '); echo "</pre>"; die;
//        }

//        $interaction_calling = LeadInteraction::where('lead_id', $lead->id)
//            ->where('status', LeadInteraction::STATUS_CALLING)
//            ->first(['id', 'lead_id', 'status', 'type']);

        $interaction_id = '';
//        if($interaction_calling){
//            $interaction_id = $interaction_calling->id;
//        }
        javascript()->put([
           'interaction_id' => $interaction_id
        ]);

        // Kiểm tra xem customer care này có được phép thao tác với lead này hay không?
        $valid_access = $lead->checkValidAccess($customer_care_name);

        $have_cp_user = true;
        if(is_null($cp_user)){
            $have_cp_user = false;
        }

        $data_copy = $this->__get_data_copy($lead);
        javascript()->put([
            'editable_link' => route('backend.sale.working.editable', ['id' => $lead->id]),
            'course_register_link' => route('backend.sale.course_register', ['email' => $lead->email]),
            'upsale_link' => route('backend.sale.upsale', ['lead_id' => $lead->id]),
            'remove_upsale_link' => route('backend.sale.remove_upsale', ['lead_id' => $lead->id]),
            'lead_detail_json_link' => route('backend.sale.lead_json', ['lead_id' => $lead->id]),
            'save_interaction_note_link' => route('backend.sale.working.note', ['id' => $lead->id]),
            'cp_user' => $cp_user,
            'lead_json' => json_encode($data_copy)
        ]);


        $client = new Client();
        $res = $client->request('GET', 'https://hocexcel.online/api/course/listing');
        $course_upsale_availables = [];

        if($res->getStatusCode() === 200){
            $result = $res->getBody();
            $course_upsale_availables = json_decode($result, true);
        }

        $vtiger_lead = json_decode($lead->lead_json);

        $combo_title = !is_null($lead->combo_title) ? $lead->combo_title :  $vtiger_lead->cf_769;


        return view('backend.sale.detail', [
            'hidden_sidebar' => true,
            'lead'          => $lead,
            'combo_title' => $combo_title,
            'vtiger_lead' => $vtiger_lead,
            'interactions'  => $interactions,
            'course_upsale_availables' => $course_upsale_availables,
            'data_copy' => $data_copy,
            'have_cp_user' => $have_cp_user,
            'valid_access' => $valid_access
        ]);
    }


    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @desc: Lưu ghi chú trên màn hình gọi điện
     */
    public function save_note($id, Request $request){

        $lead = Lead::whereId($id)->exists();
        if(!$lead){
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin lead'
            ]);
        }

        $note = $request->get('note');
        $interaction = LeadInteraction::whereLeadId($id)
            ->where('type', LeadInteraction::TYPE_OUT_GOING_CALL)
            ->orderBy('created_at', 'DESC')
            ->first();

        if(!$interaction){
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin cuộc gọi.'
            ]);
        }

        if(!empty($note)){
            $interaction->interaction_note = $note;
            $interaction->save();
        }


        return response()->json([
            'success' => false,
            'message' => 'Ghi chú thành công'
        ]);

    }


    public function lead_json($lead_id){
        $lead = Lead::find($lead_id);
        if(!$lead){
            return response()->json([
                'success' => false,
                'message' => 'Lead not found!'
            ]);
        }


        $data_copy = $this->__get_data_copy($lead);

        return response()->json([
            'success' => true,
            'data_copy' => $data_copy
        ]);

    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editable($id, Request $request){
        $lead = Lead::find($id);
        if(!$lead){
            return response()->json([
                'success' => false,
                'message' => 'Data not found!'
            ]);
        }

        $field = $request->get('field');
        $value = $request->get('value');

        $lead_attr = $lead->attributesToArray();

        if($field != 'note' && !array_key_exists($field, $lead_attr)){
            return response()->json([
                'success' => false,
                'message' => 'attribute is not defined in leads table'
            ]);
        }


        if($field == 'email'){
            if(!filter_var($value, FILTER_VALIDATE_EMAIL)){
                return response()->json([
                    'success' => false,
                    'message' => 'Email không đúng định dạng, vui lòng kiểm tra lại.'
                ]);
            }
        }

        if($field == 'note'){
//            $interaction_id = $request->get('interaction_id');
//            if(empty($interaction_id)){
//                return response()->json([
//                    'success' => false,
//                    'message' => 'Bạn không thể ghi chú được khi chưa thực hiện cuộc gọi'
//                ]);
//            }


            return response()->json([
                'success' => true,
                'message' => 'Success'
            ]);

        }



        $lead->update([
            $field => $value
        ]);


        $data_copy = $this->__get_data_copy($lead);

        $res = [
            'success' => true,
            'new_copy' => $data_copy,
            'message' => 'Success!'
        ];

        // nếu trường update là giá khóa học thì cập nhật lại số tiền phải thanh toán tại upsale_listing
        if($field == 'lead_origin_price'){
            $html = view('backend.sale.detail._upsale_listing', [
                'lead' => $lead,
                'lead_upsales' => $lead->refresh()->lead_upsale()->get()
            ])->render();

            $res['html'] = $html;
        }


        return response()->json($res);

    }


    /**
     * @param $lead_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @desc: Thêm khóa học upsale
     */
    public function upsale($lead_id, Request $request){

        $course_id = $request->get('upsale_course_id');
        $upsale_price = $request->get('upsale_price');

        if(empty($course_id)){
            return response()->json([
                'success' => false,
                'message' => 'Course upsale is not empty!'
            ]);
        }

        if(empty($upsale_price)){
            return response()->json([
                'success' => false,
                'message' => 'Upsale price is not empty!'
            ]);
        }

        $lead = Lead::find($lead_id);
        if(!$lead){
            return response()->json([
                'success' => false,
                'message' => 'Lead not found!'
            ]);
        }

        $client = new Client();
        $res = $client->request('GET', 'https://hocexcel.online/api/course/view/' . $course_id);

        if($res->getStatusCode() === 200){
            $result = $res->getBody();
            $result = json_decode($result, true);

            // thêm khóa học upsale
            $lead->lead_upsale()->updateOrCreate([
                'course_id' => $result['id'],
            ],
                [
                    'course_title' => $result['cou_title'],
                    'origin_price' => $result['cou_price'],
                    'upsale_price' => $upsale_price,
                    'course_json' => json_encode($result)
                ]
            );

            $lead->upsale = 1;
            $lead->update();
            $lead->refresh();

            $data_copy = $this->__get_data_copy($lead);

            $html = view('backend.sale.detail._upsale_listing', [
                'lead' => $lead,
                'lead_upsales' => $lead->lead_upsale()->get()
            ])->render();
            return response()->json([
                'success' => true,
                'new_copy' => $data_copy,
                'html' => $html
            ]);

        } else {
            return response()->json([
                'success' => false,
                'message' => 'Course upsale not found in hocexcel.online.'
            ]);
        }
    }

    /**
     * @param $lead_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @desc: Xóa khóa học upsale ra khỏi lead
     */
    public function removeUpsale($lead_id, Request $request){
        $course_id = $request->get('upsale_course_id');
        if(empty($course_id)){
            return response()->json([
                'success' => false,
                'message' => 'Course upsale is not empty!'
            ]);
        }

        $lead = Lead::find($lead_id);
        if(!$lead){
            return response()->json([
                'success' => false,
                'message' => 'Lead not found!'
            ]);
        }

        $lead_upsale = $lead->lead_upsale()->where('course_id', $course_id)->first();
        if(!$lead_upsale){
            return response()->json([
                'success' => false,
                'message' => 'Item not found!'
            ]);
        }
        $lead_upsale->delete();

        $lead->refresh();

        $lead_upsales = $lead->lead_upsale()->get();
        if(count($lead_upsales) == 0){
            $lead->upsale = 0;
        }

        $lead->update();

        $data_copy = $this->__get_data_copy($lead);
        $html = view('backend.sale.detail._upsale_listing', [
            'lead' => $lead,
            'lead_upsales' => $lead->lead_upsale()->get()
        ])->render();

        return response()->json([
            'success' => true,
            'new_copy' => $data_copy,
            'html' => $html
        ]);

    }



    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @desc: Lấy khóa học tại Hocexcel Online
     */
    public function course_register(Request $request){

        $postData = [
            'email' => $request->get('email')
        ];

        $client = new Client();
        $res = $client->request('POST', 'https://hocexcel.online/api/upsale-json', ['form_params' => $postData]);

        if($res->getStatusCode() === 200){

            $result = $res->getBody();
            $result = json_decode($result, true);

            $html =  view('backend.sale.detail._lead_course_registered', ['result' => $result])->render();

            return response()->json([
                'success' => true,
                'html' => $html
            ]);

        } else {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thông tin học tập từ hocexcel.online. '
            ]);
        }
    }

    public function course_listing(){
        $client = new Client();
        $res = $client->request('POST', 'https://hocexcel.online/api/upsale-json', ['form_params' => $postData]);

        if($res->getStatusCode() === 200){

            $result = $res->getBody();
            $result = json_decode($result, true);

            $html =  view('backend.sale.detail._lead_course_registered', ['result' => $result])->render();

            return response()->json([
                'success' => true,
                'html' => $html
            ]);

        } else {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thông tin học tập từ hocexcel.online. '
            ]);
        }
    }
}