<?php

namespace app\controller;

use app\BaseController;

class Pay extends BaseController
{




    public function test()
    {
        $timestamp = time();
        $order['app_id'] = "ttb905cfb8263a12dc01";
        $order['out_order_no'] = "noncestr$timestamp";
        $order['total_amount'] = 1;
        $order['subject'] = "抖音商品";
        $order['body'] = "抖音商品";
        $order['valid_time'] = 300;
        $order['notify_url'] = "https://app.mujuapi.cn/api/pay/notify";
        $order['sign'] = $this->sign($order);
        $jsonData = json_encode($order);

        $options = [
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-Type: application/json',
                'content' => $jsonData
            ]
        ];

        $context = stream_context_create($options);
        $response = file_get_contents('https://developer.toutiao.com/api/apps/ecpay/v1/create_order', false, $context);
        $responseData = json_decode($response, true);
        return json($responseData);

    }


    function sign($map)
    {
        $rList = [];
        foreach ($map as $k => $v) {
            if ($k == "other_settle_params" || $k == "app_id" || $k == "sign" || $k == "thirdparty_id")
                continue;

            $value = trim(strval($v));
            if (is_array($v)) {
                $value = $this->arrayToStr($v);
            }

            $len = strlen($value);
            if ($len > 1 && substr($value, 0, 1) == "\"" && substr($value, $len - 1) == "\"")
                $value = substr($value, 1, $len - 1);
            $value = trim($value);
            if ($value == "" || $value == "null")
                continue;
            $rList[] = $value;
        }
        $rList[] = "JEwfiklDXIfPw4nl0lT15cpmJlreMPIMQOJbTOpT";
        sort($rList, SORT_STRING);
        return md5(implode('&', $rList));
    }

    function arrayToStr($map)
    {
        $isMap = $this->isArrMap($map);

        $result = "";
        if ($isMap) {
            $result = "map[";
        }

        $keyArr = array_keys($map);
        if ($isMap) {
            sort($keyArr);
        }

        $paramsArr = array();
        foreach ($keyArr as  $k) {
            $v = $map[$k];
            if ($isMap) {
                if (is_array($v)) {
                    $paramsArr[] = sprintf("%s:%s", $k, $this->arrayToStr($v));
                } else {
                    $paramsArr[] = sprintf("%s:%s", $k, trim(strval($v)));
                }
            } else {
                if (is_array($v)) {
                    $paramsArr[] = $this->arrayToStr($v);
                } else {
                    $paramsArr[] = trim(strval($v));
                }
            }
        }

        $result = sprintf("%s%s", $result, join(" ", $paramsArr));
        if (!$isMap) {
            $result = sprintf("[%s]", $result);
        } else {
            $result = sprintf("%s]", $result);
        }

        return $result;
    }

    function isArrMap($map)
    {
        foreach ($map as $k => $v) {
            if (is_string($k)) {
                return true;
            }
        }

        return false;
    }






    
}
