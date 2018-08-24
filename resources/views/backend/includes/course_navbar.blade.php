<?php
$active = isset($active) ? $active : '';
?>

<div class="course-admin-nav navbar navbar-default bg-white">
    <ul class="nav navbar-nav">
        <li class="{{ ($active == 'general') ? 'active' : ''}}"><a href="{{ route('backend.course.general_update', $course->id) }}">Thông tin chung</a></li>
        <li class="{{ ($active == 'money_setting') ? 'active' : ''}}"><a href="{{ route('backend.course.moneysetting', $course->id) }}">Tỷ lệ hoa hồng</a></li>
        <li class="{{ ($active == 'affiliate') ? 'active' : ''}}"><a href="{{ route('backend.course.affiliate', $course->id) }}">Affiliate</a></li>
    </ul>
</div>