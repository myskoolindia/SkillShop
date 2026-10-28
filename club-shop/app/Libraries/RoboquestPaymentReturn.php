<?php
namespace App\Libraries;
final class RoboquestPaymentReturn {
    public static function destination(object $order,array $session,string $url): ?string {
        $userId=(int)($session['auth_user_id']??0);
        if(!$userId || $userId!==(int)($order->buyer_id??0) || $userId!==(int)($session['roboquest_shop_user_id']??0))return null;
        if(!in_array($session['roboquest_shop_role']??null,['school','teacher'],true)||(int)($session['roboquest_shop_role_expires']??0)<time())return null;
        if(($order->payment_status??'')!=='payment_received')return null;
        $origin=parse_url($url);
        if(!$origin||isset($origin['user'])||isset($origin['pass'])||isset($origin['query'])||isset($origin['fragment'])||($origin['path']??'')!=='/roboquest/open')return null;
        if(($origin['scheme']??'')!=='https'&&!(($origin['scheme']??'')==='http'&&in_array($origin['host']??'',['localhost','127.0.0.1','[::1]'],true)))return null;
        return $url;
    }
}
