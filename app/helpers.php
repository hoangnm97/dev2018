<?php

/*
 * Global helpers file with misc functions
 */

if (!function_exists('app_name')) {
    /**
     * Helper to grab the application name
     *
     * @return mixed
     */
    function app_name()
    {
        return config('app.name');
    }
}

if (!function_exists('access')) {
    /**
     * Access (lol) the Access:: facade as a simple function
     */
    function access()
    {
        return app('access');
    }
}

if (!function_exists('javascript')) {
    /**
     * Access the javascript helper
     */
    function javascript()
    {
        return app('JavaScript');
    }
}

/**
 * @param $root
 * @param string|int $time timestamp
 * @param string $append
 * @param bool $create
 * @param null $tz
 * @return mixed|string
 */
function getPathByDay($root, $time = 'now', $append = '', $create = false, $tz = null)
{
    $path = rtrim($root, '\\\/');
    $time_helper = new \Carbon\Carbon($time, $tz);
    $path .= DIRECTORY_SEPARATOR . $time_helper->year
        . DIRECTORY_SEPARATOR . $time_helper->format('m_d');
    if ($append != '') {
        $path .= DIRECTORY_SEPARATOR . $append;
    }
    if ($create && \Illuminate\Support\Facades\File::exists($path)) {
        \Illuminate\Support\Facades\File::makeDirectory($path);
    }
    return $path;
}

function my_tmp($tm_path)
{
    return str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $tm_path . DIRECTORY_SEPARATOR . auth()->user()->id);
}

/**
 * @param \App\Models\Category $node
 * @return string
 */
function renderCategoryNode($node)
{
    if ($node->isLeaf()) {
        return '<li>' . renderOneCategory($node) . '</li>';
    } else {
        $html = '<li>' . renderOneCategory($node);

        $html .= '<ul class="">';

        foreach ($node->children as $child)
            $html .= renderCategoryNode($child);

        $html .= '</ul>';

        $html .= '</li>';
    }
    return $html;
}

/**
 * @param \App\Models\Category $node
 * @return string
 */
function renderOneCategory($node)
{
    return '<div>' . $node->cat_title . '
				<div class="box-tools pull-right">
				  <span class="label label-default">' . trans('common.add') . '</span>
				  <span class="label label-default">' . trans('common.edit') . '</span>
				  <span class="label label-default">' . trans('common.deleted') . '</span>
			</div>';
}

/**
 * Tạo ra 1 chuỗi global unique id
 * @param string|bool|false $prefix
 * @param bool|false $braces
 * @return string
 */
function guid($prefix = false, $braces = false)
{
    mt_srand((double)microtime() * 10000);
    $charid = strtoupper(md5(uniqid($prefix === false ? rand() : $prefix, true)));
    $hyphen = chr(45); // "-"
    $uuid = substr($charid, 0, 8) . $hyphen
        . substr($charid, 8, 4) . $hyphen
        . substr($charid, 12, 4) . $hyphen
        . substr($charid, 16, 4) . $hyphen
        . substr($charid, 20, 12);

    // Add brackets or not? "{" ... "}"
    return $braces ? chr(123) . $uuid . chr(125) : $uuid;
}

/**
 * Chuẩn hóa chuỗi trước khi sử dụng nó như tên của file
 * @param $string
 * @param string $prepend
 * @param string $replacement
 * @return mixed
 */
function makeValidFileName($string, $prepend = '', $replacement = '_')
{
    $string = rtrim($prepend . $string, " \t\n\r\0\x0B.");
    return str_replace(['\\', '/', ':', '%', '*', '?', '\'', '"', '|', '>', '<'],
        $replacement, $string);
}

function onlineDocumentView($doc_path){
    if(env('APP_ENV') == 'local'){
        return getPdfEmbedLink($doc_path);
    }
    if(config('app.online_document_viewer') == 'google'){
        return getGoogleViewer($doc_path);
    }elseif(config('app.online_document_viewer') == 'microsoft'){
        return getOfficeViewer($doc_path);
    };
    return "Chưa cấu hình trình đọc online";
}

function getPdfEmbedLink($pdfLink)
{
    return url('pdfviewer/web/viewer.html?file=' . urlencode($pdfLink));
}

function getGoogleViewer($doc_link)
{
    return "https://docs.google.com/viewer?url=" . urlencode($doc_link) . '&embedded=true';
}

function getOfficeViewer($doc_link)
{
    return "https://view.officeapps.live.com/op/view.aspx?src=" . urlencode($doc_link);
}

/**
 * @param \App\Models\Course $course
 * @return string guess/user/register/instructor/teacher/admin/
 */
function myRole(\App\Models\Course $course, \App\Models\User $user = null)
{
    if($user == null){
        // guess/user/register/instructor/teacher/admin/
        if (auth()->guest()) {
            return 'guess';
        }
        $user = auth()->user();
    }

    $user_id = $user->id;
    if ($user->hasRole(config('access.role_list.teacher')) && $course->cou_user_id == $user_id) {
        return 'teacher';
    }
    if ($user->hasRole(config('access.role_list.administrator'))) {
        return 'admin';
    }
    if ($user->hasRoles([config('access.role_list.user'),
                            config('access.role_list.teacher')], false)) {
        $course_student = \App\Models\CourseStudent::whereUserId($user_id)->whereCourseId($course->id)->exists();
        if ($course_student) {
            return 'register';
        } else {
            if($user->hasPermission('course_admod')){
                return 'course_admod';
            }
            return 'user';
        }
    }
}

function makeViewToken($string){
    return md5($string . time() . config('app.key'));
}

function human_filesize($bytes, $decimals = 2){
    $size = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}

function human_timesize($second, $show_hours = false, $zero = null){
    $hour = 0;

    if($zero !== null && $second == 0){
        return $zero;
    }

    if($show_hours){
        $hour = intval($second/3600);
        $minute = intval($second/60)%60;
    }else{
        $minute = intval($second/60);
    }
    $second = $second % 60;

    if($show_hours){
        return ($hour < 10 ? "0" . $hour : $hour)
            . ":" . ($minute < 10 ? "0" . $minute : $minute)
            . ":" . ($second < 10 ? "0" . $second : $second);
    }else{
        return ($minute < 10 ? "0" . $minute : $minute)
        . ":" . ($second < 10 ? "0" . $second : $second);
    }
}

/**
 * Hiển thị tiền dạng kèm đơn vị, dấu phẩy, ... cho dễ đọc
 * @param $amount
 * @param null $empty_text
 * @param null $currency
 * @return null|string
 */
function human_money($amount, $empty_text = null, $currency = null){

    $empty_text = $empty_text === null ? trans('common.free') : $empty_text;
    $currency = $currency === null ? config('app.currency') : $currency;
    if($amount == 0 && $empty_text != ''){
        return $empty_text;
    }else{
        return number_format($amount,
            config('app.decimals'),
            config('app.dec_point'),
            config('app.thousands_sep')) . " " . $currency;
    }
}

/**
 * Nếu tên người dùng trùng với tên người đang nhập thì hiển thị xưng danh trực tiếp với người đăng nhập.
 * @param \App\Models\User $user
 * @return mixed|string
 */
function get_name(\App\Models\User $user){
    try{
        if(auth()->user()->id == $user->id){
            return trans('common.you');
        }else{
            return $user->full_name;
        }
    }catch (\Exception $ex){
        return $user->full_name;
    }
}

/**
 * Tạo link sắp xếp
 * @param \Illuminate\Contracts\Pagination\Paginator $paginate
 * @param array $request
 * @param array $new_params
 * @param string $prefix
 * @param string $subfix
 * @return string
 */
function make_sort_link(Illuminate\Contracts\Pagination\Paginator $paginate,
                        array $request,
                        array $new_params,
                        $prefix = '',
                        $subfix = ''){
    $appends = $request;
    $page = array_get($request, 'page', 1);

    $sorting_by = array_get($request, 'key', '');
    $sorting_direction = array_get($request, 'sort', '');
    $sort_by = array_get($new_params, 'key', '');
    $sort_direction = array_get($new_params, 'sort', '');

    if($sort_by == '' || $sorting_by == $sort_by){// chỉ đổi hướng sắp xếp
        $appends['sort'] = $sorting_direction == 'asc' ? 'desc' : 'asc';
        $sorting_class = "fa-sort-" . $appends['sort'];
    }else{ // đổi trường sắp xếp
        $appends['key'] = $sort_by;
        $appends['sort'] = $sort_direction != '' ? $sort_direction : 'asc';
        $sorting_class = "fa-sort";
    }
    return "<a href='" . $paginate->appends($appends)->url($page) . "'>"
            . $prefix
            . " <i class='fa "
            . $sorting_class
            . "'></i> "
            . $subfix
            . "</a>";
}

/**
 * Chuyển mảng đầu vào thành các input dạng hidden để chèn thêm vào form
 * @param array $params
 * @return string
 */
function make_hidden_fields(array $params){
    $str = '';
    foreach($params as $k => $v){
        $str .= "<input type='hidden' name='" . $k ."' value='" . htmlentities($v) . "'/>";
    }
    return $str;
}

function genRating($avg_rate, $review_count = 0, $show_count = true){

    //width của thẻ bao ngoài star
    $long_star  =   65;
    
    $width  =   (($avg_rate*100)/5 * $long_star)/100;

    //Tính lại width do mỗi star có 5px space.(nếu ko có space thì ko cần chỗ này)
    if($avg_rate > 1){
        $width = $width + (5*intval($avg_rate)) - 1;
    }

    $star   =   '<div class="gen-star">';
    $star  .=   '<div class="stars">';
    $star  .=   '<div class="stars-content">';
    $star  .=   '<div class="fa fa-star star-on" style="width: '.$width.'px"></div>';
    $star  .=   '<div class="fa fa-star star-off"></div>';
    $star  .=   '</div>';
    $star  .=   '</div>';

    if($show_count){
        $star  .=   '<span class="review-count pull-left">('.$review_count.')</span>';
    }

    $star  .=   '</div>';
    
    return $star;
}
function genRating1($avg_rate,$show_rate = true){

    //width của thẻ bao ngoài star
    $long_star  =   65;

    $width  =   (($avg_rate*100)/5 * $long_star)/100;

    //Tính lại width do mỗi star có 5px space.(nếu ko có space thì ko cần chỗ này)
    if($avg_rate > 1){
        $width = $width + (5*intval($avg_rate)) - 1;
    }

    $star   =   '<div class="stars">';
    $star  .=   '<div class="stars-content">';
    $star  .=   '<div class="fa fa-star star-on" style="width: '.$width.'px"></div>';
    $star  .=   '<div class="fa fa-star star-off"></div>';
    $star  .=   '</div>';
    $star  .=   '</div>';

    if($show_rate){
        $star  .=   '<span class="avg-star pull-left">('.$avg_rate.')</span>';
    }

    return $star;
}

/**
 * Đoạn này dùng để chứa các id của player support multisource
 */
$ub_video_with_multi_source = [];
function add_multi_source_player($player_id){
    global $ub_video_with_multi_source;
    $ub_video_with_multi_source[] = $player_id;
}
function get_multi_source_players(){
    global $ub_video_with_multi_source;
    return empty($ub_video_with_multi_source) ? [] : $ub_video_with_multi_source;
}

/////////////
function generateCodeCod(){
    $length      = 6;
    $offset      = rand(0, 26);
    $hash        = strtoupper(md5(time()));
    $final_code  = substr($hash, $offset, $length);
    return $final_code;
}

if(!function_exists('addFilter')){
    /**
     * @param : Model $model
     * @param : Array $filter
     * @return  object Model
     ***/
    function addFilter($model,$filter = [])
    {
        if (!empty($filter)){
            foreach ($filter as $key => $value) {
                if ($value == ''){
                    unset($filter[$key]);
                }

                //Trường hợp nếu param truyền vào dạng ['column' => ['operator'=>'>','value'=> 1]]
                if(is_array($value)){
                    $op     = array_get($value, 'operator');
                    $val    = array_get($value, 'value');
                    $column = $key;
                    if($op == 'IN') {
                        $model = $model->whereIn($column, $val);
                    } elseif ($op == 'BETWEEN') {
                        $model = $model->whereBetween($column, $val);
                    } elseif ($op == 'OR') {
                        $model = $model->orWhere($column, $val);
                    } else {
                        $model = $model->where($column, $op, $val);
                    }
                } else {
                    //Trường hợp truyền vào simple ['column'=>'value']
                    $model = $model->where($key, $value);
                }
            }
        }

        return $model;
    }
}

if(!function_exists('generate_random_string')){
    function generate_random_string($length){
        $offset      = rand(1, 30);
        $hash        = strtoupper(md5(time()));
        $random_str  = substr($hash, $offset, $length);

        return $random_str;
    }
}

function vietnames_day_of_week($day_of_week){
    $vietname_mappings = [
        0 => 'Chủ nhật',
        1 => 'Thứ hai',
        2 => 'Thứ ba',
        3 => 'Thứ tư',
        4 => 'Thứ năm',
        5 => 'Thứ sáu',
        6 => 'Thứ bảy',
    ];

    if(isset($vietname_mappings[$day_of_week]))
        return $vietname_mappings[$day_of_week];

    return 'Unknow';
}

function get_class_color($point){
    if($point >= 10){
        return 'red';
    }
    if($point >= 3){
        return 'halert';
    }

    return 'green';
}

function sum_array_by_attr($array, $attr){
    $sum_result = 0;
    foreach ($array as $arr){
        $sum_result +=$arr[$attr];
    }
    return $sum_result;
}

function get_role_class_css($role){
    if($role == ''){

    }
}


if(!function_exists('parser_utm_medium')){

    function parser_utm_medium($utm_medium){


        $mkt_ref  = null;
        $mkt_team = null;

        $mkt_channel        = 'EMPTY';
        $mkt_code           = 'EMPTY';
        $mkt_person         = 'EMPTY';
        $mkt_type           = 'EMPTY';
        $mkt_landing_page   = 'EMPTY';
        $mkt_ad_group       = 'EMPTY';
        $mkt_ad_id          = 'EMPTY';

        if(!empty($utm_medium)) {

            $utm_medium_arr = explode('_', $utm_medium);

            if ($utm_medium_arr[0] == 'OUTSOURCE') {
                $mkt_ref    = 'OUTSOURCE';
                $mkt_team   = isset($utm_medium_arr[1]) ? $utm_medium_arr[1] : '';
                $mkt_code   = isset($utm_medium_arr[2]) ? $utm_medium_arr[2] : 'EMPTY';
            } elseif (in_array($utm_medium_arr[0], ['FA', 'GA', 'GDN', 'ADMARKET', 'COCCOC'])) {

                $mkt_channel        = $utm_medium_arr[0];
                $mkt_code           = isset($utm_medium_arr[1]) ? $utm_medium_arr[1] : 'EMPTY';
                $mkt_person         = isset($utm_medium_arr[2]) ? $utm_medium_arr[2] : 'EMPTY';
                $mkt_type           = isset($utm_medium_arr[3]) ? $utm_medium_arr[3] : 'EMPTY';
                $mkt_landing_page   = isset($utm_medium_arr[4]) ? $utm_medium_arr[4] : 'EMPTY';
                $mkt_ad_group       = isset($utm_medium_arr[5]) ? $utm_medium_arr[5] : 'EMPTY';
                $mkt_ad_id          = isset($utm_medium_arr[6]) ? $utm_medium_arr[6] : 'EMPTY';

            } elseif( in_array($utm_medium_arr[0], ['SOCIAL', 'YOUTUBE', 'BLOG', 'AF', 'EMAIL'])){

                $mkt_channel        = $utm_medium_arr[0];
                $mkt_code           = isset($utm_medium_arr[1]) ? $utm_medium_arr[1] : 'EMPTY';
                $mkt_person         = isset($utm_medium_arr[2]) ? $utm_medium_arr[2] : 'EMPTY';
                $mkt_type           = isset($utm_medium_arr[3]) ? $utm_medium_arr[3] : 'EMPTY';
                $mkt_landing_page   = isset($utm_medium_arr[4]) ? $utm_medium_arr[4] : 'EMPTY';
            }
            elseif( in_array($utm_medium_arr[0], ['HOCTHU'])){

                $mkt_channel        = $utm_medium_arr[0];
                $mkt_code           = isset($utm_medium_arr[1]) ? $utm_medium_arr[1] : 'EMPTY';
                $mkt_person         = isset($utm_medium_arr[2]) ? $utm_medium_arr[2] : 'EMPTY';
                $mkt_type           = isset($utm_medium_arr[3]) ? $utm_medium_arr[3] : 'EMPTY';
            }
        }

        return [
            'mkt_ref'           => $mkt_ref,
            'mkt_team'          => $mkt_team,
            'mkt_channel'       => $mkt_channel,
            'mkt_code'          => $mkt_code,
            'mkt_person'        => $mkt_person,
            'mkt_type'          => $mkt_type,
            'mkt_landing_page'  => $mkt_landing_page,
            'mkt_ad_group'      => $mkt_ad_group,
            'mkt_ad_id'         => $mkt_ad_id
        ];
    }
}