<?php

use GuzzleHttp\Cookie\CookieJar;

function send_http_request($method, $url, $body)
{
    $response = [];
    $client = [];
    try {
        $client = new GuzzleHttp\Client();

        if (isset($_SESSION['jsessionid'])) {
            $cookieJar = ['headers' => [
                'Cookie' => 'JSESSIONID=' . $_SESSION['jsessionid'] . ';'
            ]];
            $body = array_merge($body, $cookieJar);
        }

        // $timeout = ['connect_timeout' => 10];
        // $body = array_merge($body, $timeout);

        $request = $client->request($method, $url, $body);
        if ($request->getStatusCode() == '200') {
            $response = $request->getBody();
        } else {
            $response = json_encode(['status' => $request->getStatusCode(), 'message' => $request->getReasonPhrase() . $request->getStatusCode()]);
        }
    } catch (Exception $e) {
        $err_response = $e->getMessage();
        $response = json_encode(['status' => '400', 'message' => $err_response]);
    }
    return json_decode($response);
}

function get_stream($url)
{
    $client = [];
    $client = new GuzzleHttp\Client();

    if (isset($_SESSION['jsessionid'])) {
        $cookieJar = ['headers' => [
            'Cookie' => 'JSESSIONID=' . $_SESSION['jsessionid'] . ';'
        ]];
        $body = array_merge([], $cookieJar);
    }

    $request = $client->request('GET', $url, $body);
    return $request->getBody();
}

function login($url, $body)
{
    $response = [];
    $client = [];
    $cookies = [];
    try {
        $client = new GuzzleHttp\Client(['cookies' => true]);
        $client->request('POST', $url . '/login', $body);
        $response = json_encode(['status' => '400', 'message' => 'Password salah / User sedang digunakan']);
        return json_decode($response);
    } catch (Exception $e) {
        $cookies = $client->getConfig('cookies')->toArray();
    }

    try {
        $cookieJar = ['headers' => [
            'Cookie' => 'JSESSIONID=' . $cookies[0]['Value'] . ';'
        ]];
        $body = array_merge([], $cookieJar);
        $request = $client->request('GET', $url . '/web/check', $body);
        if ($request->getStatusCode() == '200') {
            $response = json_encode(['status' => '200', 'data' => $client->getConfig('cookies')->toArray()]);
        } else {
            $response = json_encode(['status' => $request->getStatusCode(), 'message' => $request->getReasonPhrase() . $request->getStatusCode()]);
        }
    } catch (Exception $e) {
        $err_response = $e->getMessage();
        if (preg_match('/403/i', $err_response)) {
            $err_response = 'Hak akses user ditolak';
        }
        $response = json_encode(['status' => '400', 'message' => $err_response]);
    }
    return json_decode($response);
}
