<?php
date_default_timezone_set('Asia/Riyadh');

Route::get('test/sms', function () {
    $body = 'Mr.Fawzy
Beverly Clinics confirms your reservation no. xxx
at ' . date('h:i A');
    //dd(nl2br($body));
    dd(Functions::sendSMS('0561074511', ($body)));
});

Route::get('test/patient/get-national-id', function(){
    ini_set('max_execution_time', 0);
    Patient::getNationalIdIfNull();
    dd('finished');
});

Route::get('test/sms', function () {
    $body = 'Mr.Fawzy
SGH Riyadh confirms your reservation no. xxx
at ' . date('h:i A');
    //dd(nl2br($body));
    dd(Functions::sendSMS('0561074511', ($body)));
});

foreach (File::allFiles(__DIR__ . '/core/routes') as $route) {
    require_once $route->getPathname();
}
Route::group(array('prefix' => 'diagnosis'), function () {
    foreach (File::allFiles(__DIR__ . '/core/diagnosis/routes') as $route) {
        require_once $route->getPathname();
    }
});