<?php

if (!function_exists('validate_api_token')) {

    function validate_api_token(): bool
    {
        $request = service('request');

        $token = $request->getHeaderLine('Authorization');

        if (empty($token)) {
            return false;
        }

        $token = str_replace('Bearer ', '', $token);

        return $token === env('API_TOKEN');
    }
}