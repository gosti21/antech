<?php

return [
    /*
        |--------------------------------------------------------------------------
        | Credenciales para busqueda en sunat y reniec
        |--------------------------------------------------------------------------
        */
    'search_apis_net' => [
        'base_url' => env('BASE_URL_APIS_NET'),
        'token' => env('TOKEN_APIS_NET'),
    ],

    /*
        |--------------------------------------------------------------------------
        | Credenciales para nubefact (emision de boletas o facturas)
        |--------------------------------------------------------------------------
        */
    'nubefact' => [
        'base_url' => env('NUBEFACT_URL'),
        //Para ventas online (a4)
        'token_online' => env('NUBEFACT_TOKEN_ONLINE'),
        'serie_boleta_online' => env('NUBEFACT_SERIE_BOLETA_ONLINE'),
        'serie_factura_online' => env('NUBEFACT_SERIE_FACTURA_ONLINE'),
        //Para ventas en tienda (ticket)
        'token_store' => env('NUBEFACT_TOKEN_STORE'),
        'serie_boleta_store' => env('NUBEFACT_SERIE_BOLETA_STORE'),
        'serie_factura_store' => env('NUBEFACT_SERIE_FACTURA_STORE'),
    ],

    'billing' => [
        'provider' => env('BILLING_PROVIDER', 'nubefact'),
    ],

    'sunat' => [
        'base_url' => env('SUNAT_API_URL'),
        'emit_endpoint' => env('SUNAT_API_EMIT_ENDPOINT', '/invoices'),
        'token' => env('SUNAT_API_TOKEN'),
        'ruc' => env('SUNAT_RUC_EMISOR'),
        'sol_user' => env('SUNAT_SOL_USER'),
        'sol_pass' => env('SUNAT_SOL_PASS'),
        'serie_boleta_online' => env('SUNAT_SERIE_BOLETA_ONLINE', 'B001'),
        'serie_factura_online' => env('SUNAT_SERIE_FACTURA_ONLINE', 'F001'),
        'serie_boleta_store' => env('SUNAT_SERIE_BOLETA_STORE', 'B002'),
        'serie_factura_store' => env('SUNAT_SERIE_FACTURA_STORE', 'F002'),
    ],

    'greenter' => [
        'ruc' => env('SUNAT_RUC_EMISOR'),
        'sol_user' => env('SUNAT_SOL_USER'),
        'sol_pass' => env('SUNAT_SOL_PASS'),
        'cert_path' => env('SUNAT_CERT_PATH'),
        'cert_pass' => env('SUNAT_CERT_PASS'),
        'environment' => env('SUNAT_ENVIRONMENT', 'beta'),
        'razon_social' => env('SUNAT_RAZON_SOCIAL', env('APP_NAME', 'EMPRESA')),
        'nombre_comercial' => env('SUNAT_NOMBRE_COMERCIAL', env('APP_NAME', 'EMPRESA')),
        'ubigeo' => env('SUNAT_UBIGEO', '150101'),
        'departamento' => env('SUNAT_DEPARTAMENTO', 'LIMA'),
        'provincia' => env('SUNAT_PROVINCIA', 'LIMA'),
        'distrito' => env('SUNAT_DISTRITO', 'LIMA'),
        'direccion' => env('SUNAT_DIRECCION', 'SIN DIRECCION'),
        'serie_boleta_online' => env('SUNAT_SERIE_BOLETA_ONLINE', 'B001'),
        'serie_factura_online' => env('SUNAT_SERIE_FACTURA_ONLINE', 'F001'),
        'serie_boleta_store' => env('SUNAT_SERIE_BOLETA_STORE', 'B002'),
        'serie_factura_store' => env('SUNAT_SERIE_FACTURA_STORE', 'F002'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Credenciales del Niubiz
    |--------------------------------------------------------------------------
    */
    'niubiz' => [
        'url_api' => env('NIUBIZ_URL_API'),
        'merchant_id' => env('NIUBIZ_MERCHANT_ID'),
        'user' => env('NIUBIZ_USER'),
        'password' => env('NIUBIZ_PASSWORD'),
        'force_approved' => env('NIUBIZ_FORCE_APPROVED', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Url de mi IA
    |--------------------------------------------------------------------------
    */
    'ia' => [
        'url_ia' => env('IA_API_URL'),
    ]
];
