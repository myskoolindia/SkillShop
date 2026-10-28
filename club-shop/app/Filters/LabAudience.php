<?php
namespace App\Filters;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
class LabAudience implements FilterInterface {
    public function before(RequestInterface $request, $arguments = null) {
        $path = preg_replace('~^/club-shop/~','/', $request->getUri()->getPath());
        // Payment provider callbacks keep their own verified gateway authentication.
        if (!preg_match('~^/(?:cart|checkout|basic-cart|advance-cart|advanced-cart|premium-cart|skillvation-cart|lab-design-cart|add-to-cart|buy-now)(?:[-/]|$)~',$path)) return;
        $session = session();
        if (!$session->get('auth_user_id') || (int)$session->get('auth_user_id') !== (int)$session->get('roboquest_shop_user_id') || !in_array($session->get('roboquest_shop_role'),['school','teacher','admin']) || (int)$session->get('roboquest_shop_role_expires') < time()) {
            return service('response')->setStatusCode(403)->setBody('Lab checkout requires a verified school or teacher account. Open the lab shop from Skillvation while signed in.');
        }
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
