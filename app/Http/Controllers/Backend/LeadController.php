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
use App\Models\Lead;
use App\Models\LeadCodStatus;
use App\User;
use Carbon\Carbon;
use function GuzzleHttp\Psr7\build_query;
use Illuminate\Http\Request;

class LeadController extends Controller
{

    private function __yong_domain_list(){
        return [
            'excelnangcao.hocexcel.online',
            'excelcoban.hocexcel.online',
            'ketoantonghop.hocexcel.online',
            'nghiepvuhanhchinhnhansu.hocexcel.online'
        ];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @desc: Danh sách lead
     */
    public function index(Request $request){

        $loginUser = \Auth::user();
        $access_full = false;
        $full_access_roles = [
            'Administrator',
            'MarketingManager'
        ];

        $roles = $loginUser->roles()->get()->mapWithKeys(function($item){
            return [$item['name'] => $item['name']];
        })->toArray();

        foreach ($roles as $role){
            if(in_array($role, $full_access_roles)){
                $access_full = true;
                break;
            }
        }


//        $now_time  = new \DateTime();
//        $first_day_of_month = $now_time->format('Y-m') . '-01';

        $id = $request->get('id');

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
        $name = $request->get('name');
        $email = $request->get('email');
        $phone = $request->get('phone');
        $mkt_channel = $request->get('mkt_channel');
        $mkt_person = $request->get('mkt_person');
        $utm_medium = $request->get('utm_medium');
        $is_duplicated = $request->get('is_duplicated', 0);


//        if(!empty($time_start) && !empty($time_end)){
//            javascript()->put([
//                'time_start' => date('Y-m-d',strtotime($time_start)),
//                'time_end' => date('Y-m-d',strtotime($time_end))
//            ]);
//        }

        $leadModel = new Lead();
        $leadModel = $leadModel->where('status', Lead::STATUS_LIVE);


        $yong_domain_list = $this->__yong_domain_list();
        // Mếu tài khoản đăng nhập không phải team HEO thì là team outsource => chỉ xem dc lead do team đó tạo ra.
        if(!empty($loginUser->team) &&  $loginUser->team != 'HEO'){

            $user_team = $loginUser->team;
            $leadModel = $leadModel->where(function($q) use ($user_team, $yong_domain_list){
                $q->whereIn('mkt_domain_ref', $yong_domain_list)
                    ->orWhere('mkt_team', $user_team);
            });
        } else {

            // Nếu là tài khoản là của HEO và không phải MarketingManager hoặc Admin thì chỉ được xem lead do HEO mang về
            if(!$access_full){
                $leadModel = $leadModel->where(function($q) use ($yong_domain_list){
                    $q->whereNotIn('mkt_domain_ref', $yong_domain_list)
                        ->orWhereNull('mkt_domain_ref')
                        ->whereNull('mkt_team');
                });
            }
        }

        if(!empty($id)){
            $leadModel = $leadModel->where('id', $id);
        }

        if($created_filter){
            $leadModel = $leadModel->whereBetween('time_created', [$created_filter['start'], $created_filter['end']]);
        }
        if(!empty($name)){
            $leadModel = $leadModel->where('name', 'LIKE', "%{$name}%");
        }
        if(!empty($email)){
            $leadModel = $leadModel->where('email', 'LIKE', "%{$email}%");
        }
        if(!empty($phone)){
            $leadModel = $leadModel->where('phone', $phone);
        }
        if(!empty($mkt_channel)){
            $leadModel = $leadModel->where('mkt_channel', $mkt_channel);
        }

        if(!empty($mkt_person)){
            $leadModel = $leadModel->where('mkt_person', $mkt_person);
        }

        if(!empty($utm_medium)){
            $leadModel = $leadModel->where('utm_medium','LIKE',"%{$utm_medium}%");
        }

        // mặc định sẽ không lấy các lead bị trùng
        if($is_duplicated == 0){
            $leadModel = $leadModel->where('is_duplicated', 0);
        }

        $results = $leadModel->orderBy('time_created', 'DESC')->paginate('15');


        return view('backend.lead.index', [
            'results' => $results,
//            'time_start' => $time_start,
//            'time_end' => $time_end,
            'mkt_channel' => $mkt_channel,
            'mkt_person' => $mkt_person,
            'utm_medium' => $utm_medium,
            'email' => $email,
            'phone' => $phone,
            'is_duplicated' => $is_duplicated,

        ]);


    }


    public function export(Request $request){

        $loginUser = \Auth::user();
        $access_full = false;
        $full_access_roles = [
            'Administrator',
            'MarketingManager'
        ];

        $roles = $loginUser->roles()->get()->mapWithKeys(function($item){
            return [$item['name'] => $item['name']];
        })->toArray();

        foreach ($roles as $role){
            if(in_array($role, $full_access_roles)){
                $access_full = true;
                break;
            }
        }

        $id = $request->get('id');

        $created = $request->get('table_created', null);
        $created_filter = null;
        $time_start = 'all';
        $time_end = 'all';

        if($created){
            $created = explode('-', $created);

            $temp_start =  \DateTime::createFromFormat('d/m/Y', trim($created[0]));
            $temp_end = \DateTime::createFromFormat('d/m/Y', trim($created[1]));

            $created_filter = [
                'start' => $temp_start->format('Y-m-d') . ' 00:00:00',
                'end' => $temp_end->format('Y-m-d') . ' 23:59:59'
            ];

            $time_start = $temp_start->format('Y-m-d');
            $time_end = $temp_end->format('Y-m-d');
        }
        $name = $request->get('name');
        $email = $request->get('email');
        $phone = $request->get('phone');
        $mkt_channel = $request->get('mkt_channel');
        $mkt_person = $request->get('mkt_person');
        $utm_medium = $request->get('utm_medium');
        $is_duplicated = $request->get('is_duplicated', 0);



        $leadModel = new Lead();


        $yong_domain_list = $this->__yong_domain_list();

        // Mếu tài khoản đăng nhập không phải team HEO thì là team outsource => chỉ xem dc lead do team đó tạo ra.
        if(!empty($loginUser->team) &&  $loginUser->team != 'HEO'){
            $user_team = $loginUser->team;
            $leadModel = $leadModel->where(function($q) use ($user_team, $yong_domain_list){
                $q->whereIn('mkt_domain_ref', $yong_domain_list)
                    ->orWhere('mkt_team', $user_team);
            });
        } else {

            // Nếu là tài khoản là của HEO và không phải MarketingManager hoặc Admin thì chỉ được xem lead do HEO mang về
            if(!$access_full){
                $leadModel = $leadModel->where(function($q) use ($yong_domain_list){
                    $q->whereNotIn('mkt_domain_ref', $yong_domain_list)
                        ->orWhereNull('mkt_domain_ref')
                        ->whereNull('mkt_team');
                });
            }
        }

        if(!empty($id)){
            $leadModel = $leadModel->where('id', $id);
        }

        if($created_filter){
            $leadModel = $leadModel->whereBetween('time_created', [$created_filter['start'], $created_filter['end']]);
        }
        if(!empty($name)){
            $leadModel = $leadModel->where('name', 'LIKE', "%{$name}%");
        }
        if(!empty($email)){
            $leadModel = $leadModel->where('email', 'LIKE', "%{$email}%");
        }
        if(!empty($phone)){
            $leadModel = $leadModel->where('phone', $phone);
        }
        if(!empty($mkt_channel)){
            $leadModel = $leadModel->where('mkt_channel', $mkt_channel);
        }

        if(!empty($mkt_person)){
            $leadModel = $leadModel->where('mkt_person', $mkt_person);
        }

        if(!empty($utm_medium)){
            $leadModel = $leadModel->where('utm_medium','LIKE',"%{$utm_medium}%");
        }

        // mặc định sẽ không lấy các lead bị trùng
        if($is_duplicated == 0){
            $leadModel = $leadModel->where('is_duplicated', 0);
        }

        $leadModel = $leadModel->orderBy('time_created', 'DESC');
        $leads = $leadModel->get();

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=lead_export_from_". $time_start . "_to_" .$time_end .".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array(
            'leadID', 'Name', 'Email', 'Phone', 'Cod_address', 'Origin Price', 'Combo Price', 'course_ids', 'crm_lead_source',
            'crm_lead_id', 'crm_lead_number',
            'customer_care_id', 'customer_care', 'first_customer_care_id', 'first_customer_care', 'second_customer_care_id', 'second_customer_care',
            'is_duplicated', 'status', 'customer_ship_fee','cod_fee',
            'ship_fee', 'tax_fee', 'marketing_cost', 'marketing_cost_int', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_tern',
            'utm_content', 'lead_ref', 'mkt_team', 'mkt_channel', 'mkt_code', 'mkt_person', 'mkt_type', 'mkt_landing_page', 'mkt_ad_group', 'mkt_ad_id',
//            'mkt_cost',
//            'mkt_dynamic_cost',
            'teaching_partner_share',
            'time_created', 'date_created', 'hour_created', 'created_at', 'updated_at',
            'Payment method', 's9', 's9_timestamp', 's10', 's10_timestamp', 's11', 's11_timestamp', 's12', 's12_timestamp', 's13', 's13_timestamp',
            's14', 's14_timestamp', 's15', 's15_timestamp'
        );

        $callback = function() use ($leads, $columns, $time_start, $time_end)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            /** @var  $lead Lead */
            foreach($leads as $lead) {

                $line_array = [
                    $lead->id, html_entity_decode($lead->name), $lead->email, $lead->phone, html_entity_decode($lead->cod_address), $lead->lead_price, $lead->lead_origin_price, $lead->course_ids, $lead->crm_lead_source,
                    $lead->crm_lead_id, $lead->crm_lead_number,
                    $lead->customer_care_id, $lead->customer_care, $lead->first_customer_care_id, $lead->first_customer_care, $lead->second_customer_care_id, $lead->second_customer_care,
                    $lead->is_duplicated, $lead->status, $lead->customer_ship_fee, $lead->cod_fee,
                    $lead->ship_fee, $lead->tax_fee, $lead->marketing_cost, $lead->marketing_cost_int, $lead->utm_source, $lead->utm_medium, $lead->utm_campaign, $lead->utm_tern,
                    $lead->utm_content, $lead->lead_ref, $lead->mkt_team, $lead->mkt_channel, $lead->mkt_code, $lead->mkt_person, $lead->mkt_type, $lead->mkt_landing_page,$lead->mkt_ad_group, $lead->mkt_ad_id,
//                    $lead->getMarketingCost($time_start, $time_end),
//                    $lead->calcMktDynamicCost($time_start, $time_end),
                    $lead->production_cost,
                    $lead->time_created, $lead->date_created, $lead->hour_created, $lead->created_at, $lead->updated_at,
                    $lead->payment_method, $lead->s9, $lead->s9_timestamp, $lead->s10, $lead->s10_timestamp, $lead->s11, $lead->s11_timestamp, $lead->s12, $lead->s12_timestamp, $lead->s13, $lead->s13_timestamp,
                    $lead->s14, $lead->s14_timestamp, $lead->s15, $lead->s15_timestamp
                ];
                fputcsv($file, $line_array);
            }
            fclose($file);
        };
        return \Response::stream($callback, 200, $headers);

    }


    /**
     * @param Request $request
     * @desc: Export dữ liệu lead
     */
    public function exportExcel(Request $request){

        $time_start = $request->get('time_start');
        $time_end = $request->get('time_start');

//        $mkt_person = $request->get('mkt_person');
//        $utm_medium = $request->get('utm_medium');

//        echo "<pre>"; print_r(111); echo "</pre>"; die;


        $leads = Lead::where('status', Lead::STATUS_LIVE)
            ->whereBetween('date_created', [$time_start, $time_end]);


        \Excel::create('Report', function($excel) use ($leads) {
            $excel->sheet('report', function($sheet) use($leads) {

//                $sheet->appendRow(array(
//                    'id', 'landing', 'departure', 'phone_id'
//                ));
                $leads->chunk(100, function($rows) use ($sheet)
                {
                    foreach ($rows as $row)
                    {
                        $sheet->appendRow($row);
                    }
                });
            });
        })->download('xlsx');
    }



    public function detail($id){

        $loginUser = \Auth::user();

        $lead = Lead::whereId($id)->first();
//        echo "<pre>"; print_r($lead); echo "</pre>"; die;

        if(!$lead)
            abort(404);


        $yong_domain_list = $this->__yong_domain_list();


        if(!empty($loginUser->team) && $loginUser->team != 'HEO'){


            // marketer team khác  không được truy cập lead của team HEO
            if($lead->mkt_team != $loginUser->team && !in_array($lead->mkt_domain_ref, $yong_domain_list)){

                return redirect(route('backend.lead.index'));
            }
        } elseif(in_array($lead->mkt_domain_ref, $yong_domain_list))  {

            // marketer HEO không được truy cập lead của team khác
            return redirect(route('backend.lead.index'));
        }

        $data = [
            'lead' => $lead
        ];
        if($lead->is_duplicated){
            $lead_duplicated = $lead->find($lead->duplicated_with_id);
            $data['lead_duplicated'] = $lead_duplicated;
        }

        $lead_crm = false;
        if($lead->lead_ref === 'vtiger_crm'){
            $lead_crm = json_decode($lead->lead_json, true);
        }

        $data['lead_crm'] = $lead_crm;

        $lead_cod_statuses = $lead->lead_cod_status()->orderBy('created_at', 'DESC')->get();

        $data['lead_cod_statuses'] = $lead_cod_statuses;

        return view('backend.lead.detail', $data);

    }

}