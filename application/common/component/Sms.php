<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2019/1/7
 * Time: 10:42
 */
namespace app\common\component;

use Flc\Dysms\Client;
use Flc\Dysms\Request\SendSms;

class Sms {

    protected $key;  //阿里短信key
    protected $secret;  //阿里短信密钥
    protected $sign; //阿里短信签名

    public function __construct(){
        $this->key = config('sms.key');
        $this->secret = config('sms.secret');
        $this->sign = config('sms.sign');
    }

    /**
     * @param int $phone
     * @param string $tpl
     * @param array $param
     * @return array
     */
    public function send($phone ,$param, $tpl){
        $config = [
            'accessKeyId' => $this->key,
            'accessKeySecret' => $this->secret,
        ];
        $client  = new Client($config);
        $sendSms = new SendSms();
        $sendSms->setPhoneNumbers($phone);
        $sendSms->setSignName($this->sign);
        $sendSms->setTemplateCode($tpl);
        $sendSms->setTemplateParam($param);

        $resp = $client->execute($sendSms);
        $model = new \app\common\model\common\Sms();

        $status = 0;
        if( $resp->Code == 'OK' ){
            $status = 1;
        }
        $model->save([
            'phone' => $phone,
            'tpl' => $tpl,
            'content' => json_encode($param),
            'code' => $resp->Code,
            'msg' => $resp->Message,
            'status' => $status
        ]);
        if( $resp->Code == 'OK' ){
            return success('发送成功');
        }else{
            return error($resp->Message);
        }
    }

}