<?php

class TenonPageExtension extends DataExtension {

    public function onBeforeWrite() {
//die("Yeow");
      //Debug::message("Extension");die();
   //   console.log("hello");
//   Debug::message("DIE");
//      die();
//$link = $this->owner->Link();
//Debug::message($link);
//$httpMethod = "GET";
//$source = Director::test($link, null, null, 'GET');
//Debug::message($source);die();
//console.log($source);
//Debug::show($source);die();
$source = "<img src='jpeg.jpg' />";

$processor = new TenonProcessor();
$processor->analyse($source);
   /*     $link = $this->owner->Link();
        //Debug::message($link);die();
        $source = Director::test($link, null, null, 'GET');
        Debug::message($source);die();
        $processor = new TenonProcessor();
        $processor->analyse(); */
    }

}
