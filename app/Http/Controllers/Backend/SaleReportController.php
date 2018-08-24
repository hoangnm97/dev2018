<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2/5/2018
 * Time: 9:29 AM
 */
namespace App\Http\Controllers\Backend;


use App\Core\FaceBookSdk;
use App\Core\Report\SaleReport;
use App\Http\Controllers\Controller;
use App\Models\AdAccount;
use App\Models\AdAds;
use App\Models\FbAdStatistic;
use App\Models\Lead;
use App\Models\LeadCodStatus;
use App\Models\LeadInteraction;
use App\Models\LeadSaleStatus;
use Carbon\Carbon;
use Faker\Provider\DateTime;
use function foo\func;
use function GuzzleHttp\Psr7\build_query;
use Hamcrest\Core\JavaForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleReportController extends Controller
{

    private function __getConfigCompare(Request $request){
        $today = Carbon::today();
        $day_of_last_week = Carbon::today()->modify('-7 days');
        $day_of_last_month = Carbon::today()->modify('-30 days');

        $yesterday = Carbon::yesterday();
        $day_of_last_week_yesterday = Carbon::today()->modify('-8 days');
        $day_of_last_month_yesterday = Carbon::today()->modify('-31 days');


        // So sánh mặc định khi chưa chọn cái nào
        $compareConfigs['default_compare'] = [
            'compare_type' => 'day',
            'compare_from' =>  $today->format('Y-m-d'),
            'compare_to' => $yesterday->format('Y-m-d'),
        ];

        $dayConfigs = [
            [
                'label' => 'Hôm nay',
                'sub_label' => vietnames_day_of_week($today->dayOfWeek) . ', '. $today->format('d/m/Y'),
                'id' => 'from_day_'. $today->format('Y-m-d'),
                'default' => true,
                'value' => $today->format('Y-m-d'),
                'time_start' => $today->format('d/m/Y'),
                'time_end' => $today->format('d/m/Y'),
                'compare_list' => [
                    [
                        'label' => 'Hôm qua',
                        'sub_label' => vietnames_day_of_week($yesterday->dayOfWeek) . ', '. $yesterday->format('d/m/Y'),
                        'id' => 'to_day_' .$yesterday->format('Y-m-d'),
                        'value' => $yesterday->format('Y-m-d'),
                    ],
                    [
                        'label' => 'Cùng thứ tuần trước',
                        'sub_label' => vietnames_day_of_week($day_of_last_week->dayOfWeek) . ', '. $day_of_last_week->format('d/m/Y'),
                        'id' => 'to_day_' .$day_of_last_week->format('Y-m-d'),
                        'value' => $day_of_last_week->format('Y-m-d'),
                    ],
                    [
                        'label' => 'Cùng ngày tháng trước',
                        'sub_label' => vietnames_day_of_week($day_of_last_month->dayOfWeek) . ', '. $day_of_last_month->format('d/m/Y'),
                        'id' => 'to_day_' .$day_of_last_month->format('Y-m-d'),
                        'value' => $day_of_last_month->format('Y-m-d'),
                    ],
                ]
            ],
            [
                'label' => 'Hôm qua',
                'sub_label' => vietnames_day_of_week($yesterday->dayOfWeek) . ', '. $yesterday->format('d/m/Y'),
                'id' => 'to_day_' .$yesterday->format('Y-m-d'),
                'value' => $yesterday->format('Y-m-d'),
                'default' => false,
                'time_start' => $yesterday->format('d/m/Y'),
                'time_end' => $yesterday->format('d/m/Y'),
                'compare_list' => [
                    [
                        'label' => 'Cùng thứ tuần trước',
                        'sub_label' => vietnames_day_of_week($day_of_last_week_yesterday->dayOfWeek) . ', '. $day_of_last_week_yesterday->format('d/m/Y'),
                        'id' => 'to_day_' .$day_of_last_week_yesterday->format('Y-m-d'),
                        'value' => $day_of_last_week_yesterday->format('Y-m-d'),
                    ],
                    [
                        'label' => 'Cùng ngày tháng trước',
                        'sub_label' => vietnames_day_of_week($day_of_last_month_yesterday->dayOfWeek) . ', '. $day_of_last_month_yesterday->format('d/m/Y'),
                        'id' => 'to_day_' .$day_of_last_month_yesterday->format('Y-m-d'),
                        'value' => $day_of_last_month_yesterday->format('Y-m-d'),
                    ],
                ]
            ]
        ];

        $compareConfigs['days'] = $dayConfigs;

        $before_7day = $today->copy()->modify('-6 days');
        $day_of_berore_4w = $today->copy()->subWeek(4);


        $weekConfigs[] = [
            'label' => '7 Ngày qua',
            'sub_label' => $before_7day->format('d/m/Y') . ' đến ' . $today->format('d/m/Y'),
            'id' => 'from_week_1_'. $before_7day->format('Y-m-d') . '_' . $today->format('Y-m-d'),
            'default' => true,
            'value' => $before_7day->format('Y-m-d') . '_' . $today->format('Y-m-d'),
            'time_start' => $before_7day->format('d/m/Y'),
            'time_end' => $today->format('d/m/Y'),
            'compare_list' => [
                [
                    'label' => '7 ngày trước đó',
                    'sub_label' => $before_7day->copy()->modify('-7 days')->format('d/m/Y') . ' đến ' . $before_7day->copy()->modify('-1 days')->format('d/m/Y'),
                    'id' => 'to_week_1_' .$before_7day->copy()->modify('-7 days')->format('Y-m-d') . '_' . $before_7day->copy()->modify('-1 days')->format('Y-m-d'),
                    'value' => $before_7day->copy()->modify('-7 days')->format('Y-m-d') . '_' . $before_7day->copy()->modify('-1 days')->format('Y-m-d'),
                ],
                [
                    'label' => 'Trước đây 4 tuần',
                    'sub_label' => $day_of_berore_4w->copy()->modify('-6 days')->format('d/m/Y') . ' đến ' . $day_of_berore_4w->format('d/m/Y'),
                    'id' => 'to_week_2_' .$day_of_berore_4w->copy()->modify('-6 days')->format('Y-m-d') . '_' . $day_of_berore_4w->format('Y-m-d'),
                    'value' => $day_of_berore_4w->copy()->modify('-6 days')->format('Y-m-d') . '_' . $day_of_berore_4w->format('Y-m-d'),
                ],
            ]
        ];

        // tuần này
        $start_of_week = $today->copy()->startOfWeek();


        // tuần trước
        $pre_week_start = $start_of_week->copy()->subWeek();
        $pre_week_end = $pre_week_start->copy()->endOfWeek();

        // Trước đây 4 tuần
        $before_4w_w_start = $start_of_week->copy()->modify('-28 days');
        $before_4w_w_end = $before_4w_w_start->copy()->modify('+6 days');

        $weekConfigs[] = [
            'label' => 'Tuần này',
            'sub_label' => $start_of_week->format('d/m/Y') . ' đến ' . $today->format('d/m/Y'),
            'id' => 'from_week_2_'. $start_of_week->format('Y-m-d') . '_' . $today->format('Y-m-d'),
            'default' => false,
            'value' => $start_of_week->format('Y-m-d') . '_' . $today->format('Y-m-d'),
            'time_start' => $start_of_week->format('d/m/Y'),
            'time_end' => $today->format('d/m/Y'),
            'compare_list' => [
                [
                    'label' => 'Tuần trước',
                    'sub_label' => $pre_week_start->format('d/m/Y') . ' đến ' . $pre_week_end->format('d/m/Y'),
                    'id' => 'to_week_3_' .$pre_week_start->format('Y-m-d') . '_' . $pre_week_end->format('Y-m-d'),
                    'value' => $pre_week_start->format('Y-m-d') . '_' . $pre_week_end->format('Y-m-d'),
                ],
                [
                    'label' => 'Trước đây 4 tuần',
                    'sub_label' => $before_4w_w_start->format('d/m/Y') . ' đến ' . $before_4w_w_end->format('d/m/Y'),
                    'id' => 'to_week_4_' .$before_4w_w_start->format('Y-m-d') . '_' . $before_4w_w_end->format('Y-m-d'),
                    'value' => $before_4w_w_start->format('Y-m-d') . '_' . $before_4w_w_end->format('Y-m-d'),
                ],
            ]
        ];


        // tuần trước của tuần trước
        $before_of_last_w_end = $pre_week_start->copy()->modify('-1 days');
        $before_of_last_w_start = $before_of_last_w_end->copy()->modify('-6 days');

        // trước đây 4 tuần của tuần trước
        $before_last_w_4w_start = $pre_week_start->copy()->modify('-28 days');
        $before_last_w_4w_end = $before_last_w_4w_start->copy()->modify('+6 days');

        $weekConfigs[] = [
            'label' => 'Tuần trước',
            'sub_label' => $pre_week_start->format('d/m/Y') . ' đến ' . $pre_week_end->format('d/m/Y'),
            'id' => 'from_week_3_'. $pre_week_start->format('Y-m-d') . '_' . $pre_week_end->format('Y-m-d'),
            'default' => false,
            'value' => $pre_week_start->format('Y-m-d') . '_' . $pre_week_end->format('Y-m-d'),
            'time_start' => $pre_week_start->format('d/m/Y'),
            'time_end' => $pre_week_end->format('d/m/Y'),
            'compare_list' => [
                [
                    'label' => 'Tuần trước nữa',
                    'sub_label' => $before_of_last_w_start->format('d/m/Y') . ' đến ' . $before_of_last_w_end->format('d/m/Y'),
                    'id' => 'to_week_5_' .$before_of_last_w_start->format('Y-m-d') . '_' . $before_of_last_w_end->format('Y-m-d'),
                    'value' => $before_of_last_w_start->copy()->format('Y-m-d') . '_' . $before_of_last_w_end->format('Y-m-d'),
                ],
                [
                    'label' => 'Trước đây 4 tuần',
                    'sub_label' => $before_last_w_4w_start->format('d/m/Y') . ' đến ' . $before_last_w_4w_end->format('d/m/Y'),
                    'id' => 'to_week_6_' .$before_last_w_4w_start->format('Y-m-d') . '_' . $before_last_w_4w_end->format('Y-m-d'),
                    'value' => $before_last_w_4w_start->format('Y-m-d') . '_' . $before_last_w_4w_end->format('Y-m-d'),
                ],
            ]
        ];

        $compareConfigs['weeks'] = $weekConfigs;

        // tháng
        // 30 ngày qua
        $before_30d = $today->copy()->modify('-30 days');

        // 30 ngày trước
        $prev_30d_start = $before_30d->copy()->modify('-31 days');
        $prev_30d_end = $prev_30d_start->copy()->modify('+30 days');

        // năm trước
        $day_of_pre_year = $today->copy()->subYear();
        $day_of_pre_year_start = $day_of_pre_year->copy()->modify('-30 days');

        $monthConfigs[] = [
            'label' => '30 ngày qua',
            'sub_label' => $before_30d->format('d/m/Y') . ' đến ' . $today->format('d/m/Y'),
            'id' => 'from_week_'. $before_30d->format('Y-m-d') . '_' . $today->format('Y-m-d'),
            'default' => true,
            'value' => $before_30d->format('Y-m-d') . '_' . $today->format('Y-m-d'),
            'time_start' => $before_30d->format('d/m/Y'),
            'time_end' => $today->format('d/m/Y'),
            'compare_list' => [
                [
                    'label' => '30 ngày trước',
                    'sub_label' => $prev_30d_start->format('d/m/Y') . ' đến ' . $prev_30d_end->format('d/m/Y'),
                    'id' => 'to_week_' .$prev_30d_start->format('Y-m-d') . '_' . $prev_30d_end->format('Y-m-d'),
                    'value' => $prev_30d_start->copy()->format('Y-m-d') . '_' . $prev_30d_end->format('Y-m-d'),
                ],
                [
                    'label' => 'Năm trước',
                    'sub_label' => $day_of_pre_year->format('d/m/Y') . ' đến ' . $day_of_pre_year_start->format('d/m/Y'),
                    'id' => 'to_week_' .$day_of_pre_year->format('Y-m-d') . '_' . $day_of_pre_year_start->format('Y-m-d'),
                    'value' => $day_of_pre_year->format('Y-m-d') . '_' . $day_of_pre_year_start->format('Y-m-d'),
                ],
            ]
        ];

        // tháng này
        $this_month_start = new Carbon('first day of this month');
        $this_month_end = $this_month_start->copy()->endOfMonth();

        // tháng trước của tháng này
        $prev_month_of_this_month_start = $this_month_start->copy()->subMonth();
        $prev_month_of_this_month_end = $prev_month_of_this_month_start->copy()->endOfMonth();

        // năm trước của tháng này
        $month_of_prev_y_start = $this_month_start->copy()->subYear();
        $month_of_prev_y_end = $month_of_prev_y_start->copy()->endOfMonth();

        $monthConfigs[] = [
            'label' => 'Tháng này',
            'sub_label' => $this_month_start->format('d/m/Y') . ' đến ' . $this_month_end->format('d/m/Y'),
            'id' => 'from_week_'. $this_month_start->format('Y-m-d') . '_' . $this_month_end->format('Y-m-d'),
            'default' => false,
            'value' => $this_month_start->format('Y-m-d') . '_' . $this_month_end->format('Y-m-d'),
            'time_start' => $this_month_start->format('d/m/Y'),
            'time_end' => $this_month_end->format('d/m/Y'),
            'compare_list' => [
                [
                    'label' => 'Tháng trước',
                    'sub_label' => $prev_month_of_this_month_start->format('d/m/Y') . ' đến ' . $prev_month_of_this_month_end->format('d/m/Y'),
                    'id' => 'to_week_' .$prev_month_of_this_month_start->format('Y-m-d') . '_' . $prev_month_of_this_month_end->format('Y-m-d'),
                    'value' => $prev_month_of_this_month_start->format('Y-m-d') . '_' . $prev_month_of_this_month_end->format('Y-m-d'),
                ],
                [
                    'label' => 'Năm trước',
                    'sub_label' => $month_of_prev_y_start->format('d/m/Y') . ' đến ' . $month_of_prev_y_end->format('d/m/Y'),
                    'id' => 'to_week_' .$month_of_prev_y_start->format('Y-m-d') . '_' . $month_of_prev_y_end->format('Y-m-d'),
                    'value' => $month_of_prev_y_start->format('Y-m-d') . '_' . $month_of_prev_y_end->format('Y-m-d'),
                ],
            ]
        ];


        // tháng trước

        // tháng trước của tháng trước
        $pre_month_of_last_month_start = $prev_month_of_this_month_start->copy()->subMonth();
        $pre_month_of_last_month_end = $pre_month_of_last_month_start->copy()->endOfMonth();

        // năm trước của tháng trước
        $before_1year_of_last_month_start = $prev_month_of_this_month_start->copy()->subYear();
        $before_1year_of_last_month_end = $before_1year_of_last_month_start->copy()->endOfMonth();

        $monthConfigs[] = [
            'label' => 'Tháng trước',
            'sub_label' => $prev_month_of_this_month_start->format('d/m/Y') . ' đến ' . $prev_month_of_this_month_end->format('d/m/Y'),
            'id' => 'from_week_'. $prev_month_of_this_month_start->format('Y-m-d') . '_' . $prev_month_of_this_month_end->format('Y-m-d'),
            'default' => false,
            'value' => $prev_month_of_this_month_start->format('Y-m-d') . '_' . $prev_month_of_this_month_end->format('Y-m-d'),
            'time_start' => $prev_month_of_this_month_start->format('d/m/Y'),
            'time_end' => $prev_month_of_this_month_end->format('d/m/Y'),
            'compare_list' => [
                [
                    'label' => 'Tháng trước ',
                    'sub_label' => $pre_month_of_last_month_start->format('d/m/Y') . ' đến ' . $pre_month_of_last_month_end->format('d/m/Y'),
                    'id' => 'to_week_' .$pre_month_of_last_month_start->format('Y-m-d') . '_' . $pre_month_of_last_month_end->format('Y-m-d'),
                    'value' => $pre_month_of_last_month_start->format('Y-m-d') . '_' . $pre_month_of_last_month_end->format('Y-m-d'),
                ],
                [
                    'label' => 'Năm trước',
                    'sub_label' => $before_1year_of_last_month_start->format('d/m/Y') . ' đến ' . $before_1year_of_last_month_end->format('d/m/Y'),
                    'id' => 'to_week_' .$before_1year_of_last_month_start->format('Y-m-d') . '_' . $before_1year_of_last_month_end->format('Y-m-d'),
                    'value' => $before_1year_of_last_month_start->format('Y-m-d') . '_' . $before_1year_of_last_month_end->format('Y-m-d'),
                ],
            ]
        ];

        $compareConfigs['months'] = $monthConfigs;


        $date_start = $request->has('time_start') ? $request->get('time_start') : Carbon::today()->format('Y-m-d');
        $date_end = $request->has('time_end') ? $request->get('time_end') : Carbon::today()->format('Y-m-d');

        $custom_date_start = New Carbon($date_start);
        $custom_date_end = New Carbon($date_end);

        $diff_day = $custom_date_end->diffInDays($custom_date_start);

        $custom_date_before_start = Carbon::yesterday();
        $custom_date_before_end = $custom_date_before_start;
        if($diff_day > 0){
            $custom_date_before_end = $custom_date_start->copy()->modify('-1 days');
            $custom_date_before_start = $custom_date_before_end->copy()->modify('-' . $diff_day .' days');
        }


        $custom_date_pre_year_start = $custom_date_start->copy()->subYear();
        $custom_date_pre_year_end = $custom_date_end->copy()->subYear();


        $custom_configs[] = [
            'label' => 'Thời gian xem báo cáo',
            'sub_label' => $custom_date_start->format('d/m/Y') . ' đến ' . $custom_date_end->format('d/m/Y'),
            'id' => 'from_custom_'. $custom_date_start->format('Y-m-d') . '_' . $custom_date_end->format('Y-m-d'),
            'default' => true,
            'value' => $custom_date_start->format('Y-m-d') . '_' . $custom_date_end->format('Y-m-d'),
            'time_start' => $custom_date_start->format('d/m/Y'),
            'time_end' => $custom_date_end->format('d/m/Y'),
            'compare_list' => [
                [
                    'label' => 'Khoảng thời gian trước',
                    'sub_label' => $custom_date_before_start->format('d/m/Y') . ' đến ' . $custom_date_before_end->format('d/m/Y'),
                    'id' => 'to_custom_1_' .$custom_date_before_start->format('Y-m-d') . '_' . $custom_date_before_end->format('Y-m-d'),
                    'value' => $custom_date_before_start->format('Y-m-d') . '_' . $custom_date_before_end->format('Y-m-d'),
                ],
                [
                    'label' => 'Cùng thời gian năm trước',
                    'sub_label' => $custom_date_pre_year_start->format('d/m/Y') . ' đến ' . $custom_date_pre_year_end->format('d/m/Y'),
                    'id' => 'to_custom_2_' .$custom_date_pre_year_start->format('Y-m-d') . '_' . $custom_date_pre_year_end->format('Y-m-d'),
                    'value' => $custom_date_pre_year_start->format('Y-m-d') . '_' . $custom_date_pre_year_end->format('Y-m-d'),
                ],
            ]
        ];

        $compareConfigs['customs'] = $custom_configs;

        return $compareConfigs;
    }


    private function __getDataCompare(Request $request){

        $compare_type = $request->get('compare_type');
        $compare_to = $request->get('compare_to');

        $compare_to_start = $compare_to;
        $compare_to_end = $compare_to;

        if($compare_type != 'day'){
            $compare_to_arr = explode('_', $compare_to);
            $compare_to_start = $compare_to_arr[0];
            $compare_to_end = $compare_to_arr[1];
        }

        $ranger_time = [
            date('Y-m-d', strtotime($compare_to_start)) . ' 00:00:00',
            date('Y-m-d', strtotime($compare_to_end)) . ' 23:59:59'
        ];

        $dataCount = [];

        $query_sum_in_cod_l1 = "
            SELECT COUNT(*) as sum_in_cod FROM leads WHERE `status`=1
            AND final_status='Success'
            AND in_cod=1
            AND in_wait_tranfer_money=0
            AND sale_t1_status > 0
            AND sale_t2_status = 0
            AND sale_t3_status = 0
            AND t1_status_updated >='". $ranger_time[0] ."'
            AND t1_status_updated <='". $ranger_time[1] ."'
        ";

        $query_sum_in_cod_l2 = "
            SELECT COUNT(*) as sum_in_cod FROM leads WHERE `status`=1
            AND final_status='Success'
            AND in_cod=1
            AND in_wait_tranfer_money=0
            AND sale_t2_status > 0
            AND sale_t3_status = 0
            AND t2_status_updated >='". $ranger_time[0] ."'
            AND t2_status_updated <='". $ranger_time[1] ."'
        ";
        $query_sum_in_cod_l3 = "
            SELECT COUNT(*) as sum_in_cod FROM leads WHERE `status`=1
            AND final_status='Success'
            AND in_cod=1
            AND in_wait_tranfer_money=0
            AND sale_t3_status > 0
            AND t3_status_updated >='". $ranger_time[0] ."'
            AND t3_status_updated <='". $ranger_time[1] ."'
        ";

        $in_cod_count_l1 = DB::select($query_sum_in_cod_l1);
        $in_cod_count_l2 = DB::select($query_sum_in_cod_l2);
        $in_cod_count_l3 = DB::select($query_sum_in_cod_l3);
        $in_cod_count = $in_cod_count_l1[0]->sum_in_cod + $in_cod_count_l2[0]->sum_in_cod + $in_cod_count_l3[0]->sum_in_cod;
        $dataCount['cod_count'] = $in_cod_count;


        $query_sum_in_ck_l1 = "
            SELECT COUNT(*) as sum_in_ck FROM leads WHERE `status`=1
            AND final_status='Success'
            AND in_wait_tranfer_money=1
            AND in_cod=0
            AND sale_t1_status > 0
            AND sale_t2_status = 0
            AND sale_t3_status = 0
            AND t1_status_updated >='". $ranger_time[0] ."'
            AND t1_status_updated <='". $ranger_time[1] ."'
        ";

        $query_sum_in_ck_l2 = "
            SELECT COUNT(*) as sum_in_ck FROM leads WHERE `status`=1
            AND final_status='Success'
            AND in_wait_tranfer_money=1
            AND in_cod=0
            AND sale_t2_status > 0
            AND sale_t3_status = 0
            AND t2_status_updated >='". $ranger_time[0] ."'
            AND t2_status_updated <='". $ranger_time[1] ."'
        ";
        $query_sum_in_ck_l3 = "
            SELECT COUNT(*) as sum_in_ck FROM leads WHERE `status`=1
            AND final_status='Success'
            AND in_wait_tranfer_money=1
            AND in_cod=0
            AND sale_t3_status > 0
            AND t3_status_updated >='". $ranger_time[0] ."'
            AND t3_status_updated <='". $ranger_time[1] ."'
        ";

        $in_ck_count_l1 = DB::select($query_sum_in_ck_l1);
        $in_ck_count_l2 = DB::select($query_sum_in_ck_l2);
        $in_ck_count_l3 = DB::select($query_sum_in_ck_l3);
        $in_ck_count = $in_ck_count_l1[0]->sum_in_ck + $in_ck_count_l2[0]->sum_in_ck + $in_ck_count_l3[0]->sum_in_ck;
        $dataCount['in_ck_count'] = $in_ck_count;



        $finalEmpty = [];
        $finalSuccess = [];
        $finalNotSuccess = [];
        $finalOthers = [];
        $touchLeads = [];
        $call_datas = [];

        $customer_cares = \DB::table('leads')
            ->select(DB::raw('DISTINCT customer_care'))
            ->get();

        foreach ($customer_cares as $customer_care){

            $call_datas[$customer_care->customer_care]['call_count'] = LeadSaleStatus::where('customer_care', $customer_care->customer_care)
                ->whereBetween('created_at', $ranger_time)
                ->count();

            $touchLeads[$customer_care->customer_care]['l1_count'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->where('sale_t1_status', '>', 0)
                ->whereBetween('t1_status_updated', $ranger_time)
                ->where('sale_t2_status',  0)
                ->where('sale_t3_status',  0)
                ->count();

            $touchLeads[$customer_care->customer_care]['l2_count'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->where('sale_t2_status', '>', 0)
                ->whereBetween('t2_status_updated', $ranger_time)
                ->where('sale_t3_status',  0)
                ->count();
            $touchLeads[$customer_care->customer_care]['l3_count'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->where('sale_t3_status', '>', 0)
                ->whereBetween('t3_status_updated', $ranger_time)
                ->count();

            $finalEmpty[$customer_care->customer_care]['l1_count'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->where('sale_t1_status', '<>', 0)
                ->whereBetween('t1_status_updated', $ranger_time)
                ->where('sale_t2_status',  0)
                ->where('sale_t3_status',  0)
                ->where('final_status', null)
                ->count();
            $finalEmpty[$customer_care->customer_care]['l2_count'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->where('sale_t2_status', '<>', 0)
                ->whereBetween('t2_status_updated', $ranger_time)
                ->where('sale_t3_status',  0)
                ->where('final_status', null)
                ->count();
            $finalEmpty[$customer_care->customer_care]['l3_count'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->where('sale_t3_status', '<>', 0)
                ->whereBetween('t3_status_updated', $ranger_time)
                ->where('final_status', null)
                ->count();

            $finalSuccess[$customer_care->customer_care]['l1_count'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->whereBetween('t1_status_updated', $ranger_time)
                ->where('sale_t2_status',  0)
                ->where('sale_t3_status',  0)
                ->where('final_status', 'Success')
                ->count();

            $finalSuccess[$customer_care->customer_care]['l2_count'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->whereBetween('t2_status_updated', $ranger_time)
                ->where('sale_t3_status',  0)
                ->where('final_status', 'Success')
                ->count();

            $finalSuccess[$customer_care->customer_care]['l3_count'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->whereBetween('t3_status_updated', $ranger_time)
                ->where('final_status', 'Success')
                ->count();

            $finalNotSuccess[$customer_care->customer_care]['l1_count'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->whereBetween('t1_status_updated', $ranger_time)
                ->where('sale_t2_status',  0)
                ->where('sale_t3_status',  0)
                ->where('final_status', 'Not Success')
                ->count();

            $finalNotSuccess[$customer_care->customer_care]['l2_count'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->whereBetween('t2_status_updated', $ranger_time)
                ->where('sale_t3_status',  0)
                ->where('final_status', 'Not Success')
                ->count();

            $finalNotSuccess[$customer_care->customer_care]['l3_count'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->where('final_status', 'Not Success')
                ->whereBetween('t3_status_updated', $ranger_time)
                ->count();


            $finalOthers[$customer_care->customer_care]['l1_count'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->where('sale_t2_status',  0)
                ->where('sale_t3_status',  0)
                ->where('final_status', 'Other')
                ->whereBetween('t1_status_updated', $ranger_time)
                ->count();

            $finalOthers[$customer_care->customer_care]['l2_count'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->where('sale_t3_status',  0)
                ->where('final_status', 'Other')
                ->whereBetween('t2_status_updated', $ranger_time)
                ->count();

            $finalOthers[$customer_care->customer_care]['l3_count'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->where('final_status', 'Other')
                ->whereBetween('t3_status_updated', $ranger_time)
                ->count();
        }


        $total_call_count = 0;
        foreach ($call_datas as $call_data){
            $total_call_count += $call_data['call_count'];
        }
        $dataCount['call_count'] = $total_call_count;

        $touch_count = 0;
        foreach ($touchLeads as $touchLead){
            $touch_count += $touchLead['l1_count'] + $touchLead['l2_count'] + $touchLead['l3_count'];
        }
        $dataCount['touch_count'] = $touch_count;

        $need_actions_count = 0;
        foreach ($finalEmpty as $key => $item){
            $need_actions_count += $item['l1_count'] + $item['l2_count'] + $item['l3_count'];
            $finalEmpty[$key]['sum'] = $finalEmpty[$key]['l1_count'] + $finalEmpty[$key]['l2_count'] + $finalEmpty[$key]['l3_count'];
        }
        $dataCount['need_actions_count'] = $need_actions_count;

        $not_success_count = 0;
        foreach ($finalNotSuccess as $key => $notSuccess){
            $not_success_count += $notSuccess['l1_count'] + $notSuccess['l2_count'] + $notSuccess['l3_count'];
            $finalNotSuccess[$key]['sum'] = $finalNotSuccess[$key]['l1_count'] + $finalNotSuccess[$key]['l2_count'] + $finalNotSuccess[$key]['l3_count'];
        }
        $dataCount['not_success_count'] = $not_success_count;

        $final_other_count = 0;
        foreach ($finalOthers as $finalOther){
            $final_other_count += $finalOther['l1_count'] + $finalOther['l2_count'] + $finalOther['l3_count'];
        }
        $dataCount['final_other_count'] = $final_other_count;


        $success_count = 0;
        foreach ($finalSuccess as $key => $success){
            $success_count += $success['l1_count'] + $success['l2_count'] + $success['l3_count'];
            $tmp_sum = $finalSuccess[$key]['l1_count'] + $finalSuccess[$key]['l2_count'] + $finalSuccess[$key]['l3_count'];
            $finalSuccess[$key]['sum'] = $tmp_sum;
            $dvz = $tmp_sum + $finalEmpty[$key]['sum'] + $finalNotSuccess[$key]['sum'];
            if($tmp_sum > 0 && $dvz > 0){
                $finalSuccess[$key]['rate'] = round($tmp_sum / $dvz * 100, 2);
            } else {
                $finalSuccess[$key]['rate'] = 100;
            }
        }



        $dataCount['success_count'] = $success_count;

        $dataCount['success_rate'] = 0;
        if($dataCount['success_count'] > 0 && $dataCount['touch_count'] > 0){
            $dataCount['success_rate'] = round($dataCount['success_count'] / $dataCount['touch_count'] * 100, 2);
        }

        return $dataCount;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @desc: Báo cáo sale
     */
    public function general(Request $request){

        echo "<pre>"; print_r('Link not found!'); echo "</pre>"; die;

        $customer_cares = \DB::table('leads')
            ->select(DB::raw('DISTINCT customer_care'))
            ->get();

        $date_start = $request->get('time_start');
        $date_end = $request->get('time_end');

        $enable_compare = $request->get('enable_compare');

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


        $ranger_time = [
            date('Y-m-d', strtotime($date_start)) . ' 00:00:00',
            date('Y-m-d', strtotime($date_end)) . ' 23:59:59'
        ];

        $dataCount = [];

        $query_sum_in_cod_l1 = "
            SELECT COUNT(*) as sum_in_cod FROM leads WHERE `status`=1
            AND final_status='Success'
            AND in_cod=1
            AND in_wait_tranfer_money=0
            AND sale_t1_status > 0
            AND sale_t2_status = 0
            AND sale_t3_status = 0
            AND t1_status_updated >='". $ranger_time[0] ."'
            AND t1_status_updated <='". $ranger_time[1] ."'
        ";

        $query_sum_in_cod_l2 = "
            SELECT COUNT(*) as sum_in_cod FROM leads WHERE `status`=1
            AND final_status='Success'
            AND in_cod=1
            AND in_wait_tranfer_money=0
            AND sale_t2_status > 0
            AND sale_t3_status = 0
            AND t2_status_updated >='". $ranger_time[0] ."'
            AND t2_status_updated <='". $ranger_time[1] ."'
        ";
        $query_sum_in_cod_l3 = "
            SELECT COUNT(*) as sum_in_cod FROM leads WHERE `status`=1
            AND final_status='Success'
            AND in_cod=1
            AND in_wait_tranfer_money=0
            AND sale_t3_status > 0
            AND t3_status_updated >='". $ranger_time[0] ."'
            AND t3_status_updated <='". $ranger_time[1] ."'
        ";

        $in_cod_count_l1 = DB::select($query_sum_in_cod_l1);
        $in_cod_count_l2 = DB::select($query_sum_in_cod_l2);
        $in_cod_count_l3 = DB::select($query_sum_in_cod_l3);
        $in_cod_count = $in_cod_count_l1[0]->sum_in_cod + $in_cod_count_l2[0]->sum_in_cod + $in_cod_count_l3[0]->sum_in_cod;
        $dataCount['cod_count']['count'] = $in_cod_count;

        $query_sum_in_ck_l1 = "
            SELECT COUNT(*) as sum_in_ck FROM leads WHERE `status`=1
            AND final_status='Success'
            AND in_wait_tranfer_money=1
            AND in_cod=0
            AND sale_t1_status > 0
            AND sale_t2_status = 0
            AND sale_t3_status = 0
            AND t1_status_updated >='". $ranger_time[0] ."'
            AND t1_status_updated <='". $ranger_time[1] ."'
        ";

        $query_sum_in_ck_l2 = "
            SELECT COUNT(*) as sum_in_ck FROM leads WHERE `status`=1
            AND final_status='Success'
            AND in_wait_tranfer_money=1
            AND in_cod=0
            AND sale_t2_status > 0
            AND sale_t3_status = 0
            AND t2_status_updated >='". $ranger_time[0] ."'
            AND t2_status_updated <='". $ranger_time[1] ."'
        ";
        $query_sum_in_ck_l3 = "
            SELECT COUNT(*) as sum_in_ck FROM leads WHERE `status`=1
            AND final_status='Success'
            AND in_wait_tranfer_money=1
            AND in_cod=0
            AND sale_t3_status > 0
            AND t3_status_updated >='". $ranger_time[0] ."'
            AND t3_status_updated <='". $ranger_time[1] ."'
        ";

        $in_ck_count_l1 = DB::select($query_sum_in_ck_l1);
        $in_ck_count_l2 = DB::select($query_sum_in_ck_l2);
        $in_ck_count_l3 = DB::select($query_sum_in_ck_l3);
        $in_ck_count = $in_ck_count_l1[0]->sum_in_ck + $in_ck_count_l2[0]->sum_in_ck + $in_ck_count_l3[0]->sum_in_ck;
        $dataCount['in_ck_count']['count'] = $in_ck_count;


        $query_sum_in_ck_pre_l1 = "
            SELECT COUNT(*) as sum_in_ck FROM leads WHERE `status`=1
            AND final_status='Success'
            AND in_wait_tranfer_money=1
            AND in_cod=0
            AND sale_t1_status > 0
            AND sale_t2_status = 0
            AND sale_t3_status = 0
            AND sale_success = 0
            AND t1_status_updated >='". $ranger_time[0] ."'
            AND t1_status_updated <='". $ranger_time[1] ."'
        ";

        $query_sum_in_ck_pre_l2 = "
            SELECT COUNT(*) as sum_in_ck FROM leads WHERE `status`=1
            AND final_status='Success'
            AND in_wait_tranfer_money=1
            AND in_cod=0
            AND sale_t2_status > 0
            AND sale_t3_status = 0
            AND sale_success = 0
            AND t2_status_updated >='". $ranger_time[0] ."'
            AND t2_status_updated <='". $ranger_time[1] ."'
        ";


        $query_sum_in_ck_pre_l3 = "
            SELECT COUNT(*) as sum_in_ck FROM leads WHERE `status`=1
            AND final_status='Success'
            AND in_wait_tranfer_money=1
            AND in_cod=0
            AND sale_t3_status > 0
            AND sale_success = 0
            AND t3_status_updated >='". $ranger_time[0] ."'
            AND t3_status_updated <='". $ranger_time[1] ."'
        ";

        $in_ck_count_pre_l1 = DB::select($query_sum_in_ck_pre_l1);
        $in_ck_count_pre_l2 = DB::select($query_sum_in_ck_pre_l2);
        $in_ck_count_pre_l3 = DB::select($query_sum_in_ck_pre_l3);

        $in_ck_pre_count = $in_ck_count_pre_l1[0]->sum_in_ck + $in_ck_count_pre_l2[0]->sum_in_ck + $in_ck_count_pre_l3[0]->sum_in_ck;
        $dataCount['in_ck_count']['count_pre'] = $in_ck_pre_count;



        $finalEmpty = [];
        $finalSuccess = [];
        $finalNotSuccess = [];
        $finalOthers = [];
        $touchLeads = [];
        $call_datas = [];

        foreach ($customer_cares as $customer_care){

            // đếm tất cả các cuộc gọi trong thời gian xem báo cáo
            $touchLeads[$customer_care->customer_care]['call_count'] = LeadSaleStatus::where('customer_care', $customer_care->customer_care)
                ->whereBetween('created_at', $ranger_time)
                ->count();

            $touchLeads[$customer_care->customer_care]['l1_count'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->where('sale_t1_status', '>', 0)
                ->whereBetween('t1_status_updated', $ranger_time)
                ->where('sale_t2_status',  0)
                ->where('sale_t3_status',  0)
                ->count();

            // Đếm lead mới trong khoảng thời gian xem báo cáo của L1
            $touchLeads[$customer_care->customer_care]['l1_count_new'] = $touchLeads[$customer_care->customer_care]['l1_count'];

            $touchLeads[$customer_care->customer_care]['l2_count'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->where('sale_t2_status', '>', 0)
                ->whereBetween('t2_status_updated', $ranger_time)
                ->where('sale_t3_status',  0)
                ->count();
            $touchLeads[$customer_care->customer_care]['l2_count_new'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->where('sale_t2_status', '>', 0)
                ->whereBetween('t2_status_updated', $ranger_time)
                ->whereBetween('t1_status_updated', $ranger_time)
                ->where('sale_t3_status',  0)
                ->count();


            $touchLeads[$customer_care->customer_care]['l3_count'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->where('sale_t3_status', '>', 0)
                ->whereBetween('t3_status_updated', $ranger_time)
                ->count();
            $touchLeads[$customer_care->customer_care]['l3_count_new'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->where('sale_t3_status', '>', 0)
                ->whereBetween('t3_status_updated', $ranger_time)
                ->whereBetween('t1_status_updated', $ranger_time)
                ->count();


            $final_empty_l1_query = "SELECT count(*) as l1_count FROM `leads` WHERE customer_care='". $customer_care->customer_care ."'
                AND status = ". Lead::STATUS_LIVE ."
                AND sale_t1_status > 0
                AND sale_t2_status = 0
                AND sale_t3_status = 0
                AND t1_status_updated >='". $ranger_time[0] ."'
                AND t1_status_updated <='". $ranger_time[1] ."'
                AND (final_status ='' OR final_status is null)";

            $query_result = DB::select($final_empty_l1_query);
            $finalEmpty[$customer_care->customer_care]['l1_count'] = $query_result[0]->l1_count;

            $final_empty_l2_query = "SELECT count(*) as l2_count FROM `leads` WHERE customer_care='". $customer_care->customer_care ."'
                AND status = ". Lead::STATUS_LIVE ."
                AND sale_t2_status > 0
                AND sale_t3_status = 0
                AND t2_status_updated >='". $ranger_time[0] ."'
                AND t2_status_updated <='". $ranger_time[1] ."'
                AND (final_status ='' OR final_status is null)";

            $query_result = DB::select($final_empty_l2_query);
            $finalEmpty[$customer_care->customer_care]['l2_count'] = $query_result[0]->l2_count;

            $final_empty_l2_query = "SELECT count(*) as l3_count FROM `leads` WHERE customer_care='". $customer_care->customer_care ."'
                AND status = ". Lead::STATUS_LIVE ."
                AND sale_t3_status > 0
                AND t3_status_updated >='". $ranger_time[0] ."'
                AND t3_status_updated <='". $ranger_time[1] ."'
                AND (final_status ='' OR final_status is null)";

            $query_result = DB::select($final_empty_l2_query);
            $finalEmpty[$customer_care->customer_care]['l3_count'] = $query_result[0]->l3_count;

            $finalSuccess[$customer_care->customer_care]['l1_count'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->whereBetween('t1_status_updated', $ranger_time)
                ->where('sale_t2_status',  0)
                ->where('sale_t3_status',  0)
                ->where('final_status', 'Success')
                ->count();

            // contact tươi
            $finalSuccess[$customer_care->customer_care]['l1_fresh_count'] = $finalSuccess[$customer_care->customer_care]['l1_count'];

            $finalSuccess[$customer_care->customer_care]['l2_count'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->whereBetween('t2_status_updated', $ranger_time)
                ->where('sale_t3_status',  0)
                ->where('final_status', 'Success')
                ->count();

            $finalSuccess[$customer_care->customer_care]['l2_fresh_count'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->whereBetween('t2_status_updated', $ranger_time)
                ->whereBetween('t1_status_updated', $ranger_time)
                ->where('sale_t3_status',  0)
                ->where('final_status', 'Success')
                ->count();

            $finalSuccess[$customer_care->customer_care]['l3_count'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->whereBetween('t3_status_updated', $ranger_time)
                ->where('final_status', 'Success')
                ->count();

            $finalSuccess[$customer_care->customer_care]['l3_fresh_count'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->whereBetween('t3_status_updated', $ranger_time)
                ->whereBetween('t1_status_updated', $ranger_time)
                ->where('final_status', 'Success')
                ->count();

            $finalNotSuccess[$customer_care->customer_care]['l1_count'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->whereBetween('t1_status_updated', $ranger_time)
                ->where('sale_t2_status',  0)
                ->where('sale_t3_status',  0)
                ->where('final_status', 'Not Success')
                ->count();

            $finalNotSuccess[$customer_care->customer_care]['l2_count'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->whereBetween('t2_status_updated', $ranger_time)
                ->where('sale_t3_status',  0)
                ->where('final_status', 'Not Success')
                ->count();

            $finalNotSuccess[$customer_care->customer_care]['l3_count'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->where('final_status', 'Not Success')
                ->whereBetween('t3_status_updated', $ranger_time)
                ->count();


            $finalOthers[$customer_care->customer_care]['l1_count'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->where('sale_t2_status',  0)
                ->where('sale_t3_status',  0)
                ->where('final_status', 'Other')
                ->whereBetween('t1_status_updated', $ranger_time)
                ->count();

            $finalOthers[$customer_care->customer_care]['l2_count'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->where('sale_t3_status',  0)
                ->where('final_status', 'Other')
                ->whereBetween('t2_status_updated', $ranger_time)
                ->count();

            $finalOthers[$customer_care->customer_care]['l3_count'] = Lead::where('status', Lead::STATUS_LIVE)
                ->where('customer_care', $customer_care->customer_care)
                ->where('final_status', 'Other')
                ->whereBetween('t3_status_updated', $ranger_time)
                ->count();
        }


        $total_call_count = 0;


        $touch_count = 0;
        foreach ($touchLeads as $touchLead){
            $touch_count += $touchLead['l1_count'] + $touchLead['l2_count'] + $touchLead['l3_count'];
            $total_call_count += $touchLead['call_count'];
        }

        $dataCount['touch_count']['count'] = $touch_count;
        $dataCount['call_count']['count'] = $total_call_count;

        $need_actions_count = 0;
        foreach ($finalEmpty as $key => $item){
            $need_actions_count += $item['l1_count'] + $item['l2_count'] + $item['l3_count'];
            $finalEmpty[$key]['sum'] = $finalEmpty[$key]['l1_count'] + $finalEmpty[$key]['l2_count'] + $finalEmpty[$key]['l3_count'];
        }
        $dataCount['need_actions_count']['count'] = $need_actions_count;

        $not_success_count = 0;
        foreach ($finalNotSuccess as $key => $notSuccess){
            $not_success_count += $notSuccess['l1_count'] + $notSuccess['l2_count'] + $notSuccess['l3_count'];
            $finalNotSuccess[$key]['sum'] = $finalNotSuccess[$key]['l1_count'] + $finalNotSuccess[$key]['l2_count'] + $finalNotSuccess[$key]['l3_count'];
        }
        $dataCount['not_success_count']['count'] = $not_success_count;

        $final_other_count = 0;
        foreach ($finalOthers as $finalOther){
            $final_other_count += $finalOther['l1_count'] + $finalOther['l2_count'] + $finalOther['l3_count'];
        }

        $dataCount['final_other_count']['count'] = $final_other_count;


        $success_count = 0;
        foreach ($finalSuccess as $key => $success){
            $success_count += $success['l1_count'] + $success['l2_count'] + $success['l3_count'];

            $tmp_sum = $finalSuccess[$key]['l1_count'] + $finalSuccess[$key]['l2_count'] + $finalSuccess[$key]['l3_count'];
            $finalSuccess[$key]['sum'] = $tmp_sum;
            $dvz = $tmp_sum + $finalEmpty[$key]['sum'] + $finalNotSuccess[$key]['sum'];

            if($tmp_sum > 0 && $dvz > 0){
                $finalSuccess[$key]['rate'] = round($tmp_sum / $dvz * 100, 2);
            } else {
                $finalSuccess[$key]['rate'] = 100;
            }
        }


//        echo "<pre>"; print_r($finalSuccess); echo "</pre>"; die;

        $dataCount['success_count']['count'] = $success_count;

        $dataCount['success_rate']['rate'] = 0;

        $total_user_touch = $dataCount['success_count']['count'] + $dataCount['need_actions_count']['count'] + $dataCount['not_success_count']['count'];
        if($total_user_touch > 0){
            $dataCount['success_rate']['rate'] = round($dataCount['success_count']['count'] / $total_user_touch * 100, 2);
        } else {
            $dataCount['success_rate']['rate'] = 100;
        }

        $dataCount['other_count']['count'] = 5;



        $date_start_obj = new Carbon($date_start);

        $report_label = vietnames_day_of_week($date_start_obj->dayOfWeek) . ', '. $date_start_obj->format('d/m/Y');
        if($date_start != $date_end){
            $date_end_obj = new Carbon($date_end);
            $report_label = vietnames_day_of_week($date_start_obj->dayOfWeek) . ', '. $date_start_obj->format('d/m/Y') . ' đến ' . vietnames_day_of_week($date_end_obj->dayOfWeek) . ', '. $date_end_obj->format('d/m/Y');
        }

        if($enable_compare == 'yes'){

            $compare_type = $request->get('compare_type');
            $compare_to = $request->get('compare_to');

            $compare_to_start = $compare_to;
            $compare_to_end = $compare_to;

            if($compare_type != 'day'){
                $compare_to_arr = explode('_', $compare_to);
                $compare_to_start = $compare_to_arr[0];
                $compare_to_end = $compare_to_arr[1];
            }

            $compare_to_start_obj = new Carbon($compare_to_start);
            $label_to = vietnames_day_of_week($compare_to_start_obj->dayOfWeek) . ', '. $compare_to_start_obj->format('d/m/Y');

            if($compare_to_start != $compare_to_end){
                $compare_to_end_obj = new Carbon($compare_to_end);
                $label_to = vietnames_day_of_week($compare_to_start_obj->dayOfWeek) . ', '. $compare_to_start_obj->format('d/m/Y') . ' đến ' . vietnames_day_of_week($compare_to_end_obj->dayOfWeek) . ', '. $compare_to_end_obj->format('d/m/Y');
            }

            $report_label = $report_label . '<span class="caompare-to-label"> vs. '. $label_to .'</span>';




            $dataCompare = $this->__getDataCompare($request);



            $dataCount['cod_count']['compare_count'] = $dataCompare['cod_count'];
            $dataCount['cod_count']['change'] = $dataCount['cod_count']['count'] - $dataCount['cod_count']['compare_count'];
            $dataCount['cod_count']['change_percent'] = ($dataCount['cod_count']['count'] === 0) ?
                ($dataCount['cod_count']['compare_count'] * -1) * 100 :
                round(($dataCount['cod_count']['count'] - $dataCount['cod_count']['compare_count'])/$dataCount['cod_count']['count'] * 100, 2);

            $dataCount['in_ck_count']['compare_count'] = $dataCompare['in_ck_count'];
            $dataCount['in_ck_count']['change'] = $dataCount['in_ck_count']['count'] - $dataCount['in_ck_count']['compare_count'];
            $dataCount['in_ck_count']['change_percent'] = ($dataCount['in_ck_count']['count'] === 0) ?
                ($dataCount['in_ck_count']['compare_count'] * -1) * 100 :
                round(($dataCount['in_ck_count']['count'] - $dataCount['in_ck_count']['compare_count'])/$dataCount['in_ck_count']['count'] * 100, 2);

            $dataCount['touch_count']['compare_count'] = $dataCompare['touch_count'];
            $dataCount['touch_count']['change'] = $dataCount['touch_count']['count'] - $dataCount['touch_count']['compare_count'];
            $dataCount['touch_count']['change_percent'] = ($dataCount['touch_count']['count'] === 0) ?
                ($dataCount['touch_count']['compare_count'] * -1) * 100 :
                round(($dataCount['touch_count']['count'] - $dataCount['touch_count']['compare_count'])/$dataCount['touch_count']['count'] * 100, 2);

            $dataCount['call_count']['compare_count'] = $dataCompare['call_count'];
            $dataCount['call_count']['change'] = $dataCount['call_count']['count'] - $dataCount['call_count']['compare_count'];
            $dataCount['call_count']['change_percent'] = ($dataCount['call_count']['count'] === 0) ?
                ($dataCount['call_count']['compare_count'] * -1) * 100 :
                round(($dataCount['call_count']['count'] - $dataCount['call_count']['compare_count'])/$dataCount['call_count']['count'] * 100, 2);


            $dataCount['not_success_count']['compare_count'] = $dataCompare['not_success_count'];
            $dataCount['not_success_count']['change'] = $dataCount['not_success_count']['count'] - $dataCount['not_success_count']['compare_count'];
            $dataCount['not_success_count']['change_percent'] = ($dataCount['not_success_count']['count'] === 0) ?
                ($dataCount['not_success_count']['compare_count'] * -1) * 100 :
                round(($dataCount['not_success_count']['count'] - $dataCount['not_success_count']['compare_count'])/$dataCount['not_success_count']['count'] * 100, 2);

            $dataCount['final_other_count']['compare_count'] = $dataCompare['final_other_count'];
            $dataCount['final_other_count']['change'] = $dataCount['final_other_count']['count'] - $dataCount['final_other_count']['compare_count'];
            $dataCount['final_other_count']['change_percent'] = ($dataCount['final_other_count']['count'] === 0) ?
                ($dataCount['final_other_count']['compare_count'] * -1) * 100 :
                round(($dataCount['final_other_count']['count'] - $dataCount['final_other_count']['compare_count'])/$dataCount['final_other_count']['count'] * 100, 2);

            $dataCount['success_count']['compare_count'] = $dataCompare['success_count'];
            $dataCount['success_count']['change'] = $dataCount['success_count']['count'] - $dataCount['success_count']['compare_count'];
            $dataCount['success_count']['change_percent'] = ($dataCount['success_count']['count'] === 0) ?
                ($dataCount['success_count']['compare_count'] * -1) * 100 :
                round(($dataCount['success_count']['count'] - $dataCount['success_count']['compare_count'])/$dataCount['success_count']['count'] * 100, 2);

            // added success rate compare here!

//            $dataCount['other_count']['compare_count'] = $dataCompare['other_count'];
//            $dataCount['other_count']['change'] = $dataCount['other_count']['count'] - $dataCount['other_count']['compare_count'];
//            $dataCount['other_count']['change_percent'] = ($dataCount['other_count']['count'] === 0) ?
//                ($dataCount['other_count']['compare_count'] * -1) * 100 :
//                round(($dataCount['other_count']['count'] - $dataCount['other_count']['compare_count'])/$dataCount['other_count']['count'] * 100, 2);
        }

//        echo "<pre>"; print_r($dataCount); echo "</pre>"; die;

        javascript()->put([
            'get_data_popup_link' => route('backend.report.get_data_popup'),
            'get_break_data_popup_link' => route('backend.report.break_data_popup'),
            'get_ck_leads_link' => route('backend.report.get_ck_leads'),
            'get_ck_leads_break_link' => route('backend.report.get_ck_leads_break'),
            'compare_report_link' => route('backend.report.compare_report')
        ]);






        return view('backend.sale_report.general', [
            'report_label'      => $report_label,
            'enable_compare'    => $enable_compare,
            'dataCount'         => $dataCount,
            'finalEmpty'        => $finalEmpty,
            'finalSuccess'      => $finalSuccess,
            'finalNotSuccess'   => $finalNotSuccess,
            'finalOthers'       => $finalOthers,
            'touchLeads'        => $touchLeads
        ]);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     * @desc: Lấy config so sánh dữ liệu
     */
    public function compareReport(Request $request){
        $compare_configs = $this->__getConfigCompare($request);


        $res = [
            'success' => true,
            'default' => $compare_configs['default_compare'],
            'html' => view('backend.sale_report.includes.compare_popup', [
                'compare_configs' => $compare_configs
            ])->render()
        ];

        return response()->json($res);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @desc: Danh sách chuyển khoản
     */
    public function ckListModal(Request $request){
        $time_start = $request->get('time_start');
        $time_end = $request->get('time_end');
        $type = $request->get('type');
        $sale = $request->get('sale');
        $times = $request->get('times');


        $customer_cares = \DB::table('leads')
            ->select(DB::raw('DISTINCT customer_care'))
            ->get();


        $time_ranger = [
            $time_start . ' 00:00:00',
            $time_end . ' 23:59:58'
        ];
        $time_label = 'Từ ' . date('d/m/Y', strtotime($time_start)) . ' tới ' . date('d/m/Y', strtotime($time_end));

        $data = [];


        $ck_leads_l1_query = new Lead();
        $ck_leads_l1_query = $ck_leads_l1_query->where('status', Lead::STATUS_LIVE);
        $ck_leads_l1_query = $ck_leads_l1_query->where('in_wait_tranfer_money', 1);
        $ck_leads_l1_query = $ck_leads_l1_query->where('in_cod', 0);
        $ck_leads_l1_query = $ck_leads_l1_query->where('final_status', 'Success');
        $ck_leads_l1_query = $ck_leads_l1_query->where('sale_success', 0);
        $ck_leads_l1_query = $ck_leads_l1_query->where('sale_t1_status' , '>', 0);
        $ck_leads_l1_query = $ck_leads_l1_query->where('sale_t2_status',0);
        $ck_leads_l1_query = $ck_leads_l1_query->where('sale_t3_status',0);
        $ck_leads_l1_query = $ck_leads_l1_query->whereBetween('t1_status_updated', $time_ranger);
        $ck_leads_l1_query = $ck_leads_l1_query->orderBy('final_timestamp', 'ASC');
        $ck_leads_l1 = $ck_leads_l1_query->get();


        foreach ($ck_leads_l1 as $lead){
            foreach ($customer_cares as $customer_care){
                if($lead->customer_care == $customer_care->customer_care){
                    if(isset($customer_care->ck_count)){
                        $customer_care->ck_count = $customer_care->ck_count + 1;
                    } else {
                        $customer_care->ck_count = 1;
                    }
                }
            }
        }
        $data['ck_leads_l1'] = $ck_leads_l1;

        $ck_leads_l2_query = new Lead();
        $ck_leads_l2_query = $ck_leads_l2_query->where('status', Lead::STATUS_LIVE);
        $ck_leads_l2_query = $ck_leads_l2_query->where('in_wait_tranfer_money', 1);
        $ck_leads_l2_query = $ck_leads_l2_query->where('in_cod', 0);
        $ck_leads_l2_query = $ck_leads_l2_query->where('final_status', 'Success');
        $ck_leads_l2_query = $ck_leads_l2_query->where('sale_success', 0);
        $ck_leads_l2_query = $ck_leads_l2_query->where('sale_t2_status', '>', 0);
        $ck_leads_l2_query = $ck_leads_l2_query->where('sale_t3_status',0);
        $ck_leads_l2_query = $ck_leads_l2_query->whereBetween('t2_status_updated', $time_ranger);
        $ck_leads_l2_query = $ck_leads_l2_query->orderBy('final_timestamp', 'ASC');
        $ck_leads_l2 = $ck_leads_l2_query->get();

        foreach ($ck_leads_l2 as $lead){
            foreach ($customer_cares as $customer_care){
                if($lead->customer_care == $customer_care->customer_care){
                    if(isset($customer_care->ck_count)){
                        $customer_care->ck_count = $customer_care->ck_count + 1;
                    } else {
                        $customer_care->ck_count = 1;
                    }
                }
            }
        }
        $data['ck_leads_l2'] = $ck_leads_l2;

        $ck_leads_l3_query = new Lead();
        $ck_leads_l3_query = $ck_leads_l3_query->where('status', Lead::STATUS_LIVE);
        $ck_leads_l3_query = $ck_leads_l3_query->where('in_wait_tranfer_money', 1);
        $ck_leads_l3_query = $ck_leads_l3_query->where('in_cod', 0);
        $ck_leads_l3_query = $ck_leads_l3_query->where('final_status', 'Success');
        $ck_leads_l3_query = $ck_leads_l3_query->where('sale_success', 0);
        $ck_leads_l3_query = $ck_leads_l3_query->where('sale_t3_status','>', 0);
        $ck_leads_l3_query = $ck_leads_l3_query->whereBetween('t3_status_updated', $time_ranger);
        $ck_leads_l3_query = $ck_leads_l3_query->orderBy('final_timestamp', 'ASC');
        $ck_leads_l3 = $ck_leads_l3_query->get();


        foreach ($ck_leads_l3 as $lead){
            foreach ($customer_cares as $customer_care){
                if($lead->customer_care == $customer_care->customer_care){
                    if(isset($customer_care->ck_count)){
                        $customer_care->ck_count = $customer_care->ck_count + 1;
                    } else {
                        $customer_care->ck_count = 1;
                    }
                }
            }
        }

        $data['ck_leads_l3'] = $ck_leads_l3;

        $data['time_label'] = $time_label;

//        echo "<pre>"; print_r($customer_cares); echo "</pre>"; die;

        $data['customer_cares'] = $customer_cares;

        $res = [
            'success' => true,
            'html' => view('backend.sale_report.includes.ck_leads_content_modal', $data)->render()
        ];

        return response()->json($res);

    }

    public function bearkCkList(Request $request){
        $time_start = $request->get('time_start');
        $time_end = $request->get('time_end');
        $sale = $request->get('sale');

        $time_ranger = [
            $time_start . ' 00:00:00',
            $time_end . ' 23:59:58'
        ];


        $ck_leads_l1_query = new Lead();
        if($sale != 'ALL'){
            $ck_leads_l1_query = $ck_leads_l1_query->where('customer_care', $sale);
        }
        $ck_leads_l1_query = $ck_leads_l1_query->where('in_wait_tranfer_money', 1);
        $ck_leads_l1_query = $ck_leads_l1_query->where('in_cod', 0);
        $ck_leads_l1_query = $ck_leads_l1_query->where('final_status', 'Success');
        $ck_leads_l1_query = $ck_leads_l1_query->where('sale_success', 0);
        $ck_leads_l1_query = $ck_leads_l1_query->where('sale_t1_status' , '>', 0);
        $ck_leads_l1_query = $ck_leads_l1_query->where('sale_t2_status',0);
        $ck_leads_l1_query = $ck_leads_l1_query->where('sale_t3_status',0);
        $ck_leads_l1_query = $ck_leads_l1_query->whereBetween('t1_status_updated', $time_ranger);
        $ck_leads_l1_query = $ck_leads_l1_query->orderBy('final_timestamp', 'ASC');
        $ck_leads_l1 = $ck_leads_l1_query->get();

        $data['ck_leads_l1'] = $ck_leads_l1;

        $ck_leads_l2_query = new Lead();
        if($sale != 'ALL'){
            $ck_leads_l2_query = $ck_leads_l2_query->where('customer_care', $sale);
        }
        $ck_leads_l2_query = $ck_leads_l2_query->where('in_wait_tranfer_money', 1);
        $ck_leads_l2_query = $ck_leads_l2_query->where('in_cod', 0);
        $ck_leads_l2_query = $ck_leads_l2_query->where('final_status', 'Success');
        $ck_leads_l2_query = $ck_leads_l2_query->where('sale_success', 0);
        $ck_leads_l2_query = $ck_leads_l2_query->where('sale_t2_status', '>', 0);
        $ck_leads_l2_query = $ck_leads_l2_query->where('sale_t3_status',0);
        $ck_leads_l2_query = $ck_leads_l2_query->whereBetween('t2_status_updated', $time_ranger);
        $ck_leads_l2_query = $ck_leads_l2_query->orderBy('final_timestamp', 'ASC');
        $ck_leads_l2 = $ck_leads_l2_query->get();


        $data['ck_leads_l2'] = $ck_leads_l2;

        $ck_leads_l3_query = new Lead();
        if($sale != 'ALL'){
            $ck_leads_l3_query = $ck_leads_l3_query->where('customer_care', $sale);
        }

        $ck_leads_l3_query = $ck_leads_l3_query->where('in_wait_tranfer_money', 1);
        $ck_leads_l3_query = $ck_leads_l3_query->where('in_cod', 0);
        $ck_leads_l3_query = $ck_leads_l3_query->where('final_status', 'Success');
        $ck_leads_l3_query = $ck_leads_l3_query->where('sale_success', 0);
        $ck_leads_l3_query = $ck_leads_l3_query->where('sale_t3_status','>', 0);
        $ck_leads_l3_query = $ck_leads_l3_query->whereBetween('t3_status_updated', $time_ranger);
        $ck_leads_l3_query = $ck_leads_l3_query->orderBy('final_timestamp', 'ASC');
        $ck_leads_l3 = $ck_leads_l3_query->get();

        $data['ck_leads_l3'] = $ck_leads_l3;


        $res = [
            'success' => true,
            'html' => view('backend.sale_report.includes.ck_leads_break_table', $data)->render()
        ];
        return response()->json($res);

    }


    public function getPopupModal(Request $request){

        $time_start = $request->get('time_start');
        $time_end = $request->get('time_end');
        $type = $request->get('type');
        $sale = $request->get('sale');
        $times = $request->get('times');

        $time_ranger = [
            $time_start . ' 00:00:00',
            $time_end . ' 23:59:58'
        ];
        $time_label = 'Từ ' . date('d/m/Y', strtotime($time_start)) . ' tới ' . date('d/m/Y', strtotime($time_end));

        $query = new Lead();
        $query = $query->where('customer_care', $sale);
        $query = $query->where('status', Lead::STATUS_LIVE);
        $times_label = '';
        if($times === 'l1_count'){
            $times_label = 'L1';
            $query = $query->where('sale_t1_status' , '>', 0);
            $query = $query->where('sale_t2_status',0);
            $query = $query->where('sale_t3_status',0);
            $query = $query->whereBetween('t1_status_updated', $time_ranger);

        }
        if($times === 'l2_count'){
            $times_label = 'L2';
            $query = $query->where('sale_t2_status' , '>', 0);
            $query = $query->where('sale_t3_status',0);
            $query = $query->whereBetween('t2_status_updated', $time_ranger);
        }
        if($times === 'l3_count'){
            $times_label = 'L3';
            $query = $query->where('sale_t3_status' , '>', 0);
            $query = $query->whereBetween('t3_status_updated', $time_ranger);
        }

        if($type == 'need_actions'){
            $query = $query->where(function($q){
                $q->where('final_status', null)
                    ->orWhere('final_status', '');
            });
        }
        if($type == 'not_success'){
            $query = $query->where('final_status', Lead::FINAL_NOT_SUCCESS);
        }
        if($type == 'other'){
            $query = $query->where('final_status', Lead::FINAL_OTHER);
        }


        $query = $query->orderBy('updated_at', 'ASC');
        $leads = $query->get();

        $sumary_reports = [
            'KLL' => [
                'label' => 'Không liên lạc được',
                'count' => 0
            ],
            'KNM' => [
                'label' => 'Không nghe máy',
                'count' => 0
            ],
            'SNT' => [
                'label' => 'Suy nghĩ thêm',
                'count' => 0
            ],
            'GLS' => [
                'label' => 'Gọi lại sau',
                'count' => 0
            ],
            'SSO' => [
                'label' => 'Sai số',
                'count' => 0
            ],
            'TSO' => [
                'label' => 'Trùng số',
                'count' => 0
            ],
            'KMU' => [
                'label' => 'Không mua',
                'count' => 0
            ],
        ];

        foreach ($sumary_reports as $key => $value){
            if($times === 'l1_count'){
                $sumary_reports[$key]['key'] = LeadSaleStatus::mapCrmL1Status($key);
            }
            if($times === 'l2_count'){
                $sumary_reports[$key]['key'] = LeadSaleStatus::mapCrmL2Status($key);
            }
            if($times === 'l3_count'){
                $sumary_reports[$key]['key'] = LeadSaleStatus::mapCrmL3Status($key);
            }
        }

        foreach ($leads as $lead){
            if($times === 'l1_count'){
                if($lead->sale_t1_status == LeadSaleStatus::T1_KLL_STATUS){
                    $sumary_reports['KLL']['count'] += 1;
                }
                if($lead->sale_t1_status == LeadSaleStatus::T1_KNM_STATUS){
                    $sumary_reports['KNM']['count'] += 1;
                }
                if($lead->sale_t1_status == LeadSaleStatus::T1_SNT_STATUS){
                    $sumary_reports['SNT']['count'] += 1;
                }
                if($lead->sale_t1_status == LeadSaleStatus::T1_GLS_STATUS){
                    $sumary_reports['GLS']['count'] += 1;
                }
                if($lead->sale_t1_status == LeadSaleStatus::T1_SSO_STATUS){
                    $sumary_reports['SSO']['count'] += 1;
                }
                if($lead->sale_t1_status == LeadSaleStatus::T1_TSO_STATUS){
                    $sumary_reports['TSO']['count'] += 1;
                }
                if($lead->sale_t1_status == LeadSaleStatus::T1_KMU_STATUS){
                    $sumary_reports['KMU']['count'] += 1;
                }
            }
            if($times === 'l2_count'){
                if($lead->sale_t2_status == LeadSaleStatus::T2_KLL_STATUS){
                    $sumary_reports['KLL']['count'] += 1;
                }
                if($lead->sale_t2_status == LeadSaleStatus::T2_KNM_STATUS){
                    $sumary_reports['KNM']['count'] += 1;
                }
                if($lead->sale_t2_status == LeadSaleStatus::T2_SNT_STATUS){
                    $sumary_reports['SNT']['count'] += 1;
                }
                if($lead->sale_t2_status == LeadSaleStatus::T2_GLS_STATUS){
                    $sumary_reports['GLS']['count'] += 1;
                }
                if($lead->sale_t2_status == LeadSaleStatus::T2_SSO_STATUS){
                    $sumary_reports['SSO']['count'] += 1;
                }
                if($lead->sale_t2_status == LeadSaleStatus::T2_TSO_STATUS){
                    $sumary_reports['TSO']['count'] += 1;
                }
                if($lead->sale_t2_status == LeadSaleStatus::T2_KMU_STATUS){
                    $sumary_reports['KMU']['count'] += 1;
                }
            }
            if($times === 'l3_count'){
                if($lead->sale_t3_status == LeadSaleStatus::T3_KLL_STATUS){
                    $sumary_reports['KLL']['count'] += 1;
                }
                if($lead->sale_t3_status == LeadSaleStatus::T3_KNM_STATUS){
                    $sumary_reports['KNM']['count'] += 1;
                }
                if($lead->sale_t3_status == LeadSaleStatus::T3_SNT_STATUS){
                    $sumary_reports['SNT']['count'] += 1;
                }
                if($lead->sale_t3_status == LeadSaleStatus::T3_GLS_STATUS){
                    $sumary_reports['GLS']['count'] += 1;
                }
                if($lead->sale_t3_status == LeadSaleStatus::T3_SSO_STATUS){
                    $sumary_reports['SSO']['count'] += 1;
                }
                if($lead->sale_t3_status == LeadSaleStatus::T3_TSO_STATUS){
                    $sumary_reports['TSO']['count'] += 1;
                }
                if($lead->sale_t3_status == LeadSaleStatus::T3_KMU_STATUS){
                    $sumary_reports['KMU']['count'] += 1;
                }
            }

        }

        $res = [
            'success' => true,
            'html' => view('backend.sale_report.includes.render_popup', [
                'leads' => $leads,
                'time_label' => $time_label,
                'times_label' => $times_label,
                'sale' => $sale,
                'type' => $type,
                'times' => $times,
                'sumary_reports' => $sumary_reports
            ])->render()
        ];

        return response()->json($res);

    }

    public function breakStatusTable(Request $request){
        $time_start = $request->get('time_start');
        $time_end = $request->get('time_end');
        $type = $request->get('type');
        $sale = $request->get('sale');
        $times = $request->get('times');
        $times_status = $request->get('times_status');

        $time_ranger = [
            $time_start . ' 00:00:00',
            $time_end . ' 23:59:58'
        ];

        $query = new Lead();
        $query = $query->where('customer_care', $sale);
        $query = $query->where('status', Lead::STATUS_LIVE);

        if($times === 'l1_count'){
            if($times_status == 'ALL'){
                $query = $query->where('sale_t1_status' , '>', 0);
            } else {
                $query = $query->where('sale_t1_status' , $times_status);
            }
            $query = $query->where('sale_t2_status',0);
            $query = $query->where('sale_t3_status',0);
            $query = $query->whereBetween('t1_status_updated', $time_ranger);

        }
        if($times === 'l2_count'){
            if($times_status == 'ALL'){
                $query = $query->where('sale_t2_status' , '>', 0);
            } else {
                $query = $query->where('sale_t2_status' , $times_status);
            }
            $query = $query->where('sale_t3_status',0);
            $query = $query->whereBetween('t2_status_updated', $time_ranger);
        }
        if($times === 'l3_count'){
            if($times_status == 'ALL'){
                $query = $query->where('sale_t3_status' , '>', 0);
            } else {
                $query = $query->where('sale_t3_status' , $times_status);
            }
            $query = $query->whereBetween('t3_status_updated', $time_ranger);
        }

        if($type == 'need_actions'){
            $query = $query->where(function($q){
                $q->where('final_status', null)
                    ->orWhere('final_status', '');
            });
        }
        if($type == 'not_success'){
            $query = $query->where('final_status', Lead::FINAL_NOT_SUCCESS);
        }

        if($type == 'other'){
            $query = $query->where('final_status', Lead::FINAL_OTHER);
        }

        $query = $query->orderBy('updated_at', 'ASC');
        $leads = $query->get();

        $res = [
            'success' => true,
            'html' => view('backend.sale_report.includes.break_data_popup', [
                'leads' => $leads
            ])->render()
        ];

        return response()->json($res);

    }


    public function dailyReport(Request $request){
        $today_start = date('Y-m-d') . ' 00:00:00';
        $today_end = date('Y-m-d') . ' 23:59:59';

        $results = DB::table('leads')
//            ->select(DB::raw('DISTINCT customer_care'))
            ->where('date_created', date('Y-m-d'))
//            ->whereBetween('t1_status_updated', [$today_start, $today_end])
//            ->orWhereBetween('t2_status_updated', [$today_start, $today_end])
//            ->orWhereBetween('t3_status_updated', [$today_start, $today_end])
//            ->groupBy('customer_care')
            ->get();

        $reports = [];

        foreach ($results as $result){

            $key = $result->customer_care;
            if(empty($key))
                $key = 'EMPTY';

            // total
            if( isset($reports[$key]['lead_count']) ){
                $reports[$key]['lead_count'] += 1;
            } else {
                $reports[$key]['lead_count'] = 1;
            }

            if($result->in_cod === 0 && $result->in_wait_tranfer_money === 0){



            } else {
                $reports[$key]['lead_success_count'] = isset($reports[$key]['lead_success_count']) ? $reports[$key]['lead_success_count'] + 1: 1;

                if($result->sale_t1_status != 0 && $result->sale_t2_status === 0){
                    $reports[$key]['l1_count'] = isset($reports[$key]['l1_count']) ? $reports[$key]['l1_count'] + 1: 1;
                }

                if($result->sale_t2_status != 0){
                    $reports[$key]['l2_count'] = isset($reports[$key]['l2_count']) ? $reports[$key]['l2_count'] + 1: 1;
                }

                if($result->sale_t3_status != 0){
                    $reports[$key]['l3_count'] = isset($reports[$key]['l3_count']) ? $reports[$key]['l3_count'] + 1: 1;
                }

            }

        }

        echo "<pre>"; print_r($reports); echo "</pre>"; die;

    }


    public function successTimeLine(Request $request){

        $time_start = !empty($request->get('time_start')) ? $request->get('time_start'): null;
        $time_end = !empty($request->get('time_end')) ? $request->get('time_end') : date('Y-m-d', time());

        if(empty($time_start)){
            $time_end_obj = new \DateTime($time_end);

            $time_start = $time_end_obj->modify('-7 days')->format('Y-m-d');
        }


        $day_start = new \DateTime($time_start);

        $day_count = round((strtotime($time_end) - strtotime($time_start)) / (60 * 60 * 24));

        if ($day_count == 0)
            $day_count = 1;

        $reportDatas = [];

        for ($i = 0; $i < $day_count; $i++){

            $log_day = $day_start ->modify('+1' . ' day')->format('Y-m-d');

            $lead_l0 = DB::table('leads')
                ->select(DB::raw('count(*) as total'))
                ->where('date_created', $log_day)
                ->first();
            $reportDatas[$log_day]['lead_l0_total'] = $lead_l0->total;

            $lead_success = DB::table('leads')
                ->select(DB::raw('count(*) as total'))
                ->where('date_created', $log_day)
                ->where('cod_status', 'Success')
                ->first();
            $reportDatas[$log_day]['lead_success_count'] = $lead_success->total;

            $data_time_line = [];


            $time_line_start = new \DateTime($log_day);
            for ( $j = 1; $j <= 10; $j++){

                $status_time_day_obj = $time_line_start->modify('+1 day');

                $status_time_day = $status_time_day_obj->format('Y-m-d');

//                echo "<pre>"; print_r($status_time_day); echo "</pre>";

                $cod_success = DB::table('lead_cod_statuses')
                    ->select(DB::raw('count(*) as total'))
                    ->where('status', 'Success')
                    ->where('lead_date_created', $log_day)
                    ->whereBetween('created_at', [$status_time_day . ' 00:00:00', $status_time_day . ' 23:23:59'])
//                    ->groupBy('lead_date_created')
                    ->first();


                $data_time_line[$status_time_day] = $cod_success->total;
            }


            $reportDatas[$log_day]['time_line_datas'] = $data_time_line;

        }

        return view('backend.sale_report.success_time_line', compact('reportDatas'));
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @desc: Báo cáo L9 performance
     */
    public function sale_l9_performance(Request $request){

        $date_start = $request->get('time_start');
        $date_end = $request->get('time_end');
        $date_filter = $request->get('date_filter', 'created_at');

        $enable_compare = $request->get('enable_compare');

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

        $customer_cares = \DB::table('leads')
            ->select(DB::raw('DISTINCT customer_care'))
            ->whereBetween($date_filter, [$time_start, $time_end])
            ->get();

        $dataReports = [];

        $param_heo = [
            $date_filter => [
                'time_start' => $time_start,
                'time_end' => $time_end,
            ]
        ];

        $heo_l1_count = Lead::l1Count($param_heo);
        $heo_l4_count = Lead::l4Count($param_heo);
        $heo_l7_count = Lead::l7Count($param_heo);
        $heo_l94_count = Lead::l94Count($param_heo);
        $heo_l97_count = Lead::l97Count($param_heo);
        $heo_l9_count = Lead::l9Count($param_heo);

        $heoSumary = [
            'l1_count' => $heo_l1_count,
            'l4_count' => $heo_l4_count,
            'l7_count' => $heo_l7_count,
            'l9_count' => $heo_l9_count,
            'l94_count' => $heo_l94_count,
            'l97_count' => $heo_l97_count,
            'l94_per_l4' => ($heo_l4_count > 0) ? round(($heo_l94_count/$heo_l4_count) * 100, 2) : 0,
            'l97_per_l7' => ($heo_l7_count > 0) ? round(($heo_l97_count/$heo_l7_count) * 100, 2) : 0,
            'l9_per_l1' => ($heo_l7_count > 0) ? round(($heo_l9_count/$heo_l1_count) * 100, 2) : 0,
            'l4_revenue' => Lead::l4_revenue($param_heo),
            'l7_revenue' => Lead::l7_revenue($param_heo),
            'l9_revenue' => Lead::l9_revenue($param_heo),
        ];

        foreach ($customer_cares as $customer_care){
            $params = [
                'customer_care' => !empty($customer_care->customer_care) ? $customer_care->customer_care : null,
                $date_filter => [
                    'time_start' => $time_start,
                    'time_end' => $time_end,
                ]
            ];


            $mktCodes = Lead::getMktCodes($params);

            $sumary_l1_count = Lead::l1Count($params);
            $sumary_l4_count = Lead::l4Count($params);
            $sumary_l7_count = Lead::l7Count($params);
//            $sumary_l9_sale_count = Lead::l7Count($params);
            $sumary_l94_count = Lead::l94Count($params);
            $sumary_l97_count = Lead::l97Count($params);
            $sumary_l9_count = Lead::l9Count($params);

            $dataReports[$customer_care->customer_care]['sumary']['l1_count'] = $sumary_l1_count;
            $dataReports[$customer_care->customer_care]['sumary']['l4_count'] = $sumary_l4_count;
            $dataReports[$customer_care->customer_care]['sumary']['l7_count'] = $sumary_l7_count;
            $dataReports[$customer_care->customer_care]['sumary']['l9_count'] = $sumary_l9_count;
            $dataReports[$customer_care->customer_care]['sumary']['l94_count'] = $sumary_l94_count;
            $dataReports[$customer_care->customer_care]['sumary']['l97_count'] = $sumary_l97_count;

            $dataReports[$customer_care->customer_care]['sumary']['l94_per_l4'] = ($sumary_l4_count > 0) ? round(($sumary_l94_count/$sumary_l4_count)*100, 2) : 0;
            $dataReports[$customer_care->customer_care]['sumary']['l97_per_l7'] = ($sumary_l7_count > 0) ? round(($sumary_l97_count/$sumary_l7_count)*100, 2) : 0;
            $dataReports[$customer_care->customer_care]['sumary']['l9_per_l1'] = ($sumary_l1_count > 0) ? round(($sumary_l9_count/$sumary_l1_count)*100, 2) : 0;

            $dataReports[$customer_care->customer_care]['sumary']['l4_revenue'] = Lead::l4_revenue($params);
            $dataReports[$customer_care->customer_care]['sumary']['l7_revenue'] = Lead::l7_revenue($params);
            $dataReports[$customer_care->customer_care]['sumary']['l9_revenue'] = Lead::l9_revenue($params);

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

                $dataReports[$customer_care->customer_care]['mkt_codes'][$mktCode]['l1_count'] = $break_l1_count;
                $dataReports[$customer_care->customer_care]['mkt_codes'][$mktCode]['l4_count'] = $break_l4_count;
                $dataReports[$customer_care->customer_care]['mkt_codes'][$mktCode]['l7_count'] = $break_l7_count;
                $dataReports[$customer_care->customer_care]['mkt_codes'][$mktCode]['l9_count'] = $break_l9_count;
                $dataReports[$customer_care->customer_care]['mkt_codes'][$mktCode]['l94_count'] = $break_l94_count;
                $dataReports[$customer_care->customer_care]['mkt_codes'][$mktCode]['l97_count'] = $break_l97_count;

                $dataReports[$customer_care->customer_care]['mkt_codes'][$mktCode]['break_l94_per_l4'] = $break_l94_per_l4;
                $dataReports[$customer_care->customer_care]['mkt_codes'][$mktCode]['break_l97_per_l7'] = $break_l97_per_l7;
                $dataReports[$customer_care->customer_care]['mkt_codes'][$mktCode]['break_l9_per_l1'] = $break_l9_per_l1;

                $dataReports[$customer_care->customer_care]['mkt_codes'][$mktCode]['l4_revenue'] = Lead::l4_revenue($params);
                $dataReports[$customer_care->customer_care]['mkt_codes'][$mktCode]['l7_revenue'] = Lead::l7_revenue($params);
                $dataReports[$customer_care->customer_care]['mkt_codes'][$mktCode]['l9_revenue'] = Lead::l9_revenue($params);
            }

        }

//        echo "<pre>"; print_r($dataReports); echo "</pre>";die;
        $date_start_obj = new Carbon($date_start);
        $report_label = vietnames_day_of_week($date_start_obj->dayOfWeek) . ', '. $date_start_obj->format('d/m/Y');
        if($date_start != $date_end){
            $date_end_obj = new Carbon($date_end);
            $report_label = vietnames_day_of_week($date_start_obj->dayOfWeek) . ', '. $date_start_obj->format('d/m/Y') . ' đến ' . vietnames_day_of_week($date_end_obj->dayOfWeek) . ', '. $date_end_obj->format('d/m/Y');
        }

        $date_filters = [
            'created_at' => 'Created Time',
            'sale_success_updated' => 'L9 Time'
        ];

//        echo "<pre>"; print_r($dataReports); echo "</pre>"; die;

        return view('backend.sale_report.sale_l9_performance', [
            'report_label'  => $report_label,
            'heoSumary'     => $heoSumary,
            'dataReports'   => $dataReports,
            'date_filters'  => $date_filters
        ]);
    }


    public function l9_performance_pivot(Request $request){


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

        $customer_cares = \DB::table('leads')
            ->select(DB::raw('DISTINCT customer_care'))
            ->whereBetween($date_filter, [$time_start, $time_end])
            ->get();

        $dataReports = [];


        foreach ($customer_cares as $customer_care){
            $params = [
                'customer_care' => !empty($customer_care->customer_care) ? $customer_care->customer_care : null,
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
                    'l4_revenue' => Lead::l4_revenue($params),
                    'l7_revenue' => Lead::l7_revenue($params),
                    'l9_revenue' => Lead::l9_revenue($params)
                ];

            }

        }

        javascript()->put([
            'report_pivot_dataset' => json_encode($dataReports),
            'time_start' => date('Y-m-d', strtotime($date_start)),
            'time_end' => date('Y-m-d', strtotime($date_end))
        ]);

        $date_filters = [
            'created_at' => 'Created Time',
            'sale_success_updated' => 'L9 Time'
        ];

        return view('backend.sale_report.l9_performance_pivot',[
            'date_filters' => $date_filters
        ]);
    }

//    public function l9_performance_pivot(Request $request){
//
//
//        $date_start = $request->get('time_start');
//        $date_end = $request->get('time_end');
//        $date_filter = $request->get('date_filter', 'created_at');
//        if(!empty($date_start)){
//            $temp = \DateTime::createFromFormat('d/m/Y', $date_start);
//            $date_start = $temp->format('Y-m-d');
//        } else {
//            $date_start = date('Y-m-d', time());
//        }
//
//        if(!empty($date_end)){
//            $temp = \DateTime::createFromFormat('d/m/Y', $date_end);
//            $date_end = $temp->format('Y-m-d');
//        } else {
//            $date_end = date('Y-m-d', time());
//        }
//
//        if(!empty($date_start) && !empty($date_end)) {
//            javascript()->put([
//                'time_start' => date('Y-m-d', strtotime($date_start)),
//                'time_end' => date('Y-m-d', strtotime($date_end))
//            ]);
//        }
//
//        $time_start = $date_start . ' 00:00:00';
//        $time_end = $date_end . ' 23:59:59';
//
//        $lead_data = [
//            'customer_care',
//            'mkt_person',
//            'in_cod',
//            'in_wait_tranfer_money',
//            'sale_success',
//            'lead_origin_price'
//        ];
//
//        $leads = \DB::table('leads')
//            ->whereBetween($date_filter, [$time_start, $time_end])
//            ->get($lead_data)
//            ->toArray();
//
//        echo "<pre>"; print_r($leads); echo "</pre>"; die;
//
//        $dataReports = [];
//
//        echo "<pre>"; print_r($dataReports); echo "</pre>"; die;
//
//        javascript()->put([
//            'report_pivot_dataset' => json_encode($dataReports),
//            'time_start' => date('Y-m-d', strtotime($date_start)),
//            'time_end' => date('Y-m-d', strtotime($date_end))
//        ]);
//
//        $date_filters = [
//            'created_at' => 'Created Time',
//            'sale_success_updated' => 'L9 Time'
//        ];
//
//        return view('backend.sale_report.l9_performance_pivot',[
//            'date_filters' => $date_filters
//        ]);
//    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @desc: Báo cáo doanh thu sale
     */
    public function report_sale_revenue(Request $request){

        $customer_care_query = "SELECT DISTINCT a.customer_care FROM leads a
            INNER JOIN (
                SELECT b.lead_id, b.call_start, DATE(b.call_start) AS date_only,  b.interaction_no, c.max_interaction AS max_interaction, s12, s12_timestamp, s13, s14, s15 FROM lead_interactions b
                        INNER JOIN(
				SELECT lead_id, call_start, MAX(interaction_no) AS max_interaction FROM lead_interactions
				GROUP BY lead_interactions.interaction_no, lead_interactions.lead_id
                        ) c 
                    ON b.lead_id=c.lead_id AND
                    b.interaction_no=c.max_interaction
            ) d 
            ON a.id=d.lead_id
            
            
            LEFT JOIN (
		        SELECT lead_id, SUM(upsale_price) AS upsale_revenue FROM lead_upsales
		        GROUP BY lead_id
            ) u
            ON a.id=u.lead_id
            
            WHERE a.status=1
            AND a.is_duplicated=0
            AND d.s12=1";

        $customer_cares = DB::select($customer_care_query);

        $params = [
            'time_start' => '2018-06-30 00:00:00',
            'time_end' => '2018-07-31 23:59:59',
        ];

        $reports = [];
        foreach ($customer_cares as $customer_care){
            $key = !empty($customer_care->customer_care) ? $customer_care->customer_care : 'NULL';
            $params['customer_care'] = $key;
            $reports[$key] = Lead::sale_revenue_report($params);
        }

//        echo "<pre>"; print_r($reports); echo "</pre>"; die;

//        $reports['Trang'] = [
//            'Sumary' => [
//                'total_s12_count' => $total_s12_count,
//                'total_sum_revenue' => $total_sum_revenue,
//                'total_sum_upsale_revenue' => $total_sum_upsale_revenue,
//                'total_reassign_revenue' => $total_reassign_revenue,
//            ],
//            'reassign_reports' => $reassign_reports
//        ];

        return view('backend.sale_report.report_sale_revenue', [
            'reports' => $reports
        ]);
    }





    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @desc: Conversion report
     * @author: Thoai Văn
     */
    public function report_ba(Request $request){

        $time_end = !empty($request->get('time_end')) ? $request->get('time_end') : date('Y-m-d', time());

        $time_end_obj = new Carbon($time_end);

        $before_15_day = $time_end_obj->modify('-15 days')->toDateString();
        $time_start = !empty($request->get('time_start')) ? $request->get('time_start'): $before_15_day;

        $report_label = 'Báo cáo tỷ lệ chốt: từ ' . date('d/m/Y', strtotime($time_start)) . ' đến ' . date('d/m/Y', strtotime($time_end));
        $interaction_rate_report_label = 'Báo cáo tỷ lệ không tiếp cận được : từ ' . date('d/m/Y', strtotime($time_start)) . ' đến ' . date('d/m/Y', strtotime($time_end));


        if(!empty($time_start) && !empty($time_end)){
            javascript()->put([
                'time_start' => date('Y-m-d',strtotime($time_start)),
                'time_end' => date('Y-m-d',strtotime($time_end)),
                'detail_popup_link' => route('backend.report.detail_report_ba')
            ]);
        }

        $day_start = new \DateTime($time_start);
        $day_start->modify('-1 day');

        $params = [
            'date_start' => $time_start,
            'date_end' => $time_end
        ];


        $date_obj = new Carbon($time_start);
        $diff_by_day = $date_obj->diffInDays($time_end);


        $sale_sum_reports = LeadInteraction::query_report($params);
        $avg_sale_sum_reports = LeadInteraction::query_report($params, null, null, false);

        $combo_sum_reports = LeadInteraction::query_report_by_combo($params);
        $avg_combo_sum_report = LeadInteraction::query_report_by_combo($params, null, null, false);

//        echo "<pre>"; print_r($avg_combo_sum_report); echo "</pre>"; die;



        for ($i=0; $i <=$diff_by_day; $i++){

            $day_for = $date_obj->copy()->addDay($i)->toDateString();

            // tạo dữ liệu với ngày không có kết quả
            if(!array_key_exists($day_for, $sale_sum_reports)){
                $empty = new \stdClass();
                $empty->total_s1_sum = 0;
                $empty->total_s2_sum = 0;
                $empty->total_s3_sum = 0;
                $empty->total_s4_sum = 0;
                $empty->total_s5_sum = 0;
                $empty->total_s6_sum = 0;
                $empty->total_s7_sum = 0;
                $empty->total_s8_sum = 0;
                $empty->total_s9_sum = 0;
                $empty->total_s10_sum = 0;
                $empty->total_s11_sum = 0;
                $empty->total_s12_sum = 0;
                $empty->total_s13_sum = 0;
                $empty->total_s14_sum = 0;
                $empty->date_only = $day_for;
                $empty->interaction_success_count = 0;
                $empty->interaction_touch_count = 0;
                $empty->conversion_lead = 0;
                $empty->interaction_v1_count = 0;
                $empty->interaction_v1_touch_count = 0;
                $empty->ineraction_v1_rate = 0;

                $sale_sum_reports[$day_for] = $empty;
            }


            if(!array_key_exists($day_for, $combo_sum_reports)){
                $empty = new \stdClass();
                $empty->total_s1_sum = 0;
                $empty->total_s2_sum = 0;
                $empty->total_s3_sum = 0;
                $empty->total_s4_sum = 0;
                $empty->total_s5_sum = 0;
                $empty->total_s6_sum = 0;
                $empty->total_s7_sum = 0;
                $empty->total_s8_sum = 0;
                $empty->total_s9_sum = 0;
                $empty->total_s10_sum = 0;
                $empty->total_s11_sum = 0;
                $empty->total_s12_sum = 0;
                $empty->total_s13_sum = 0;
                $empty->total_s14_sum = 0;
                $empty->date_only = $day_for;
                $empty->interaction_success_count = 0;
                $empty->interaction_touch_count = 0;
                $empty->conversion_lead = 0;
                $empty->interaction_v1_count = 0;
                $empty->interaction_v1_touch_count = 0;
                $empty->ineraction_v1_rate = 0;

                $combo_sum_reports[$day_for] = $empty;
            }

        }

        ksort($sale_sum_reports);
        ksort($combo_sum_reports);

//        echo "<pre>"; print_r($sale_sum_reports); echo "</pre>"; die;


        $customer_cares = \DB::table('leads')
            ->select(DB::raw('DISTINCT customer_care'))
            ->get();





        $customer_care_reports = [];
        $avg_customer_report = [];
        foreach ($customer_cares as $customer_care){

            if(empty($customer_care->customer_care)){
                continue;
            }

            $report_by_date = LeadInteraction::query_report($params, null, $customer_care->customer_care);

            $avg_report = LeadInteraction::query_report($params, null, $customer_care->customer_care, false);

            for ($i=0; $i <=$diff_by_day; $i++){

                $day_for = $date_obj->copy()->addDay($i)->toDateString();

                if(!array_key_exists($day_for, $report_by_date)){
                    $empty = new \stdClass();
                    $empty->total_s1_sum = 0;
                    $empty->total_s2_sum = 0;
                    $empty->total_s3_sum = 0;
                    $empty->total_s4_sum = 0;
                    $empty->total_s5_sum = 0;
                    $empty->total_s6_sum = 0;
                    $empty->total_s7_sum = 0;
                    $empty->total_s8_sum = 0;
                    $empty->total_s9_sum = 0;
                    $empty->total_s10_sum = 0;
                    $empty->total_s11_sum = 0;
                    $empty->total_s12_sum = 0;
                    $empty->total_s13_sum = 0;
                    $empty->total_s14_sum = 0;
                    $empty->date_only = $day_for;
                    $empty->interaction_success_count = 0;
                    $empty->interaction_touch_count = 0;
                    $empty->conversion_lead = 0;
                    $empty->interaction_v1_count = 0;
                    $empty->interaction_v1_touch_count = 0;
                    $empty->ineraction_v1_rate = 0;

                    $report_by_date[$day_for] = $empty;
                }
            }

            ksort($report_by_date);

            $customer_care_reports[$customer_care->customer_care] = $report_by_date;
            $avg_customer_report[$customer_care->customer_care] = $avg_report;
        }


        $combos = \DB::table('leads')
            ->select(DB::raw('DISTINCT mkt_code'))
            ->get();

        $combos_reports = [];
        $avg_combo_reports = [];
        foreach ($combos as $combo){

            $key = empty($combo->mkt_code) ? 'NULL' : $combo->mkt_code;

            $report_by_date = LeadInteraction::query_report_by_combo($params, $combo->mkt_code);
            $avg_report = LeadInteraction::query_report_by_combo($params, $combo->mkt_code, null, null, false);

            for ($i=0; $i <=$diff_by_day; $i++){

                $day_for = $date_obj->copy()->addDay($i)->toDateString();

                if(!array_key_exists($day_for, $report_by_date)){
                    $empty = new \stdClass();
                    $empty->total_s1_sum = 0;
                    $empty->total_s2_sum = 0;
                    $empty->total_s3_sum = 0;
                    $empty->total_s4_sum = 0;
                    $empty->total_s5_sum = 0;
                    $empty->total_s6_sum = 0;
                    $empty->total_s7_sum = 0;
                    $empty->total_s8_sum = 0;
                    $empty->total_s9_sum = 0;
                    $empty->total_s10_sum = 0;
                    $empty->total_s11_sum = 0;
                    $empty->total_s12_sum = 0;
                    $empty->total_s13_sum = 0;
                    $empty->total_s14_sum = 0;
                    $empty->date_only = $day_for;
                    $empty->conversion_lead = 0;
                    $empty->interaction_success_count = 0;
                    $empty->interaction_touch_count = 0;
                    $empty->interaction_v1_count = 0;
                    $empty->interaction_v1_touch_count = 0;
                    $empty->ineraction_v1_rate = 0;

                    $report_by_date[$day_for] = $empty;
                }
            }

            ksort($report_by_date);

            $combos_reports[$key] = $report_by_date;
            $avg_combo_reports[$key] = $avg_report;
        }

//        echo "<pre>"; print_r($combos_reports); echo "</pre>"; die;


        $data = [
            'report_label' => $report_label,
            'interaction_rate_report_label' => $interaction_rate_report_label,

            'avg_sale_sum_reports' => $avg_sale_sum_reports,
            'avg_customer_report' => $avg_customer_report,

            'total_report_by_dates' => $sale_sum_reports,
            'report_customer_cares' => $customer_care_reports,

            'avg_combo_sum_report' => $avg_combo_sum_report,
            'avg_combo_reports' => $avg_combo_reports,

            'combo_sum_reports' => $combo_sum_reports,
            'combos_reports' => $combos_reports,

        ];

        return view('backend.sale_report.report_ba', $data);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail_modal_report_ba(Request $request){
        $date_report = $request->get('date');
        $break = $request->get('break_detail');
        $type = $request->get('type');

        $params = [
            'date_start' => $date_report,
            'date_end' => $date_report
        ];

        $customer_care_display = ($break != 'total') ? $break : 'Tất cả';

        $modal_lable = 'Chi tiết tương tác: ' . $customer_care_display . ' - ' . date('d/m/Y', strtotime($date_report));


        if($type == 'combo_rate'){
            $combo = null;
            if($break != 'total'){
                $combo = $break;
            }
            $report = LeadInteraction::query_report_by_combo($params, $combo);
        } else {
            $customer_care_report = null;
            if($break != 'total'){
                $customer_care_report = $break;
            }
            $report = LeadInteraction::query_report($params, null, $customer_care_report);
        }

        $report = $report[$date_report];

        if($report){

            $interaction_touch_count = $report->s9_sum + $report->s10_sum + $report->s3_sum +$report->s4_sum +$report->s7_sum + $report->s8_sum;
            $success_count = $report->s9_sum + $report->s10_sum - $report->s11_sum;
            $conversion_lead = '--';

            if( $interaction_touch_count > 0){
                $conversion_lead = $success_count / $interaction_touch_count;
                $conversion_lead = round($conversion_lead * 100, 2);
            }

            $report->success_count = $success_count;
            $report->interaction_touch_count = $interaction_touch_count;
            $report->conversion_lead = $conversion_lead;

            $interaction_v1_count = $report->s1_sum + $report->s2_sum;
            $interaction_v1_touch_count = $report->s9_sum + $report->s10_sum + $report->s3_sum +$report->s4_sum +$report->s7_sum + $report->s8_sum + $report->s1_sum + $report->s2_sum;

            $ineraction_v1_rate = '--';
            if($interaction_v1_touch_count > 0){
                $ineraction_v1_rate = round(($interaction_v1_count / $interaction_v1_touch_count) * 100, 2);
            }

            $report->interaction_v1_count = $interaction_v1_count;
            $report->interaction_v1_touch_count = $interaction_v1_touch_count;
            $report->ineraction_v1_rate = $ineraction_v1_rate;


        }

        $html = view('backend.sale_report.report_ba.__report_ba_detail_modal', [
            'type' => $type,
            'modal_lable' => $modal_lable,
            'report' => $report
        ])->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @desc: Báo cáo S12 theo thời gian tạo
     */
    public function report_s12(Request $request){

        $customer_care = $request->get('customer_care', 'all');
        $marketer = $request->get('marketer', 'all');
        $combo = $request->get('combo', 'all');

        $time_end = $request->get('time_end');
        $time_start = $request->get('time_start');

        if(empty($time_start) && empty($time_end)){
            $today = Carbon::now();

            $last_day_of_last_month = $today->copy()->firstOfMonth()->modify('-1 days')->toDateString();
            $tmp  = new Carbon($last_day_of_last_month);
            $first_day_of_last_month = $tmp->firstOfMonth()->toDateString();

            $time_start = $first_day_of_last_month;
            $time_end = $last_day_of_last_month;
        }

        $customer_care_label    = (empty($customer_care) || $customer_care == 'all') ? 'tất cả' : $customer_care;
        $markerter_label        = (empty($marketer) || $marketer == 'all') ? 'tất cả' : $marketer;
        $combo_label            = (empty($combo)) ? $combo : 'tất cả';

        $customer_cares = \DB::table('leads')
            ->select(DB::raw('DISTINCT customer_care'))
            ->get();


        $query_get_marketer = "SELECT DISTINCT a.mkt_person FROM leads a
            INNER JOIN (
                SELECT b.lead_id, b.call_start, DATE(b.call_start) AS date_only,  b.interaction_no, c.max_interaction AS max_interaction, s12, s13, s14, s15 FROM lead_interactions b
                        INNER JOIN(
				SELECT lead_id, call_start, MAX(interaction_no) AS max_interaction FROM lead_interactions
				GROUP BY lead_interactions.interaction_no, lead_interactions.lead_id
                        ) c 
                    ON b.lead_id=c.lead_id AND
                    b.interaction_no=c.max_interaction
            ) d 
            ON a.id=d.lead_id
            
            WHERE a.status=1
            AND a.is_duplicated=0
            AND d.s12=1
            AND a.time_created BETWEEN '". $time_start ." 00:00:00' AND '". $time_end." 23:59:59'";

        $markerter_results = DB::select($query_get_marketer);

        $marketers = [
            'all' => 'All - Marketer'
        ];
        foreach ($markerter_results as $markerter_result){
            $key = !empty($markerter_result->mkt_person) ? $markerter_result->mkt_person : 'NATIVE';
            $marketers[$key] = $key;
        }



        $query_get_combo = "SELECT DISTINCT a.mkt_code FROM leads a
            INNER JOIN (
                SELECT b.lead_id, b.call_start, DATE(b.call_start) AS date_only,  b.interaction_no, c.max_interaction AS max_interaction, s12, s13, s14, s15 FROM lead_interactions b
                        INNER JOIN(
				SELECT lead_id, call_start, MAX(interaction_no) AS max_interaction FROM lead_interactions
				GROUP BY lead_interactions.interaction_no, lead_interactions.lead_id
                        ) c 
                    ON b.lead_id=c.lead_id AND
                    b.interaction_no=c.max_interaction
            ) d 
            ON a.id=d.lead_id
            
            WHERE a.status=1
            AND a.is_duplicated=0
            AND d.s12=1
            AND a.time_created BETWEEN '". $time_start ." 00:00:00' AND '". $time_end." 23:59:59'";



        $combo_results = DB::select($query_get_combo);
        $combos = [
            'all' => 'All - Combo'
        ];
        foreach ($combo_results as $combo_result){
            $key = !empty($combo_result->mkt_code) ? $combo_result->mkt_code : 'NULL';
            $combos[$key] = $key;
        }



        if(!empty($time_start) && !empty($time_end)){
            javascript()->put([
                'time_start' => date('Y-m-d',strtotime($time_start)),
                'time_end' => date('Y-m-d',strtotime($time_end)),
                'detail_popup_link' => route('backend.report.detail_report_ba')
            ]);
        }

        $s0_count_query = Lead::where('status', Lead::STATUS_LIVE)
            ->where('is_duplicated', 0)
            ->whereBetween('time_created', [
                $time_start . ' 00:00:00',
                $time_end . ' 23:59:59'
            ]);


        $query = "SELECT COUNT(*) AS result_count, SUM(a.lead_origin_price) AS sum_revenue, SUM(u.upsale_revenue) AS sum_upsale_revenue, d.date_only FROM leads a
            INNER JOIN (
                SELECT b.lead_id, b.call_start, DATE(b.call_start) AS date_only,  b.interaction_no, c.max_interaction AS max_interaction, s12, s13, s14, s15 FROM lead_interactions b
                        INNER JOIN(
				SELECT lead_id, call_start, MAX(interaction_no) AS max_interaction FROM lead_interactions
				GROUP BY lead_interactions.interaction_no, lead_interactions.lead_id
                        ) c 
                    ON b.lead_id=c.lead_id AND
                    b.interaction_no=c.max_interaction
            ) d 
            ON a.id=d.lead_id
            
            
            LEFT JOIN (
		        SELECT lead_id, SUM(upsale_price) AS upsale_revenue FROM lead_upsales
		        GROUP BY lead_id
            ) u
            ON a.id=u.lead_id
            
            WHERE a.status=1
            AND a.is_duplicated=0
            AND d.s12=1
            AND a.time_created BETWEEN '". $time_start ." 00:00:00' AND '". $time_end ." 23:59:59'";

        if($customer_care == 'EMPTY'){
            $query = $query . " AND a.customer_care IS NULL ";
            $s0_count_query = $s0_count_query->whereNull('customer_care');
        } elseif($customer_care != 'all'){
            $query = $query . " AND a.customer_care='". $customer_care ."'";
            $s0_count_query = $s0_count_query->where('customer_care', $customer_care);
        }

        if($marketer != 'all'){
            $query = $query . " AND a.mkt_person='". $marketer ."'";
            $s0_count_query = $s0_count_query->where('mkt_person', $marketer);
        }

        if($combo == 'NULL'){
            $query = $query . " AND a.mkt_code IS NULL ";
            $s0_count_query = $s0_count_query->whereNull('mkt_code');
        } elseif($combo != 'all'){
            $query = $query . " AND a.mkt_code='". $combo ."'";
            $s0_count_query = $s0_count_query->where('mkt_code', $combo);
        }


        $query = $query . " GROUP BY d.date_only ORDER BY d.date_only ASC";

//        echo "<pre>"; print_r($query); echo "</pre>"; die;

        $s0_count = $s0_count_query->count();

        $report_s12s = DB::select($query);


        $s12_count = 0;
        $s12_runtime = 0;
        $sum_revenue_runtime = 0;
        $sum_upsale_revenue = 0;
        foreach ($report_s12s as $report_s12){

            $sum_upsale_revenue += $report_s12->sum_upsale_revenue;

            $s12_count += $report_s12->result_count;
            $day_obj = new Carbon($report_s12->date_only);
            $report_s12->day_of_week = vietnames_day_of_week($day_obj->dayOfWeek);

            $s12_runtime = $s12_runtime + $report_s12->result_count;
            $report_s12->s12_runtime = $s12_runtime;

            $s12_runtime_rate = '--';
            if($s0_count > 0){
                $s12_runtime_rate = round(($s12_runtime/$s0_count) * 100, 2);
            }
            $report_s12->s12_runtime_rate = $s12_runtime_rate;

            $sum_revenue_runtime += $report_s12->sum_revenue + $report_s12->sum_upsale_revenue;
            $report_s12->sum_revenue_runtime = $sum_revenue_runtime;
        }



        $s12_rate = '--';
        if($s0_count){
            $s12_rate = round(($s12_count/$s0_count) * 100, 2);
        }


        $list_customer_cares = [
            'all' => 'All - customer care'
        ];

        foreach ($customer_cares as $customer_care){
            $key = empty($customer_care->customer_care) ? 'EMPTY' : $customer_care->customer_care;
            $list_customer_cares[$key] = $key;
        }

        $report_label = 'Báo cáo S12 theo created time: Customer care:<span class="label-highlight">'. $customer_care_label .'</span>, marketer: <span class="label-highlight">'.
            $markerter_label .'</span>, combo: <span class="label-highlight">'. $combo_label .'</span>, từ <span class="label-highlight">' . date('d/m/Y', strtotime($time_start)).
            '</span> đến <span class="label-highlight">' . date('d/m/Y', strtotime($time_end)) . '</span>';


        return view('backend.sale_report.report_s12', [
            'customer_cares' => $list_customer_cares,
            'marketers' => $marketers,
            'combos' => $combos,
            'report_label' => $report_label,
            's12_count' => $s12_count,
            's0_count' => $s0_count,
            's12_rate' => $s12_rate,
            'sum_revenue_runtime' => $sum_revenue_runtime,
            'sum_upsale_revenue' => $sum_upsale_revenue,
            'reports' => $report_s12s
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @desc: Báo cáo S12 giao vận lọc thời gian theo thời điểm lên S9, S10
     */
    public function report_s12_transport(Request $request){

        $customer_care = $request->get('customer_care', 'all');
        $marketer = $request->get('marketer', 'all');
        $combo = $request->get('combo', 'all');

        $time_end = $request->get('time_end');
        $time_start = $request->get('time_start');

        if(empty($time_start) && empty($time_end)){
            $today = Carbon::now();

            $last_day_of_last_month = $today->copy()->firstOfMonth()->modify('-1 days')->toDateString();
            $tmp  = new Carbon($last_day_of_last_month);
            $first_day_of_last_month = $tmp->firstOfMonth()->toDateString();

            $time_start = $first_day_of_last_month;
            $time_end = $last_day_of_last_month;
        }

        $customer_care_label    = (empty($customer_care) || $customer_care == 'all') ? 'tất cả' : $customer_care;
        $markerter_label        = (empty($marketer) || $marketer == 'all') ? 'tất cả' : $marketer;
        $combo_label            = (empty($combo)) ? $combo : 'tất cả';

        $customer_cares = \DB::table('leads')
            ->select(DB::raw('DISTINCT customer_care'))
            ->get();


        $query_get_marketer = "SELECT DISTINCT a.mkt_person FROM leads a
            INNER JOIN (
                SELECT b.lead_id, b.call_start, DATE(b.call_start) AS date_only,  b.interaction_no, c.max_interaction AS max_interaction, s12, s13, s14, s15 FROM lead_interactions b
                        INNER JOIN(
				SELECT lead_id, call_start, MAX(interaction_no) AS max_interaction FROM lead_interactions
				GROUP BY lead_interactions.interaction_no, lead_interactions.lead_id
                        ) c 
                    ON b.lead_id=c.lead_id AND
                    b.interaction_no=c.max_interaction
            ) d 
            ON a.id=d.lead_id
            
            WHERE a.status=1
            AND a.is_duplicated=0
            AND d.s12=1
            AND a.time_created BETWEEN '". $time_start ." 00:00:00' AND '". $time_end." 23:59:59'";

        $markerter_results = DB::select($query_get_marketer);

        $marketers = [
            'all' => 'All - Marketer'
        ];
        foreach ($markerter_results as $markerter_result){
            $key = !empty($markerter_result->mkt_person) ? $markerter_result->mkt_person : 'NATIVE';
            $marketers[$key] = $key;
        }



        $query_get_combo = "SELECT DISTINCT a.mkt_code FROM leads a
            INNER JOIN (
                SELECT b.lead_id, b.call_start, DATE(b.call_start) AS date_only,  b.interaction_no, c.max_interaction AS max_interaction, s12, s13, s14, s15 FROM lead_interactions b
                        INNER JOIN(
				SELECT lead_id, call_start, MAX(interaction_no) AS max_interaction FROM lead_interactions
				GROUP BY lead_interactions.interaction_no, lead_interactions.lead_id
                        ) c 
                    ON b.lead_id=c.lead_id AND
                    b.interaction_no=c.max_interaction
            ) d 
            ON a.id=d.lead_id
            
            WHERE a.status=1
            AND a.is_duplicated=0
            AND d.s12=1
            AND a.time_created BETWEEN '". $time_start ." 00:00:00' AND '". $time_end." 23:59:59'";

        $combo_results = DB::select($query_get_combo);
        $combos = [
            'all' => 'All - Combo'
        ];
        foreach ($combo_results as $combo_result){
            $key = !empty($combo_result->mkt_code) ? $combo_result->mkt_code : 'NULL';
            $combos[$key] = $key;
        }



        if(!empty($time_start) && !empty($time_end)){
            javascript()->put([
                'time_start' => date('Y-m-d',strtotime($time_start)),
                'time_end' => date('Y-m-d',strtotime($time_end)),
                'detail_popup_link' => route('backend.report.detail_report_ba')
            ]);
        }

        $s9_10_count_query = "SELECT COUNT(*) AS result_count FROM leads a
            INNER JOIN (
                SELECT b.lead_id, b.call_start, DATE(b.call_start) AS date_only,  b.interaction_no, c.max_interaction AS max_interaction, s9, s9_timestamp, s10, s10_timestamp, s12 FROM lead_interactions b
                        INNER JOIN(
				SELECT lead_id, call_start, MAX(interaction_no) AS max_interaction FROM lead_interactions
				GROUP BY lead_interactions.interaction_no, lead_interactions.lead_id
                        ) c 
                    ON b.lead_id=c.lead_id AND
                    b.interaction_no=c.max_interaction
            ) d 
            ON a.id=d.lead_id
            WHERE a.status=1
            AND a.is_duplicated=0
            AND (d.s9=1 OR d.s10=1)
            AND ((d.s9_timestamp BETWEEN '". $time_start ." 00:00:00' AND '". $time_end ." 23:59:59') OR (d.s10_timestamp BETWEEN '". $time_start ." 00:00:00' AND '". $time_end ." 23:59:59'))
            ";


        $query = "SELECT COUNT(*) AS result_count, SUM(a.lead_origin_price) AS sum_revenue, SUM(u.upsale_revenue) AS sum_upsale_revenue, DATE(a.s12_timestamp) AS date_only FROM leads a
            INNER JOIN (
                SELECT b.lead_id, b.interaction_no, c.max_interaction AS max_interaction, b.s11, b.s13, b.s14, b.s16, b.s17, b.s18 FROM lead_interactions b
                INNER JOIN(
				    SELECT lead_id, call_start, MAX(interaction_no) AS max_interaction FROM lead_interactions
				    GROUP BY lead_interactions.lead_id
                ) c 
                ON b.lead_id=c.lead_id 
                AND b.interaction_no=c.max_interaction
            ) d 
            ON a.id=d.lead_id
            LEFT JOIN (
		        SELECT lead_id, SUM(upsale_price) AS upsale_revenue FROM lead_upsales
		        GROUP BY lead_id
            ) u
            ON a.id=u.lead_id
            
            WHERE a.status=1
            AND a.is_duplicated=0
            AND (a.s9=1 OR a.s10=1)
            AND a.s12=1
            AND ((a.s9_timestamp BETWEEN '". $time_start ." 00:00:00' AND '". $time_end ." 23:59:59') OR (a.s10_timestamp BETWEEN '". $time_start ." 00:00:00' AND '". $time_end ." 23:59:59'))
            AND d.s11 IS NULL
            AND d.s13 IS NULL
            AND d.s14 IS NULL
            AND d.s16 IS NULL
            AND d.s17 IS NULL
            AND d.s18 IS NULL
            ";


        if($customer_care == 'EMPTY'){
            $query = $query . " AND a.customer_care IS NULL ";
            $s9_10_count_query = $s9_10_count_query . " AND a.customer_care IS NULL";
        } elseif($customer_care != 'all'){
            $query = $query . " AND a.customer_care='". $customer_care ."'";
            $s9_10_count_query = $s9_10_count_query . " AND a.customer_care='". $customer_care ."'";
        }

        if($marketer != 'all'){
            $query = $query . " AND a.mkt_person='". $marketer ."'";
            $s9_10_count_query = $s9_10_count_query . " AND a.mkt_person='". $marketer ."'";
        }

        if($combo == 'NULL'){
            $query = $query . " AND a.mkt_code IS NULL ";
            $s9_10_count_query = $s9_10_count_query . " AND a.mkt_code IS NULL ";
        } elseif($combo != 'all'){
            $query = $query . " AND a.mkt_code='". $combo ."'";
            $s9_10_count_query = $s9_10_count_query . " AND a.mkt_code='". $combo ."'";
        }


        $query = $query . "GROUP BY date_only ORDER BY date_only ASC";

//        echo "<pre>"; print_r($query); echo "</pre>"; die;


        $report_s12s = DB::select($query);


        $s9_10_result = DB::select($s9_10_count_query);
        $s9_10_count = isset($s9_10_result[0]) ? $s9_10_result[0]->result_count : 0;

        $s12_count = 0;
        $s12_runtime = 0;
        $sum_revenue_runtime = 0;
        $sum_upsale_revenue = 0;

        foreach ($report_s12s as $report_s12){
            $s12_count += $report_s12->result_count;

            $day_obj = new Carbon($report_s12->date_only);
            $report_s12->day_of_week = vietnames_day_of_week($day_obj->dayOfWeek);

            $day_obj = new Carbon($report_s12->date_only);
            $report_s12->day_of_week = vietnames_day_of_week($day_obj->dayOfWeek);

            $s12_runtime = $s12_runtime + $report_s12->result_count;
            $report_s12->s12_runtime = $s12_runtime;

            $s12_runtime_rate = '--';
            if($s9_10_count > 0){
                $s12_runtime_rate = round(($s12_runtime/$s9_10_count) * 100, 2);
            }
            $report_s12->s12_runtime_rate = $s12_runtime_rate;

            $sum_revenue_runtime += $report_s12->sum_revenue;
            $sum_upsale_revenue += $report_s12->sum_upsale_revenue;
            $report_s12->sum_revenue_runtime = $sum_revenue_runtime + $sum_upsale_revenue;
        }

        $s12_rate = '--';
        if($s9_10_count){
            $s12_rate = round(($s12_count/$s9_10_count) * 100, 2);
        }

//        echo "<pre>"; print_r($report_s12s); echo "</pre>"; die;


        $list_customer_cares = [
            'all' => 'All - customer care'
        ];

        foreach ($customer_cares as $customer_care){
            $key = empty($customer_care->customer_care) ? 'EMPTY' : $customer_care->customer_care;
            $list_customer_cares[$key] = $key;
        }


        $report_label = 'Báo cáo S12 giao vận: Customer care:<span class="label-highlight">'. $customer_care_label .'</span>, marketer: <span class="label-highlight">'.
            $markerter_label .'</span>, combo: <span class="label-highlight">'. $combo_label .'</span>, từ <span class="label-highlight">' . date('d/m/Y', strtotime($time_start)).
            '</span> đến <span class="label-highlight">' . date('d/m/Y', strtotime($time_end)) . '</span>';


        return view('backend.sale_report.report_s12_transport', [
            'customer_cares' => $list_customer_cares,
            'marketers' => $marketers,
            'combos' => $combos,
            'report_label' => $report_label,
            's9_10_count' => $s9_10_count,
            's12_count' => $s12_count,
            'sum_revenue_runtime' => $sum_revenue_runtime,
            'sum_upsale_revenue' => $sum_upsale_revenue,
            's12_rate' => $s12_rate,
            'reports' => $report_s12s
        ]);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @desc: Báo cáo S12 cutoff
     */

    public function report_s12_cutoff(Request $request){

        // kiểm tra role access Nếu là sale chỉ dc xem báo cáo của mình.

        $loginUser = \Auth::user();

        $access_full = false;

        $full_access_roles = [
            'Administrator',
            'SaleManager'
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


        $customer_care = 'all';
        if($access_full){
            $customer_care = $request->get('customer_care', 'all');
        } else {
            $customer_care = LeadSaleStatus::getCustomerCareByID($loginUser->id);
        }

        $marketer = $request->get('marketer', 'all');
        $combo = $request->get('combo', 'all');

        $time_end = $request->get('time_end');
        $time_start = $request->get('time_start');

        if(empty($time_start) && empty($time_end)){
            $today = Carbon::now();

            $last_day_of_last_month = $today->copy()->firstOfMonth()->modify('-1 days')->toDateString();
            $tmp  = new Carbon($last_day_of_last_month);
            $first_day_of_last_month = $tmp->firstOfMonth()->toDateString();

            $time_start = $first_day_of_last_month;
            $time_end = $last_day_of_last_month;
        }

        $customer_care_label    = (empty($customer_care) || $customer_care == 'all') ? 'tất cả' : $customer_care;
        $markerter_label        = (empty($marketer) || $marketer == 'all') ? 'tất cả' : $marketer;
        $combo_label            = (empty($combo)) ? $combo : 'tất cả';

        $customer_cares = \DB::table('leads')
            ->select(DB::raw('DISTINCT customer_care'))
            ->get();


        // nếu customer_care != all thì lấy thêm báo cáo chia sẻ doanh thu với người khác
        $reassign_revenue_share_report = null;
        if($customer_care != 'all'){

            $params = [
                'time_start' => $time_start . ' 00:00:00',
                'time_end' => $time_end . ' 23:59:59',
                'customer_care' =>  $customer_care
            ];

            $reassign_revenue_share_report = Lead::sale_revenue_report($params);

        }

        $query_get_marketer = "SELECT DISTINCT a.mkt_person FROM leads a
            INNER JOIN (
                SELECT b.lead_id, b.call_start, DATE(b.call_start) AS date_only,  b.interaction_no, c.max_interaction AS max_interaction, s12, s13, s14, s15 FROM lead_interactions b
                        INNER JOIN(
				SELECT lead_id, call_start, MAX(interaction_no) AS max_interaction FROM lead_interactions
				GROUP BY lead_interactions.interaction_no, lead_interactions.lead_id
                        ) c 
                    ON b.lead_id=c.lead_id AND
                    b.interaction_no=c.max_interaction
            ) d 
            ON a.id=d.lead_id
            
            WHERE a.status=1
            AND a.is_duplicated=0
            AND d.s12=1
            AND a.time_created BETWEEN '". $time_start ." 00:00:00' AND '". $time_end." 23:59:59'";

        $markerter_results = DB::select($query_get_marketer);

        $marketers = [
            'all' => 'All - Marketer'
        ];
        foreach ($markerter_results as $markerter_result){
            $key = !empty($markerter_result->mkt_person) ? $markerter_result->mkt_person : 'NATIVE';
            $marketers[$key] = $key;
        }



        $query_get_combo = "SELECT DISTINCT a.mkt_code FROM leads a
            INNER JOIN (
                SELECT b.lead_id, b.call_start, DATE(b.call_start) AS date_only,  b.interaction_no, c.max_interaction AS max_interaction, s12, s13, s14, s15 FROM lead_interactions b
                        INNER JOIN(
				SELECT lead_id, call_start, MAX(interaction_no) AS max_interaction FROM lead_interactions
				GROUP BY lead_interactions.interaction_no, lead_interactions.lead_id
                        ) c 
                    ON b.lead_id=c.lead_id AND
                    b.interaction_no=c.max_interaction
            ) d 
            ON a.id=d.lead_id
            
            WHERE a.status=1
            AND a.is_duplicated=0
            AND d.s12=1
            AND a.time_created BETWEEN '". $time_start ." 00:00:00' AND '". $time_end." 23:59:59'";

        $combo_results = DB::select($query_get_combo);
        $combos = [
            'all' => 'All - Combo'
        ];
        foreach ($combo_results as $combo_result){
            $key = !empty($combo_result->mkt_code) ? $combo_result->mkt_code : 'NULL';
            $combos[$key] = $key;
        }



        if(!empty($time_start) && !empty($time_end)){
            javascript()->put([
                'time_start' => date('Y-m-d',strtotime($time_start)),
                'time_end' => date('Y-m-d',strtotime($time_end)),
                'detail_popup_link' => route('backend.report.detail_report_ba')
            ]);
        }

        $s9_10_count_query = "SELECT COUNT(*) AS result_count FROM leads a
            INNER JOIN (
                SELECT b.lead_id, b.call_start, DATE(b.call_start) AS date_only,  b.interaction_no, c.max_interaction AS max_interaction, s9, s9_timestamp, s10, s10_timestamp, s12 FROM lead_interactions b
                        INNER JOIN(
				SELECT lead_id, call_start, MAX(interaction_no) AS max_interaction FROM lead_interactions
				GROUP BY lead_interactions.interaction_no, lead_interactions.lead_id
                        ) c 
                    ON b.lead_id=c.lead_id AND
                    b.interaction_no=c.max_interaction
            ) d 
            ON a.id=d.lead_id
            WHERE a.status=1
            AND a.is_duplicated=0
            AND (d.s9=1 OR d.s10=1)
            AND ((d.s9_timestamp BETWEEN '". $time_start ." 00:00:00' AND '". $time_end ." 23:59:59') OR (d.s10_timestamp BETWEEN '". $time_start ." 00:00:00' AND '". $time_end ." 23:59:59'))
            ";


        $query = "SELECT e.first_care, e.second_care, e.reassign, COUNT(*) AS result_count, SUM(a.lead_origin_price) AS sum_revenue, IFNULL(SUM(u.upsale_revenue), 0) AS sum_upsale_revenue, DATE(a.s12_timestamp) AS date_only FROM leads a
	    INNER JOIN (
		SELECT id, IFNULL(first_customer_care, customer_care) AS first_care, IFNULL(second_customer_care, customer_care) AS second_care,
		IF(
			(IFNULL(first_customer_care,customer_care) !=customer_care && IFNULL(first_customer_care, customer_care) != IFNULL(second_customer_care,customer_care)), 
			IFNULL(first_customer_care, customer_care), 
			IFNULL(second_customer_care, customer_care) 
			) AS reassign FROM leads
	    ) e
	    ON a.id=e.id
	    
            INNER JOIN (
                SELECT b.lead_id, b.interaction_no, c.max_interaction AS max_interaction, s9, s9_timestamp, s10, s10_timestamp, s12, s12_timestamp FROM lead_interactions b
                        INNER JOIN(
				SELECT lead_id, call_start, MAX(interaction_no) AS max_interaction FROM lead_interactions
				GROUP BY lead_interactions.interaction_no, lead_interactions.lead_id
                        ) c 
                    ON b.lead_id=c.lead_id AND
                    b.interaction_no=c.max_interaction
            ) d 
            ON a.id=d.lead_id
            LEFT JOIN (
		        SELECT lead_id, SUM(upsale_price) AS upsale_revenue FROM lead_upsales
		        GROUP BY lead_id
            ) u
            ON a.id=u.lead_id
            
            WHERE a.status=1
            AND a.is_duplicated=0
            AND d.s12=1
            AND d.s12_timestamp BETWEEN '". $time_start ." 00:00:00' AND '". $time_end ." 23:59:59'
            ";


        if($customer_care == 'EMPTY'){
            $query = $query . " AND a.customer_care IS NULL ";
            $s9_10_count_query = $s9_10_count_query . " AND a.customer_care IS NULL";
        } elseif($customer_care != 'all'){
            $query = $query . " AND a.customer_care='". $customer_care ."' AND e.reassign='". $customer_care ."'";
            $s9_10_count_query = $s9_10_count_query . " AND a.customer_care='". $customer_care ."'";
        }

        if($marketer != 'all'){
            $query = $query . " AND a.mkt_person='". $marketer ."'";
            $s9_10_count_query = $s9_10_count_query . " AND a.mkt_person='". $marketer ."'";
        }

        if($combo == 'NULL'){
            $query = $query . " AND a.mkt_code IS NULL ";
            $s9_10_count_query = $s9_10_count_query . " AND a.mkt_code IS NULL ";
        } elseif($combo != 'all'){
            $query = $query . " AND a.mkt_code='". $combo ."'";
            $s9_10_count_query = $s9_10_count_query . " AND a.mkt_code='". $combo ."'";
        }


        $query = $query . " GROUP BY date_only ORDER BY date_only ASC";

//        echo "<pre>"; print_r($query); echo "</pre>"; die;


        $report_s12s = DB::select($query);


        $s9_10_result = DB::select($s9_10_count_query);
        $s9_10_count = isset($s9_10_result[0]) ? $s9_10_result[0]->result_count : 0;

        $s12_count = 0;
        $s12_runtime = 0;
        $sum_revenue = 0;
        $sum_upsale_revenue = 0;

        foreach ($report_s12s as $report_s12){
            $s12_count += $report_s12->result_count;
            $day_obj = new Carbon($report_s12->date_only);
            $report_s12->day_of_week = vietnames_day_of_week($day_obj->dayOfWeek);

            $day_obj = new Carbon($report_s12->date_only);
            $report_s12->day_of_week = vietnames_day_of_week($day_obj->dayOfWeek);

            $s12_runtime = $s12_runtime + $report_s12->result_count;
            $report_s12->s12_runtime = $s12_runtime;

            $s12_runtime_rate = '--';
            if($s9_10_count > 0){
                $s12_runtime_rate = round(($s12_runtime/$s9_10_count) * 100, 2);
            }
            $report_s12->s12_runtime_rate = $s12_runtime_rate;

            $sum_revenue += $report_s12->sum_revenue;
            $sum_upsale_revenue += $report_s12->sum_upsale_revenue;
            $report_s12->sum_revenue_runtime = $sum_revenue + $sum_upsale_revenue;
        }

        $s12_rate = '--';
        if($s9_10_count){
            $s12_rate = round(($s12_count/$s9_10_count) * 100, 2);
        }

//        echo "<pre>"; print_r($report_s12s); echo "</pre>"; die;


        $list_customer_cares = [
            'all' => 'All - customer care'
        ];

        foreach ($customer_cares as $customer_care){
            $key = empty($customer_care->customer_care) ? 'EMPTY' : $customer_care->customer_care;
            $list_customer_cares[$key] = $key;
        }


        $report_label = 'Báo cáo S12 Cutoff: Customer care:<span class="label-highlight">'. $customer_care_label .'</span>, marketer: <span class="label-highlight">'.
            $markerter_label .'</span>, combo: <span class="label-highlight">'. $combo_label .'</span>, từ <span class="label-highlight">' . date('d/m/Y', strtotime($time_start)).
            '</span> đến <span class="label-highlight">' . date('d/m/Y', strtotime($time_end)) . '</span>';

        return view('backend.sale_report.report_s12_cutoff', [
            'access_full' => $access_full,
            'customer_cares' => $list_customer_cares,
            'marketers' => $marketers,
            'combos' => $combos,
            'report_label' => $report_label,
            's9_10_count' => $s9_10_count,
            's12_count' => $s12_count,
            'sum_revenue' => $sum_revenue,
            'sum_upsale_revenue' => $sum_upsale_revenue,
            's12_rate' => $s12_rate,
            'reports' => $report_s12s,
            'reassign_revenue_share_report' => $reassign_revenue_share_report
        ]);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @desc: Báo cáo liệt kê tất cả các S theo thời gian tạo
     */
    public function report_interaction(Request $request){

        $customer_care = $request->get('customer_care', 'all');
        $marketer = $request->get('marketer', 'all');
        $combo = $request->get('combo', 'all');

        $time_end = $request->get('time_end');
        $time_start = $request->get('time_start');

        if(empty($time_start) && empty($time_end)){
            $today = Carbon::now();

            $last_day_of_last_month = $today->copy()->firstOfMonth()->modify('-1 days')->toDateString();
            $tmp  = new Carbon($last_day_of_last_month);
            $first_day_of_last_month = $tmp->firstOfMonth()->toDateString();

            $time_start = $first_day_of_last_month;
            $time_end = $last_day_of_last_month;
        }

        $customer_care_label    = (empty($customer_care) || $customer_care == 'all') ? 'tất cả' : $customer_care;
        $markerter_label        = (empty($marketer) || $marketer == 'all') ? 'tất cả' : $marketer;
        $combo_label            = (empty($combo)) ? $combo : 'tất cả';

        $customer_cares = \DB::table('leads')
            ->select(DB::raw('DISTINCT customer_care'))
            ->get();


        $query_get_marketer = "SELECT DISTINCT a.mkt_person FROM leads a
            INNER JOIN (
                SELECT b.lead_id, b.call_start, DATE(b.call_start) AS date_only,  b.interaction_no, c.max_interaction AS max_interaction, s12, s13, s14, s15 FROM lead_interactions b
                        INNER JOIN(
				SELECT lead_id, call_start, MAX(interaction_no) AS max_interaction FROM lead_interactions
				GROUP BY lead_interactions.interaction_no, lead_interactions.lead_id
                        ) c 
                    ON b.lead_id=c.lead_id AND
                    b.interaction_no=c.max_interaction
            ) d 
            ON a.id=d.lead_id
            
            WHERE a.status=1
            AND a.is_duplicated=0
            AND d.s12=1
            AND a.time_created BETWEEN '". $time_start ." 00:00:00' AND '". $time_end." 23:59:59'";

        $markerter_results = DB::select($query_get_marketer);

        $marketers = [
            'all' => 'All - Marketer'
        ];
        foreach ($markerter_results as $markerter_result){
            $key = !empty($markerter_result->mkt_person) ? $markerter_result->mkt_person : 'NATIVE';
            $marketers[$key] = $key;
        }



        $query_get_combo = "SELECT DISTINCT a.mkt_code FROM leads a
            INNER JOIN (
                SELECT b.lead_id, b.call_start, DATE(b.call_start) AS date_only,  b.interaction_no, c.max_interaction AS max_interaction, s12, s13, s14, s15 FROM lead_interactions b
                        INNER JOIN(
				SELECT lead_id, call_start, MAX(interaction_no) AS max_interaction FROM lead_interactions
				GROUP BY lead_interactions.interaction_no, lead_interactions.lead_id
                        ) c 
                    ON b.lead_id=c.lead_id AND
                    b.interaction_no=c.max_interaction
            ) d 
            ON a.id=d.lead_id
            
            WHERE a.status=1
            AND a.is_duplicated=0
            AND d.s12=1
            AND a.time_created BETWEEN '". $time_start ." 00:00:00' AND '". $time_end." 23:59:59'";



        $combo_results = DB::select($query_get_combo);
        $combos = [
            'all' => 'All - Combo'
        ];
        foreach ($combo_results as $combo_result){
            $key = !empty($combo_result->mkt_code) ? $combo_result->mkt_code : 'NULL';
            $combos[$key] = $key;
        }



        if(!empty($time_start) && !empty($time_end)){
            javascript()->put([
                'time_start' => date('Y-m-d',strtotime($time_start)),
                'time_end' => date('Y-m-d',strtotime($time_end)),
                'detail_popup_link' => route('backend.report.detail_report_ba')
            ]);
        }

        $s0_count_query = Lead::where('status', Lead::STATUS_LIVE)
            ->where('is_duplicated', 0)
            ->whereBetween('time_created', [
                $time_start . ' 00:00:00',
                $time_end . ' 23:59:59'
            ]);


        $query = " SELECT SUM(d.s0) AS sum_s0, SUM(d.s1) AS sum_s1, SUM(d.s2) AS sum_s2, SUM(d.s3) AS sum_s3, SUM(d.s4) AS sum_s4, SUM(d.s5) AS sum_s5, SUM(d.s6) AS sum_s6, 
            SUM(d.s7) AS sum_s7, SUM(d.s8) AS sum_s8, SUM(d.s9) AS sum_s9, SUM(d.s10) AS sum_s10, SUM(d.s11) AS sum_s11, SUM(d.s12) AS sum_s12, SUM(d.s13) AS sum_s13, SUM(d.s14) AS sum_s14, SUM(d.s15) AS sum_s15,
            SUM(d.s16) AS sum_s16, SUM(d.s17) AS sum_s17,
            DATE(a.time_created) AS date_only FROM leads a
            INNER JOIN (
                SELECT b.lead_id, b.call_start,  b.interaction_no, c.max_interaction AS max_interaction, s0, s1, s2, s3, s4, s5, s6, s7, s8,s9, s10, s11, s12, s13, s14, s15, s16, s17 FROM lead_interactions b
                        INNER JOIN(
				SELECT lead_id, call_start, MAX(interaction_no) AS max_interaction FROM lead_interactions
				GROUP BY lead_interactions.lead_id
                        ) c 
                    ON b.lead_id=c.lead_id AND
                    b.interaction_no=c.max_interaction
            ) d 
            ON a.id=d.lead_id
            
            WHERE a.status=1
            AND a.is_duplicated=0 
            AND a.time_created BETWEEN '". $time_start ." 00:00:00' AND '". $time_end ." 23:59:59'
            ";


        if($customer_care == 'EMPTY'){
            $query = $query . " AND a.customer_care IS NULL ";
            $s0_count_query = $s0_count_query->whereNull('customer_care');
        } elseif($customer_care != 'all'){
            $query = $query . " AND a.customer_care='". $customer_care ."'";
            $s0_count_query = $s0_count_query->where('customer_care', $customer_care);
        }

        if($marketer != 'all'){
            $query = $query . " AND a.mkt_person='". $marketer ."'";
            $s0_count_query = $s0_count_query->where('mkt_person', $marketer);
        }

        if($combo == 'NULL'){
            $query = $query . " AND a.mkt_code IS NULL ";
            $s0_count_query = $s0_count_query->whereNull('mkt_code');
        } elseif($combo != 'all'){
            $query = $query . " AND a.mkt_code='". $combo ."'";
            $s0_count_query = $s0_count_query->where('mkt_code', $combo);
        }


        $query = $query . " GROUP BY date_only ORDER BY date_only ASC";



        $s0_count = $s0_count_query->count();

        $interaction_reports = DB::select($query);


        $s12_count = 0;
        $s12_runtime = 0;
        $sum_revenue_runtime = 0;


        $s12_rate = '--';
        if($s0_count){
            $s12_rate = round(($s12_count/$s0_count) * 100, 2);
        }


        $list_customer_cares = [
            'all' => 'All - customer care'
        ];

        foreach ($customer_cares as $customer_care){
            $key = empty($customer_care->customer_care) ? 'EMPTY' : $customer_care->customer_care;
            $list_customer_cares[$key] = $key;
        }

        $report_label = 'Báo cáo S theo created time: Customer care:<span class="label-highlight">'. $customer_care_label .'</span>, marketer: <span class="label-highlight">'.
            $markerter_label .'</span>, combo: <span class="label-highlight">'. $combo_label .'</span>, từ <span class="label-highlight">' . date('d/m/Y', strtotime($time_start)).
            '</span> đến <span class="label-highlight">' . date('d/m/Y', strtotime($time_end)) . '</span>';


        return view('backend.sale_report.report_interaction', [
            'customer_cares' => $list_customer_cares,
            'marketers' => $marketers,
            'combos' => $combos,
            'report_label' => $report_label,
            's12_count' => $s12_count,
            's0_count' => $s0_count,
            's12_rate' => $s12_rate,
            'sum_revenue_runtime' => $sum_revenue_runtime,
            'reports' => $interaction_reports
        ]);
    }



    public function lead_flow_report(Request $request){

        $report_type = $request->get('report_type', 'r1');
        $customer_care = $request->get('customer_care', 'all');
        $marketer = $request->get('marketer', 'all');
        $combo = $request->get('combo', 'all');


        $time_end = $request->get('time_end');
        $time_start = $request->get('time_start');

        $report_types = [
            'r1' => 'Tỷ lệ đồng ý mua', //(S9+S10)/S0 - Tỷ lệ đồng ý mua hàng
            'r2' => 'Tỷ lệ thành công', // S12/S0 - Tỷ lệ thành công
            'r3' => 'Tỷ lệ giao vận thành công', // S12/S10 - Tỷ lệ giao vận thành công
            'r4' => 'Tỷ lệ hoàn tiền',  // S14/S12 - Tỷ lệ hoàn tiền
            'r5' => 'Tỷ lệ lead không chất lượng' // (S1+S2+S5+S6)/S0 - Tỷ lệ lead không chất lượng
        ];

        $report_division_label = [
            'r1' => 'S0',
            'r2' => 'S0',
            'r3' => 'S10',
            'r4' => 'S12',
            'r5' => 'S0'
        ];
        $division_label = isset($report_division_label[$report_type]) ? $report_division_label[$report_type] : '--';

        $result_labels = [
            'r1' => 'S9+S10',
            'r2' => 'S12',
            'r3' => 'S12',
            'r4' => 'S14',
            'r5' => 'S1+S2+S5+S6'
        ];
        $result_label = isset($result_labels[$report_type]) ? $result_labels[$report_type] : '--';


        if(empty($time_start) && empty($time_end)){
            $today = Carbon::now();

            $last_day_of_last_month = $today->copy()->firstOfMonth()->modify('-1 days')->toDateString();
            $tmp  = new Carbon($last_day_of_last_month);
            $first_day_of_last_month = $tmp->firstOfMonth()->toDateString();

            $time_start = $first_day_of_last_month;
            $time_end = $last_day_of_last_month;
        }

        $report_type_label      = isset($report_types[$report_type]) ? $report_types[$report_type] : '--';
        $customer_care_label    = (empty($customer_care) || $customer_care == 'all') ? 'tất cả' : $customer_care;
        $markerter_label        = (empty($marketer) || $marketer == 'all') ? 'tất cả' : $marketer;
        $combo_label            = (empty($combo)) ? $combo : 'tất cả';


        $division_params = [
            'time_start' => $time_start,
            'time_end' => $time_end,
            'report_type' => $report_type
        ];

        $params = [
            'time_start' => $time_start,
            'time_end' => $time_end,
            'report_type' => $report_type,
        ];
        if($customer_care != 'all'){
            $params['customer_care'] = $customer_care;
            $division_params['customer_care'] = $customer_care;
        }
        if($combo != 'all'){
            $params['combo'] = $combo;
            $division_params['combo'] = $combo;
        }
        if($marketer != 'all'){
            $params['marketer'] = $marketer;
            $division_params['marketer'] = $marketer;
        }

        // get t0 group by created_time
        $sum_divisions = Lead::sum_t0_ranger_created($division_params);
        $total_sum_division = Lead::sum_t0_ranger_created($division_params, false);


        $reports = LeadInteraction::query_lead_flow_report($params);
        $runtime_report_sum = sum_array_by_attr($reports, 'runtime_report');


        $report_rate = '00,00';
        if($total_sum_division > 0){
            $report_rate = round(($runtime_report_sum/$total_sum_division)*100, 2);
        }


        $customer_cares = \DB::table('leads')
            ->select(DB::raw('DISTINCT customer_care'))
            ->get();


        $query_get_marketer = "SELECT DISTINCT a.mkt_person FROM leads a
            INNER JOIN (
                SELECT b.lead_id, b.call_start, DATE(b.call_start) AS date_only,  b.interaction_no, c.max_interaction AS max_interaction, s12, s13, s14, s15 FROM lead_interactions b
                        INNER JOIN(
				SELECT lead_id, call_start, MAX(interaction_no) AS max_interaction FROM lead_interactions
				GROUP BY lead_interactions.interaction_no, lead_interactions.lead_id
                        ) c 
                    ON b.lead_id=c.lead_id AND
                    b.interaction_no=c.max_interaction
            ) d 
            ON a.id=d.lead_id
            
            WHERE a.status=1
            AND a.is_duplicated=0
            AND d.s12=1
            AND a.time_created BETWEEN '". $time_start ." 00:00:00' AND '". $time_end." 23:59:59'";

        $markerter_results = DB::select($query_get_marketer);

        $marketers = [
            'all' => 'All - Marketer'
        ];
        foreach ($markerter_results as $markerter_result){
            $key = !empty($markerter_result->mkt_person) ? $markerter_result->mkt_person : 'NATIVE';
            $marketers[$key] = $key;
        }

        $query_get_combo = "SELECT DISTINCT a.mkt_code FROM leads a
            INNER JOIN (
                SELECT b.lead_id, b.call_start, DATE(b.call_start) AS date_only,  b.interaction_no, c.max_interaction AS max_interaction, s12, s13, s14, s15 FROM lead_interactions b
                        INNER JOIN(
				SELECT lead_id, call_start, MAX(interaction_no) AS max_interaction FROM lead_interactions
				GROUP BY lead_interactions.interaction_no, lead_interactions.lead_id
                        ) c 
                    ON b.lead_id=c.lead_id AND
                    b.interaction_no=c.max_interaction
            ) d 
            ON a.id=d.lead_id
            
            WHERE a.status=1
            AND a.is_duplicated=0
            AND d.s12=1
            AND a.time_created BETWEEN '". $time_start ." 00:00:00' AND '". $time_end." 23:59:59'";



        $combo_results = DB::select($query_get_combo);
        $combos = [
            'all' => 'All - Combo'
        ];
        foreach ($combo_results as $combo_result){
            $key = !empty($combo_result->mkt_code) ? $combo_result->mkt_code : 'NULL';
            $combos[$key] = $key;
        }



        if(!empty($time_start) && !empty($time_end)){
            javascript()->put([
                'time_start' => date('Y-m-d',strtotime($time_start)),
                'time_end' => date('Y-m-d',strtotime($time_end)),
                'detail_popup_link' => route('backend.report.detail_report_ba')
            ]);
        }

        $list_customer_cares = [
            'all' => 'All - customer care'
        ];

        foreach ($customer_cares as $customer_care){
            $key = empty($customer_care->customer_care) ? 'EMPTY' : $customer_care->customer_care;
            $list_customer_cares[$key] = $key;
        }

        $report_label = 'Báo cáo gói Lead theo ngày: Tỷ lệ: <span class="label-highlight">'. $report_type_label .'</span>, customer care:<span class="label-highlight">'. $customer_care_label .'</span>, marketer: <span class="label-highlight">'.
            $markerter_label .'</span>, combo: <span class="label-highlight">'. $combo_label .'</span>, từ <span class="label-highlight">' . date('d/m/Y', strtotime($time_start)).
            '</span> đến <span class="label-highlight">' . date('d/m/Y', strtotime($time_end)) . '</span>';



        return view('backend.sale_report.report_lead_flow', [
            'report_types' => $report_types,
            'report_type' =>$report_type,
            'customer_cares' => $list_customer_cares,
            'marketers' => $marketers,
            'combos' => $combos,
            'report_label' => $report_label,
            'sum_divisions' => $sum_divisions,
            'division_label' => $division_label,
            'total_sum_division' => $total_sum_division,
            'runtime_report_sum' => $runtime_report_sum,
            'result_label' => $result_label,
            'report_rate' => $report_rate,
            'reports' => $reports
        ]);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * Report average speed to answer ASA
     */
    public function asa_report(Request $request ){

        $time_end = !empty($request->get('time_end')) ? $request->get('time_end') : date('Y-m-d', time());
        $time_end_obj = new Carbon($time_end);

        $before_15_day = $time_end_obj->modify('-15 days')->toDateString();
        $time_start = !empty($request->get('time_start')) ? $request->get('time_start'): $before_15_day;

        $group_by = $request->get('report_type', 'customer_care');

        if(!empty($time_start) && !empty($time_end)){
            javascript()->put([
                'time_start' => date('Y-m-d',strtotime($time_start)),
                'time_end' => date('Y-m-d',strtotime($time_end)),
                'detail_popup_link' => route('backend.report.detail_report_ba')
            ]);
        }

        $day_start = new \DateTime($time_start);
        $day_start->modify('-1 day');

        $report_types = [
            'customer_care' => 'Customer_care',
            'mkt_person' => 'Marketer',
            'mkt_code' => 'Combo',
        ];

        if(!isset($report_types[$group_by])){
            $group_by = 'customer_care';
        }

        $params = [
            'time_start' => $time_start . ' 00:00:00',
            'time_end' => $time_end . ' 23:59:59',
            'group_by' => $group_by
        ];

        $report_type_label = 'Customer care';
        if($group_by == 'mkt_person')
            $report_type_label = 'Marketer';
        if($group_by == 'mkt_code')
            $report_type_label = 'Combo';

        $reports = LeadInteraction::asa_report($params);

        $report_label = 'Báo cáo tốc độ trung bình chăm sóc theo: <span class="label-highlight">'. $report_type_label .'</span>, created time từ <span class="label-highlight">' . date('d/m/Y', strtotime($time_start)).
            '</span> đến <span class="label-highlight">' . date('d/m/Y', strtotime($time_end)) . '</span>';


        return view('backend.sale_report.report_asa', [
            'report_label' => $report_label,
            'report_types' => $report_types,
            'reports' => $reports
        ]);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @desc: Báo cáo phân lead cho sale.
     */
    public function report_assign_s0(Request $request){


        $time_end = $request->get('time_end');
        $time_start = $request->get('time_start');

        if(empty($time_start) && empty($time_end)){
            $today = Carbon::now();
            $time_start = $today->toDateString();
            $time_end = $today->toDateString();
        }


        javascript()->put([
            'time_start' => date('Y-m-d',strtotime($time_start)),
            'time_end' => date('Y-m-d',strtotime($time_end)),
        ]);



        $customer_cares = \DB::table('lead_interactions')
            ->select(DB::raw('DISTINCT customer_care'))
            ->where('s0', 1)
            ->get()->mapWithKeys(function ($item){
                return [$item->customer_care => $item->customer_care];
            })
            ->toArray();

        $param_report = [
            'time_start' => $time_start,
            'time_end' => $time_end,
        ];

        $reports = [];
        foreach ($customer_cares as $customer_care){
            $param_report['customer_care'] = $customer_care;

            $report = SaleReport::customer_care_interaction($param_report);

            $reports[$customer_care] = $report;
        }


        $report_label = 'Báo cáo phân lead cho customer care: từ <span class="label-highlight">' . date('d/m/Y', strtotime($time_start)).
            '</span> đến <span class="label-highlight">' . date('d/m/Y', strtotime($time_end)) . '</span>';

        return view('backend.sale_report.report_assign_s0', [
            'report_label' => $report_label,
            'customer_cares' => $customer_cares,
            'reports' => $reports
        ]);
    }
}