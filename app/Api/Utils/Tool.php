<?php


namespace App\Api\Utils;


class Tool
{
    static public function smartDate($timestamp, $isWechat = false)
    {
        if (!is_numeric($timestamp)) $timestamp = strtotime($timestamp);
        $nowstamp  = time();
        $diffstamp = $nowstamp - $timestamp;
        if ($diffstamp < 60) {
            $return = '刚刚';
        } else if ($diffstamp >= 60 && $diffstamp < 3600) {
//            if (!$isWechat) {
            $minutes = floor($diffstamp / 60);
            $return  = "{$minutes}分钟前";
//            } else {
//                $return = "刚刚";
//            }
        } else if ($diffstamp >= 3600 && $diffstamp < 86400) {
            $return = date('H:i', $timestamp);
        } else if ($diffstamp >= 86400 && $diffstamp < 86400 * 2) {
            $return = '昨天';
            if (!$isWechat) $return .= ' ' . date('H:i', $timestamp);
        } else if ($isWechat && $diffstamp >= 86400 && $diffstamp < 86400 * 7) {
            $weeks  = ['一', '二', '三', '四', '五', '六', '天'];
            $return = '星期' . $weeks[date('w', $timestamp)];
        } else if (!$isWechat && $diffstamp >= 86400 * 7 && $diffstamp < 86400 * 365) {
            $return = date('m-d H:i', $timestamp);
        } else {
            if ($isWechat) $return = date('y/m/d', $timestamp);
            else $return = date('y/m/d H:i', $timestamp);
        }
        return $return;
    }
}
