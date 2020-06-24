<?php

namespace app\common\extend\sms;

class Submail
{
    public $name = '赛邮云短信';
    public $ver = '1.0';

    public function submit($phone, $code, $type_flag, $type_des, $text)
    {
        if (empty($phone) || empty($code) || empty($type_flag)) {
            return ['code' => 101, 'msg' => '参数错误'];
        }
        $appid = $GLOBALS['config']['sms']['appid'];
        $appkey = $GLOBALS['config']['sms']['appkey'];
        $sign = $GLOBALS['config']['sms']['sign'];
        $tpl = $GLOBALS['config']['sms']['tpl_code_' . $type_flag];
        $params = [
            $code
        ];
        $content = str_replace('${code}', $code, $tpl);

        try {
            $data['appid']  =   $appid;
            $data['to'] =   $phone;
            $data['signature']  =   $appkey;
            $content = '【' . $sign . '】' . $content;//要发送的短信内容
            $data['content']    =   $content;
            $query = http_build_query($data);
            $options['http'] = array(
                'timeout' => 60,
                'method' => 'POST',
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $query
            );
            $context = stream_context_create($options);
            $result = file_get_contents("https://api.mysubmail.com/message/send/", false, $context);
            $output = trim($result, "\xEF\xBB\xBF");
            $result = json_decode($output, true);
            if ($result['status'] == 'success') {
                return ['code' => 1, 'msg' => 'ok'];
            }
            return ['code' => 101, 'msg' => $result['msg']];
        } catch (\Exception $e) {
            return ['code' => 102, 'msg' => '发生异常请重试'];
        }
    }
}
