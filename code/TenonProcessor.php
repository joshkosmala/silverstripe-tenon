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


      public static function analyse($link) {

      $tenon = SiteConfig::current_site_config();
      //Debug::show($tenon);die();
      $tenon_options['key'] = $tenon->TenonAPIKey;
      $tenon_options['url'] = $link;

      // Initialise cURL
     $curlObj = curl_init();
     curl_setopt($curlObj, CURLOPT_URL, $tenon->TenonURL);
     //curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
     curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($curlObj, CURLOPT_POST, true);
     curl_setopt($curlObj, CURLOPT_FAILONERROR, true);
     curl_setopt($curlObj, CURLOPT_POSTFIELDS, $tenon_options);
     //curl_setopt($curlObj,CURLOPT_HTTPHEADER, array('Expect: '));
     // Execute post, get results, close connection
     $data = curl_exec($curlObj);
     $code = curl_getinfo($curlObj, CURLINFO_HTTP_CODE);
     curl_close($curlObj);



     // Evaluate response
     if($code === 200){
        // Turn JSON response in to php array

       //Debug::show($data);die();
       $results = json_decode($data, true);

       //Debug::show($result);
       //die();

       // add foreach here, iterate through the array creating new tenon results for each row in the array

       // this should be inside the foreach

       foreach($results['resultSet'] as $result){
          Debug::message($result['errorTitle']);die();
          $tenonResult = new TenonResult();
          $tenonResult->Title = $result['errorTitle'];
          $tenonResult->Snippet = $result['errorSnippet'];
          // set the fields
          $tenonResult->write();
       }

     } else {
        Debug::message("Tenon analyse didn't work. Are you behind a firewall? You need to be on a server connected to the internet. Perhaps your API key has expired or you forgot to fill it out?");
        die();
     }
   }



}
