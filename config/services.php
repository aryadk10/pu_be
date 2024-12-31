<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'bjb' => [
        'username'          => env('EXTERNAL.BJB.CREDENTIAL.USERNAME'),
        'password'          => env('EXTERNAL.BJB.CREDENTIAL.PASSWORD'),
        'base_url'          => env('EXTERNAL.BJB.BASE_URL'),
        'endpoints'         => [
            'create_invoice' => env('EXTERNAL.BJB.ENDPOINTS.CREATE_INVOICE'),
            'create_qris'    => env('EXTERNAL.BJB.ENDPOINTS.CREATE_QRIS'),
            'get_payment'    => env('EXTERNAL.BJB.ENDPOINTS.GET_PAYMENT'),
        ],
        'departemen_kode'   => env('EXTERNAL.BJB.DEPARTEMEN_KODE'),
        'departemen_nama'   => env('EXTERNAL.BJB.DEPARTEMEN_NAMA'),
        'objek_kode'        => env('EXTERNAL.BJB.OBJEK_KODE'),
        'objek_nama'        => env('EXTERNAL.BJB.OBJEK_NAMA'),
        'objek_alamat1'     => env('EXTERNAL.BJB.OBJEK_ALAMAT1'),
        'objek_alamat2'     => env('EXTERNAL.BJB.OBJEK_ALAMAT2'),
        'produk_kode'       => env('EXTERNAL.BJB.PRODUK_KODE'),
        'produk_nama'       => env('EXTERNAL.BJB.PRODUK_NAMA'),
        'rekening_kode'     => env('EXTERNAL.BJB.REKENING_KODE'),
        'rekening_nama'     => env('EXTERNAL.BJB.REKENING_NAMA'),
        'pejabat_nm'        => env('EXTERNAL.BJB.PEJABAT_NM'),
        'subjek_kode'       => env('EXTERNAL.BJB.SUBJEK_KODE'),
        'subjek_nama'       => env('EXTERNAL.BJB.SUBJEK_NAMA'),
        'subjek_alamat1'    => env('EXTERNAL.BJB.SUBJEK_ALAMAT1'),
        'subjek_alamat2'    => env('EXTERNAL.BJB.SUBJEK_ALAMAT2'),
        'retry_count'       => env('EXTERNAL.BJB.RETRY_COUNT', 1),
        'retry_wait_time'   => env('EXTERNAL.BJB.RETRY_WAIT_TIME', 1),
        'timeout'           => env('EXTERNAL.BJB.TIME_OUT', 15),
    ],


];
