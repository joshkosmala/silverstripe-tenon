<?php

class TenonProcessor extends Controller {

   private static $allowed_actions = array(
          'analyse',
          'index'
      );

      public function index(SS_HTTPRequest $request) {
          // ..
      }


   function init() {

   }

   private function analyse($link) {

      $tenon = TenonConfig::get()->first();
      Debug::show($tenon);die();
      // Initialise cURL
     $curlObj = curl_init();
     curl_setopt($curlObj, CURLOPT_URL, $this->tenon_url);
     curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
     curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($curlObj, CURLOPT_POST, true);
     curl_setopt($curlObj, CURLOPT_FAILONERROR, true);
     curl_setopt($curlObj, CURLOPT_POSTFIELDS, $tenon_options);
     curl_setopt($curlObj,CURLOPT_HTTPHEADER, array('Expect: '));
     // Execute post, get results, close connection
     $data = curl_exec($curlObj);
     $code = curl_getinfo($curlObj, CURLINFO_HTTP_CODE);
     curl_close($curlObj);
     // Evaluate response
    /* $out = ($code === 200);
     if ($out){
        $this->tenon_response = json_decode($data);
        return $out;
     } else */



   }



}
