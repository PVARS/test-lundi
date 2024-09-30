<?php

function call_api($endpoint, $method = 'GET', $params = [], $headers = [])
{
    if (isset($_SESSION['auth'])) {
        $password = $_SESSION['auth']['token'];
        $credentials = base64_encode("'':$password");
        $headers[] = "Authorization: Basic $credentials";
    }

    if (strtoupper($method) === 'GET' && !empty($params)) {
        $queryString = http_build_query($params);
        $endpoint .= '?' . $queryString;
    }

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($headers, ['Content-Type: application/json']));

    switch (strtoupper($method)) {
        case 'POST':
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            break;
        case 'PUT':
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_PUT, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            break;
    }

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = 'Error:' . curl_error($ch);
        curl_close($ch);
        return $error;
    }
    curl_close($ch);

    return $response;

}