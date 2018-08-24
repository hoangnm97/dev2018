<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2/5/2018
 * Time: 9:29 AM
 */
namespace App\Http\Controllers\Api;


use App\Core\FaceBookSdk;
use App\Events\Backend\UpdateLeadEvent;
use App\Http\Controllers\Controller;
use App\Models\AdAccount;
use App\Models\Lead;
use App\Models\LeadCodStatus;
use App\Models\LeadInteraction;
use App\Models\LeadSaleStatus;
use Faker\Provider\DateTime;
use function GuzzleHttp\Psr7\build_query;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LeadController extends Controller
{

    public $interaction;

    public function __construct()
    {
        $this->interaction = new LeadInteraction();
    }


    private function __validate_request(Request $request){

        $key = $request->get('key');
        $cipher = $request->get('cipher');

        if($key === config('app.token_key') && $cipher === config('app.cipher')){
            return true;
        }
        return false;
    }



    public function updateStatus(Request $request){

        if (!$this->__validate_request($request)){
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực kết nối',
            ]);
        }

        $crm_lead_id    = $request->get('crm_lead_id');
        $cod_status     = $request->get('cod_status');

        $lead = Lead::getByCrmId($crm_lead_id);

        if(!$lead){
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin',
            ]);
        }


        $lead_update = [
            'cod_status' => $cod_status
        ];

        // Những trạng thái trả về thể hiện đơn bị thất bại
        $failStatuses =  [
            'Failed',
            'Canceled',
            'Aborted',
            'CarrierCanceled',
        ];
        // nếu status thất bại
        if(in_array($cod_status, $failStatuses)){
            $lead_update['sale_success'] = 0;
        }


        if(in_array($cod_status, ['Canceled', 'Aborted', 'CarrierCanceled'])){

            $lead_update['sale_success'] = 0;
            $lead_update['in_cancel'] = 1;
            $lead_update['in_cancel_at'] = date('Y-m-d H:i:s');

            // nếu chưa log S11(Hủy đơn) thì ghi log S11
            if($lead->s11 == 0){

                $intaction_data = [
                    'deal_status'       => LeadInteraction::DEAL_STATUS_AFTER_SALE,
                    'call_start'        => Carbon::now()->toDateTimeString(),
                    'call_end'          => Carbon::now()->toDateTimeString(),
                    'duration'          => 0,
                    'interaction_note'  => null,
                    'update_status'     => 1,
                    'level'             => 's11',
                    'level_timestamp'   => Carbon::now()->toDateTimeString(),
                ];


                $interaction = $this->interaction->createInteraction($lead, $intaction_data, LeadInteraction::TYPE_API);

                $lead_update['interaction_no']          = $interaction['interaction_no'];
                $lead_update['payment_method']          = $interaction['payment_method'];
                $lead_update['deal_status']             = $interaction['deal_status'];
                $lead_update[$intaction_data['level']]  = 1;
                $lead_update[$intaction_data['level'] . '_timestamp'] = Carbon::now()->toDateTimeString();
            }

            // start log lead interactions S13
            $intaction_data = [
                'deal_status'       => LeadInteraction::DEAL_STATUS_AFTER_SALE,
                'call_start'        => Carbon::now()->toDateTimeString(),
                'call_end'          => Carbon::now()->toDateTimeString(),
                'duration'          => 0,
                'interaction_note'  => null,
                'update_status'     => 1,
                'level'             => 's13',
                'level_timestamp'   => Carbon::now()->toDateTimeString(),
            ];


            $interaction = $this->interaction->createInteraction($lead, $intaction_data, LeadInteraction::TYPE_API);

            $lead_update['interaction_no']          = $interaction['interaction_no'];
            $lead_update['payment_method']          = $interaction['payment_method'];
            $lead_update['deal_status']             = $interaction['deal_status'];
            $lead_update['s13_flag']                = $interaction['s13_flag'];
            $lead_update[$intaction_data['level']]  = 1;
            $lead_update[$intaction_data['level'] . '_timestamp'] = Carbon::now()->toDateTimeString();

        }

        // thành công
        elseif($cod_status === 'Success'){

            $lead_update['sale_success'] = 1;
            $lead_update['sale_success_updated'] = date('Y-m-d H:i:s');
            $lead_update['in_cancel'] = 0;
            $lead_update['in_cancel_at'] = null;


            if($lead->s12 ==0){

                // start log lead interactions S12
                $intaction_data = [
                    'deal_status'       => LeadInteraction::DEAL_STATUS_AFTER_SALE,
                    'call_start'        => Carbon::now()->toDateTimeString(),
                    'call_end'          => Carbon::now()->toDateTimeString(),
                    'duration'          => 0,
                    'interaction_note'  => null,
                    'update_status'     => 1,
                    'level'             => 's12',
                    'level_timestamp'   => Carbon::now()->toDateTimeString(),
                ];


                $interaction = $this->interaction->createInteraction($lead, $intaction_data, LeadInteraction::TYPE_API);

                $lead_update['interaction_no']          = $interaction['interaction_no'];
                $lead_update['payment_method']          = $interaction['payment_method'];
                $lead_update['deal_status']             = $interaction['deal_status'];
                $lead_update['s13_flag']                = $interaction['s13_flag'];
                $lead_update[$intaction_data['level']]  = 1;
                $lead_update[$intaction_data['level'] . '_timestamp'] = Carbon::now()->toDateTimeString();

            }

        }


        $lead->update($lead_update);
        $lead->refresh();

        $lead->lead_cod_status()->firstOrCreate([
            'lead_date_created' => date('Y-m-d', strtotime($lead->date_created)),
            'status' => $cod_status
        ]);

        event( new UpdateLeadEvent($lead));


        return response()->json([
            'success' => true,
            'message' => 'Thành công',
            'data' => $lead
        ]);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @desc: Update lead via API
     */
    public function update(Request $request){
        if (!$this->__validate_request($request)){
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực kết nối',
            ]);
        }


        $data = $request->get('data');

        $crm_lead_id    = array_get($data, 'crm_lead_id');

        $lead = Lead::getByCrmId($crm_lead_id);
        if(!$lead){
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin',
            ]);
        }

        \Log::info('----------------------------Log in listen API------------------------');
        \Log::info($data);

        $payment_method = ($lead->in_cod == 1) ? 'COD' : 'CK';
        $customer_care = $lead->customer_care;

        $dataUpdate = [];

        // cập nhật lên L9
        if(isset($data['sale_success'])){
            $dataUpdate['sale_success'] = $data['sale_success'];

            $dataUpdate['sale_success_updated'] = Carbon::now();
            if(isset($data['sale_success_updated']))
                $dataUpdate['sale_success_updated'] = $data['sale_success_updated'];

            // update hủy = 0
            $dataUpdate['in_cancel'] = 0;
            $dataUpdate['in_cancel_at'] = null;

            if($lead->s12 == 0){
                // start log lead interactions S12
                $intaction_data = [
                    'deal_status'       => LeadInteraction::DEAL_STATUS_AFTER_SALE,
                    'call_start'        => Carbon::now()->toDateTimeString(),
                    'call_end'          => Carbon::now()->toDateTimeString(),
                    'duration'          => 0,
                    'interaction_note'  => null,
                    'update_status'     => 1,
                    'level'             => 's12',
                    'level_timestamp'   => Carbon::now()->toDateTimeString(),
                ];


                $interaction = $this->interaction->createInteraction($lead, $intaction_data, LeadInteraction::TYPE_ACTIVE);

                $dataUpdate['interaction_no']          = $interaction['interaction_no'];
                $dataUpdate['payment_method']          = $interaction['payment_method'];
                $dataUpdate['deal_status']             = $interaction['deal_status'];
                $dataUpdate['s13_flag']                = $interaction['s13_flag'];
                $dataUpdate[$intaction_data['level']]  = 1;
                $dataUpdate[$intaction_data['level'] . '_timestamp'] = Carbon::now()->toDateTimeString();
            }
        }

        if(isset($data['cod_fee']))
            $dataUpdate['cod_fee'] = $data['cod_fee'];

        if(isset($data['ship_fee']))
            $dataUpdate['ship_fee'] = $data['ship_fee'];


        // cod được kích hoạt
        if(isset($data['cod_active'])){
            $dataUpdate['cod_active'] = $data['cod_active'];

            $dataUpdate['cod_active_at'] = Carbon::now()->toDateTimeString();
            if(isset($data['cod_active_at']))
                $dataUpdate['cod_active_at'] = $data['cod_active_at'];

            // nếu chưa lên L9 thì cập nhật lên L9
            if($data['cod_active'] == 1 && $lead->sale_success == 0){
                $dataUpdate['sale_success'] = 1;
                $dataUpdate['sale_success_updated'] = Carbon::now()->toDateTimeString();
            }


            $interaction_no = LeadInteraction::whereLeadId($lead->id)->count();
            $interaction_no = $interaction_no + 1;
            $curent_interaction = LeadInteraction::whereLeadId($lead->id)
                ->where('interaction_no', $lead->interaction_no)
                ->first();
            if($curent_interaction){
                $payment_method = $curent_interaction->payment_method;
            }

            // start log lead interactions S12
            if($lead->s12 == 0){

                $intaction_data = [
                    'deal_status'       => LeadInteraction::DEAL_STATUS_AFTER_SALE,
                    'call_start'        => Carbon::now()->toDateTimeString(),
                    'call_end'          => Carbon::now()->toDateTimeString(),
                    'duration'          => 0,
                    'interaction_note'  => null,
                    'update_status'     => 1,
                    'level'             => 's12',
                    'level_timestamp'   => Carbon::now()->toDateTimeString(),
                ];


                $interaction = $this->interaction->createInteraction($lead, $intaction_data, LeadInteraction::TYPE_ACTIVE);

                $dataUpdate['interaction_no']          = $interaction['interaction_no'];
                $dataUpdate['payment_method']          = $interaction['payment_method'];
                $dataUpdate['deal_status']             = $interaction['deal_status'];
                $dataUpdate['s13_flag']                = $interaction['s13_flag'];
                $dataUpdate[$intaction_data['level']]  = 1;
                $dataUpdate[$intaction_data['level'] . '_timestamp'] = Carbon::now()->toDateTimeString();

            }

            //  // start log lead interactions S15 active code course
            $intaction_data = [
                'deal_status'       => LeadInteraction::DEAL_STATUS_AFTER_SALE,
                'call_start'        => Carbon::now()->toDateTimeString(),
                'call_end'          => Carbon::now()->toDateTimeString(),
                'duration'          => 0,
                'interaction_note'  => null,
                'update_status'     => 1,
                'level'             => 's15',
                'level_timestamp'   => Carbon::now()->toDateTimeString(),
            ];

            \Log::info('-------------update Interaction S15 : ' . $lead->id);


            $interaction = $this->interaction->createInteraction($lead, $intaction_data, LeadInteraction::TYPE_ACTIVE);

            $dataUpdate['interaction_no']          = $interaction['interaction_no'];
            $dataUpdate['payment_method']          = $interaction['payment_method'];
            $dataUpdate['deal_status']             = $interaction['deal_status'];
            $dataUpdate['s13_flag']                = $interaction['s13_flag'];
            $dataUpdate[$intaction_data['level']]  = 1;
            $dataUpdate[$intaction_data['level'] . '_timestamp'] = Carbon::now()->toDateTimeString();


            // Cập nhật trạng thái giao hàng lên thành công (vì lý do nào đó sẽ không dc hãng vận chuyển cập nhật trạng thái) nếu trạng thái giao hàng chưa thành công
            if($data['cod_active'] == 1 && $lead->cod_status != 'Success'){
                $lead->lead_cod_status()->firstOrCreate([
                    'lead_date_created' => Carbon::now()->toDateTimeString(),
                    'status' => 'Success'
                ]);
            }
        }

        // Hủy COD
        if(array_key_exists('in_cancel', $data)){

            \Log::info('======================In_cancel: ' . $data['in_cancel'] . ' in_cancel_at: ' . $data['in_cancel_at']);

            $dataUpdate['in_cancel'] = $data['in_cancel'];
            $dataUpdate['in_cancel_at'] = $data['in_cancel_at'];

            $interaction_note = isset($data['reason']) ? $data['reason'] : null;

            // nếu cod bị hủy => final_status = Not Success
            if($dataUpdate['in_cancel'] == 1){


                $interaction_count = LeadInteraction::whereLeadId($lead->id)->count();
                $interaction_no = $interaction_count + 1;
                $curent_interaction = LeadInteraction::whereLeadId($lead->id)
                    ->where('interaction_no', $lead->interaction_no)
                    ->first();
                if($curent_interaction){
                    $payment_method = $curent_interaction->payment_method;
                }

                // nếu chưa log S11(Hủy đơn) thì ghi log S11
                if($lead->s11 == 0){
                    $intaction_data = [
                        'deal_status'       => LeadInteraction::DEAL_STATUS_AFTER_SALE,
                        'call_start'        => Carbon::now()->toDateTimeString(),
                        'call_end'          => Carbon::now()->toDateTimeString(),
                        'duration'          => 0,
                        'interaction_note'  => null,
                        'update_status'     => 1,
                        'level'             => 's11',
                        'level_timestamp'   => Carbon::now()->toDateTimeString(),
                    ];


                    $interaction = $this->interaction->createInteraction($lead, $intaction_data, LeadInteraction::TYPE_API);

                    $dataUpdate['interaction_no']          = $interaction['interaction_no'];
                    $dataUpdate['payment_method']          = $interaction['payment_method'];
                    $dataUpdate['deal_status']             = $interaction['deal_status'];
                    $dataUpdate['s13_flag']                = $interaction['s13_flag'];
                    $dataUpdate[$intaction_data['level']]  = 1;
                    $dataUpdate[$intaction_data['level'] . '_timestamp'] = Carbon::now()->toDateTimeString();
                }

                // log interaction S13 Not success
                $intaction_data = [
                    'deal_status'       => LeadInteraction::DEAL_STATUS_AFTER_SALE,
                    'call_start'        => Carbon::now()->toDateTimeString(),
                    'call_end'          => Carbon::now()->toDateTimeString(),
                    'duration'          => 0,
                    'interaction_note'  => null,
                    'update_status'     => 1,
                    'level'             => 's13',
                    'level_timestamp'   => Carbon::now()->toDateTimeString(),
                ];


                $interaction = $this->interaction->createInteraction($lead, $intaction_data, LeadInteraction::TYPE_API);

                $dataUpdate['interaction_no']          = $interaction['interaction_no'];
                $dataUpdate['payment_method']          = $interaction['payment_method'];
                $dataUpdate['deal_status']             = $interaction['deal_status'];
                $dataUpdate['s13_flag']                = $interaction['s13_flag'];
                $dataUpdate[$intaction_data['level']]  = 1;
                $dataUpdate[$intaction_data['level'] . '_timestamp'] = Carbon::now()->toDateTimeString();

//                $dataUpdate['sale_success'] = 0;
//                $dataUpdate['sale_success_updated'] = Carbon::now();

//                $dataUpdate['final_status'] = Lead::FINAL_NOT_SUCCESS;
//                $dataUpdate['final_timestamp'] = Carbon::now();
            }
        }


        // nếu có truyền sang trạng thái COD và không bằng trạng thái COD hiện tại
        if(array_key_exists('cod_status', $data)){
            if ($data['cod_status'] != $lead->cod_status){

                $lead->cod_status = $data['cod_status'];

                $lead_cod_status = [
                    'status' => $data['cod_status'],
                    'lead_date_created' => Carbon::now()->toDateTimeString()
                ];
                if(isset($data['reason'])){
                    $lead_cod_status['reason'] = $data['reason'];
                }

                $lead->lead_cod_status()->firstOrCreate($lead_cod_status);
            }
        }


        // update kích hoạt khóa học thử => thêm trạng thái kích hoạt khóa học thử
        if(array_key_exists('active_try', $data)){

            $active_try_timestamp = isset($data['active_try_at']) ? $data['active_try_at'] : Carbon::now()->toDateTimeString();

            $intaction_data = [
                'deal_status'       => LeadInteraction::DEAL_STATUS_AFTER_SALE,
                'call_start'        => $active_try_timestamp,
                'call_end'          => $active_try_timestamp,
                'duration'          => 0,
                'interaction_note'  => null,
                'update_status'     => 1,
                'level'             => 's18',
                'level_timestamp'   => $active_try_timestamp,
            ];


            $interaction = $this->interaction->createInteraction($lead, $intaction_data, LeadInteraction::TYPE_API);

            $dataUpdate['interaction_no']          = $interaction['interaction_no'];
            $dataUpdate['payment_method']          = $interaction['payment_method'];
            $dataUpdate['deal_status']             = $interaction['deal_status'];
            $dataUpdate['s13_flag']                = $interaction['s13_flag'];
            $dataUpdate[$intaction_data['level']]  = 1;
            $dataUpdate[$intaction_data['level'] . '_timestamp'] = $active_try_timestamp;

            $dataUpdate['access_try'] = 1;
            $dataUpdate['access_try_end'] = $data['access_try_end'];
        }


        \Log::info('========> API update Leads Listen: crm_lead_id: ' . $lead->crm_lead_id);

        $lead->update($dataUpdate);

        event( new UpdateLeadEvent($lead));

        return response()->json([
            'success' => true,
            'message' => 'Thành công',
            'data' => $lead
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @desc: Delete via API
     */
    public function delete(Request $request){

        if (!$this->__validate_request($request)){
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực kết nối',
            ]);
        }

        $crm_lead_id    = $request->get('crm_lead_id');

        $lead = Lead::getByCrmId($crm_lead_id);

        if(!$lead){
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin',
            ]);
        }

        $lead->status = -1;
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Thành công',
            'data' => $lead
        ]);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @desc: Lắng nghe submit status từ CallPasser App
     */
    public function listenCallPasser(Request $request){

        $crm_lead_id = $request->get('contact_id');
        $cp_username = $request->get('username');

        $call_start = $request->get('call_start');
        $call_end = $request->get('call_end');
        $duration = $request->get('duration');

        $dataCP = $request->get('data');


        $status = $dataCP['status_id'];
        $sub_status = $dataCP['type_id'];
        $call_note = $dataCP['note_text'];


        $lead = Lead::whereCrmLeadId($crm_lead_id)
            ->where('status', Lead::STATUS_LIVE)
            ->first();

        if(!$lead){
            \Log::info('Không tìm thấy thông tin ở panda.' . $crm_lead_id);
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin ở panda.'
            ]);
        }

        // trigger first time call
        $sum_sale_care = (int)$lead->sale_t1_status + (int)$lead->sale_t2_status + (int)$lead->sale_t3_status;

//        \Log::info('===============>sum_sale_care: ' . $sum_sale_care);

        $lead_update = [];

        if($sum_sale_care == 0){
            $carbon_now = Carbon::now();
            $minutes_diff = $carbon_now->diffInMinutes($lead->created_at);

            $lead_update['first_call_timestamp'] = $carbon_now->toDateTimeString();
            $lead_update['diff_to_created'] = $minutes_diff;

//            \Log::info('=======================> Trigger first call timestamp: ' . $carbon_now->toDateTimeString() . ' - diff: ' . $minutes_diff);
        }

        if(empty($cp_username)){
            \Log::info('Không tìm thấy thông tin của sale [0]');
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin của sale [0]'
            ]);
        }

        /** Lấy thông tin sale chăm sóc*/
        $customer_care = LeadSaleStatus::getCustomerCare($cp_username);

        if(is_null($customer_care)){
            \Log::info('Không tìm thấy thông tin của sale [1]');
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin của sale [1]'
            ]);
        }

        $start_call_timestamp = date('Y-m-d H:i:s', $call_start/1000);
        $end_call_timestamp = date('Y-m-d H:i:s', $call_end/1000);

        // Xác định số lần chăm sóc
        $care_times = 1;
        $t1_care = ($lead->sale_t1_status == 0) ? 0 : 1;
        $t2_care = ($lead->sale_t2_status == 0) ? 0 : 1;
        $t3_care = ($lead->sale_t3_status == 0) ? 0 : 1;

        $total_care = $t1_care + $t2_care + $t3_care;


        if($total_care == 0){
            $care_times = 1;
        }
        elseif ($total_care == 1){
            if($lead->t1_status_updated != null){
                $time_now = Carbon::now();
                $diff_time_minutes = $time_now->diffInMinutes($lead->t1_status_updated);
                if($diff_time_minutes > 30){
                    $care_times = 2;
                }
            }
        }
        elseif ($total_care == 2){
            $care_times = 2;
            if($lead->t2_status_updated != null){
                $time_now = Carbon::now();
                $diff_time_minutes = $time_now->diffInMinutes($lead->t2_status_updated);
                if($diff_time_minutes > 30){
                    $care_times = 3;
                }
            }
        } else {
            $care_times = 3;
        }


        $sale_status = 1;

        // lấy status hệ thống từ status từ CP
        $system_status = LeadSaleStatus::getStatusFromCP($care_times, $status);

        $crm_in_cod                 = 0;
        $crm_in_wait_cod            = 0;
        $crm_in_wait_tranfer_money  = 0;
        $crm_in_wait_topup          = 0;


        $final_status = null;

        if($status == 8){
            if($sub_status == "8_1"){
                $crm_in_cod = 1;
                $crm_in_wait_tranfer_money = 0;
            } else {
                $crm_in_cod = 0;
                $crm_in_wait_tranfer_money = 1;
            }

            $final_status = Lead::FINAL_SUCCESS;

        } elseif ($status == 7){
            $final_status = Lead::FINAL_NOT_SUCCESS;
        } elseif ($status == 5 || $status == 6){
            $final_status = Lead::FINAL_OTHER;
        }

        $lead_update['in_cod'] = $crm_in_cod;
        $lead_update['in_wait_tranfer_money'] = $crm_in_wait_tranfer_money;


        if($care_times == 1){
            $lead_update['sale_t1_status'] = $system_status;
            $lead_update['sale_t1_duration'] = $duration;
            $lead_update['t1_status_updated'] = $end_call_timestamp;

            if($system_status == LeadSaleStatus::T1_KMU_STATUS){
                $sale_status = -1;
            }
        }
        elseif ($care_times == 2){
            $lead_update['sale_t2_status'] = $system_status;
            $lead_update['sale_t2_duration'] = $duration;
            $lead_update['t2_status_updated'] = $end_call_timestamp;

            if($system_status == LeadSaleStatus::T2_KMU_STATUS){
                $sale_status = -1;
            }
        }
        elseif ($care_times == 3){
            $lead_update['sale_t3_status'] = $system_status;
            $lead_update['sale_t3_duration'] = $duration;
            $lead_update['t3_status_updated'] = $end_call_timestamp;

            if($system_status == LeadSaleStatus::T3_KMU_STATUS){
                $sale_status = -1;
            }
        }

        $lead_update['sale_status'] = $sale_status;
        $lead_update['final_status'] = $final_status;

        if($final_status != null){
            $lead_update['final_timestamp'] = Carbon::now()->toDateTimeString();;
        } else {
            $lead_update['final_timestamp'] = null;
        }


        // customer_care cuối lần chăm sóc gần đây nhất
        $last_customer_care = $lead->customer_care;

        $now_customer_care = $customer_care['customer_care'];
        $now_customer_care_id = $customer_care['id'];

        if(!$lead->checkValidAccess($now_customer_care)){
            return response()->json([
                'success' => false,
                'message' => 'Bạn không được phép thao tác với lead này.'
            ]);
        }


        $first_customer_care = $lead->first_customer_care;
        $first_customer_care_id = $lead->first_customer_care_id;

        if( empty($lead->first_customer_care) ){
            $first_customer_care = $now_customer_care;
        }

        if( empty($lead->first_customer_care_id) ){
            $first_customer_care_id = $now_customer_care_id;
        }

        $second_customer_care = $lead->second_customer_care;
        $second_customer_care_id = $lead->second_customer_care_id;

        if($now_customer_care != $first_customer_care){
            $second_customer_care = $now_customer_care;
            $second_customer_care_id = $now_customer_care_id;
        }


        \Log::info('now_customer_care: ' . $now_customer_care . ' -> first_customer_care: ' . $first_customer_care . ' -> second_customer_care: ' . $second_customer_care);

        $lead_update['customer_care'] = $now_customer_care;
        $lead_update['customer_care_id'] = $now_customer_care_id;
        $lead_update['first_customer_care'] = $first_customer_care;
        $lead_update['first_customer_care_id'] = $first_customer_care_id;
        $lead_update['second_customer_care'] = $second_customer_care;
        $lead_update['second_customer_care_id'] = $second_customer_care_id;

//        echo "<pre>"; print_r($lead_update); echo "</pre>"; die;

        $intaction_data = [
            'type' => LeadInteraction::TYPE_OUT_GOING_CALL,
            'deal_status' => LeadInteraction::DEAL_STATUS_SALE,
            'payment_method' => $lead->payment_method,
            'call_start' => $start_call_timestamp,
            'call_end' => $end_call_timestamp,
            'duration' => $duration,
            'interaction_note' => $call_note,
            'call_json' => json_encode($request->all()),
            'customer_care' => $now_customer_care,
            'customer_care_id' => $now_customer_care_id,
            'first_customer_care' => $first_customer_care,
            'first_customer_care_id' => $first_customer_care_id,
            'second_customer_care' => $second_customer_care,
            'second_customer_care_id' => $second_customer_care_id,
            'update_status' => 1
        ];

        // nếu trước đó đã lên COD hoặc CK thì deal_status về after sale
        if(!is_null($lead->payment_method)){
            $intaction_data['deal_status'] = LeadInteraction::DEAL_STATUS_AFTER_SALE;
        }

        // Không nhấc máy
        if($status == 1){
            $intaction_data['level'] = 's1';
        }
        // KLL
        if($status == 3){
            $intaction_data['level'] = 's2';
        }
        // SNT
        if($status == 4){
            $intaction_data['level'] = 's3';
        }
        // SNT
        if($status == 2){
            $intaction_data['level'] = 's4';
        }
        // SSO
        if($status == 5){
            $intaction_data['level'] = 's5';
        }
        // TSO
        if($status == 6){
            $intaction_data['level'] = 's6';
        }

        // Không mua =? Not success
        if($status == 7){
            $intaction_data['level'] = 's13';
            if($sub_status == '7_1'){
                $intaction_data['s13_sub'] = LeadInteraction::S13_CUSTOMER;
            } elseif ($sub_status == '7_2'){
                $intaction_data['s13_sub'] = LeadInteraction::S13_SALE;
            }
        }

        // success
        if($status == 8){
            // CK
            if($sub_status == "8_2"){
                $intaction_data['level'] = 's9';

                $intaction_data['payment_method'] = LeadInteraction::PAYMENT_METHOD_CK;
            }
            // COD
            if($sub_status == "8_1"){
                $intaction_data['level'] = 's10';

                $intaction_data['payment_method'] = LeadInteraction::PAYMENT_METHOD_COD;
            }
        }
        // Hủy
        if($status == 9){
            $intaction_data['level'] = 's11';
        }

        // ghi nhận push ck và khách vẫn ok ck
        if($status == 10){
            $intaction_data['level'] = 's17';
        }

        $intaction_data['level_timestamp'] = $end_call_timestamp;

        $interaction_type = LeadInteraction::TYPE_OUT_GOING_CALL;
        $interaction = $this->interaction->createInteraction($lead, $intaction_data, $interaction_type);

        // update ngược thông tin interaction vào lead.
        $lead_update['interaction_no']          = $interaction['interaction_no'];
        $lead_update['payment_method']          = $interaction['payment_method'];
        $lead_update['deal_status']             = $interaction['deal_status'];
        $lead_update['s13_sub']                 = $interaction['s13_sub'];
        $lead_update['s13_flag']                = $interaction['s13_flag'];
        $lead_update[$intaction_data['level']]  = 1;
        $lead_update[$intaction_data['level']. '_timestamp'] = $intaction_data['level_timestamp'];

        // ghi log not success nếu là ck và status hủy đơn (vì hủy CK thì không có API báo hủy về sau)
        if($status == 9 && $lead->payment_method == LeadInteraction::PAYMENT_METHOD_CK){

            $interaction_type = LeadInteraction::TYPE_SYSTEM;

            $intaction_data['type']             = LeadInteraction::TYPE_SYSTEM;
            $intaction_data['deal_status']      = LeadInteraction::DEAL_STATUS_AFTER_SALE;
            $intaction_data['level']            = 's13';
            $intaction_data['level_timestamp']  = Carbon::now()->toDateTimeString();
            $intaction_data['payment_method']   = $lead->payment_method;

            $interaction = $this->interaction->createInteraction($lead, $intaction_data, $interaction_type);

            $lead_update['interaction_no']          = $interaction['interaction_no'];
            $lead_update['payment_method']          = $interaction['payment_method'];
            $lead_update['deal_status']             = $interaction['deal_status'];
            $lead_update['s13_flag']                = $interaction['s13_flag'];
            $lead_update[$intaction_data['level']]  = 1;
            $lead_update[$intaction_data['level']. '_timestamp'] = $intaction_data['level_timestamp'];
        }

        // update;
        $lead->update($lead_update);

        $lead->lead_sale_status()->firstOrCreate([
            'crm_lead_number'   => $lead->crm_lead_number,
            'status'            => $system_status,
            'care_times'        => $care_times,
            'customer_care'     => $now_customer_care,
            'call_duration'     => $duration,
            'call_status'       => $status,
            'call_sub_status'   => $sub_status,
            'call_note'         => $call_note,
            'call_start'        => date('Y-m-d H:i:s', $call_start/1000),
            'call_end'          => date('Y-m-d H:i:s', $call_end/1000),
            'json_call'         => json_encode($request->all()),
            'update_status'     => 1
        ]);

//        \Log::info('Lead ID: ' . $lead->id);
//        \Log::info('customer_care: ' . $now_customer_care);
//        \Log::info('system_status: ' . $system_status);

        $lead->refresh();

        // gọi Event cập nhật sang CRM
        event( new UpdateLeadEvent($lead));

        return response()->json([
            'success' => true,
            'message' => 'Success'
        ]);
    }


    public function pivotData(Request $request){

        $date_start = $request->get('time_start', date('Y-m-d'));
        $date_end = $request->get('time_end', date('Y-m-d'));
        $view_mod = $request->get('view_mod', 'created');

        \Log::info('Lead API pivotData is Requesting date_start: ' . $date_start . ' - date_end: ' . $date_end . ' - view_mod: ' . $view_mod);

        $start_obj = new Carbon($date_start);
        if($start_obj->diffInDays($date_end) > 100){
            return response()->json([
                'error' => 'Thời gian bạn xem quá lớn, vui lòng chọn trong khoảng 3 tháng'
            ]);
        }

        $time_ranger = [
            $date_start . ' 00:00:00',
            $date_end . ' 23:23:59'
        ];

        $date_filter = 'created_at';
        if($view_mod == 'l9_time'){
            $date_filter = 'sale_success_updated';
        }

        $customer_cares = \DB::table('leads')
            ->select(\DB::raw('DISTINCT customer_care'))
            ->whereBetween($date_filter, $time_ranger)
            ->get();

        $dataReports = [];


        foreach ($customer_cares as $customer_care){
            $params = [
                'customer_care' => !empty($customer_care->customer_care) ? $customer_care->customer_care : null,
                $date_filter => [
                    'time_start' => $date_start,
                    'time_end' => $date_end,
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
                    'project' => 'HEO',
                    'customer_care' => !empty($customer_care->customer_care) ? $customer_care->customer_care : 'Unknow',
                    'mkt_code' => $mktCode,
                    'l1_count' => (int)$break_l1_count,
                    'l4_count' => (int)$break_l4_count,
                    'l7_count' => (int)$break_l7_count,
                    'l9_count' => (int)$break_l9_count,
                    'l94_count' => (int)$break_l94_count,
                    'l97_count' => (int)$break_l97_count,
                    'break_l94_per_l4' => $break_l94_per_l4,
                    'break_l97_per_l7' => $break_l97_per_l7,
                    'break_l9_per_l1' => $break_l9_per_l1,
                    'l4_revenue' => (int)Lead::l4_revenue($params),
                    'l7_revenue' => (int)Lead::l7_revenue($params),
                    'l9_revenue' => (int) Lead::l9_revenue($params)
                ];

            }

        }


        return response()->json($dataReports);
    }

    public function listing(Request $request){

        $date_start = $request->get('time_start', date('Y-m-d'));
        $date_end = $request->get('time_end', date('Y-m-d'));
        $view_mod = $request->get('view_mod', 'created');

        \Log::info('Lead API listing is Requesting date_start: ' . $date_start . ' - date_end: ' . $date_end . ' - view_mod: ' . $view_mod);

        $start_obj = new Carbon($date_start);
        if($start_obj->diffInDays($date_end) > 100){
            return response()->json([
                'error' => 'Thời gian bạn xem quá lớn, vui lòng chọn trong khoảng 3 tháng'
            ]);
        }

        $time_ranger = [
            $date_start . ' 00:00:00',
            $date_end . ' 23:23:59'
        ];

        $leads = Lead::where('status', Lead::STATUS_LIVE)
            ->where('is_duplicated', 0);

        $date_filter = 'created_at';
        if($view_mod == 'created'){
            $leads = $leads->whereBetween('created_at', $time_ranger);
        }
        elseif ($view_mod == 'l9_time'){
            $date_filter = 'sale_success_updated';
            $leads = $leads->whereBetween('sale_success_updated', $time_ranger);
        }


        $getData = [
            'id',
//            'name',
//            'email',
//            'phone',
//            'lead_price',
            'lead_origin_price',
//            'course_ids',
//            'crm_lead_source',
//            'crm_lead_source',
            'crm_lead_id',
            'customer_care',
//            'sale_status',
            'sale_t1_status',
            'sale_t1_duration',
            't1_status_updated',
            'sale_t2_status',
            'sale_t2_duration',
            't2_status_updated',
            'sale_t3_status',
            'sale_t3_duration',
            't3_status_updated',
            'in_cod',
            'in_cod_updated',
            'in_wait_cod',
            'in_cod_updated',
            'in_wait_tranfer_money',
            'in_wait_tranfer_money_at',
//            'in_wait_topup',
            'sale_success',
            'sale_success_updated',
            'in_cancel',
            'in_cancel_at',
            'final_status',
            'final_timestamp',
            'cod_status',
            'status',
            'customer_ship_fee',
            'cod_fee',
            'ship_fee',
            'tax_fee',
            'marketing_cost',
            'mkt_channel',
            'mkt_code',
            'mkt_person',
//            'mkt_type',
//            'mkt_landing_page',
//            'mkt_ad_group',
//            'mkt_ad_id',
            'cod_active',
            'cod_active_at',
            'time_created',
            'date_created',
            'hour_created',
//            'created_at',
//            'updated_at'
        ];

        $data = $leads->get($getData)->toArray();

        return response()->json($data);
    }

}