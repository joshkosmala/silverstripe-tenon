<?php

class TenonPageExtension extends DataExtension {

    public function onBeforeWrite() {
//die("Yeow");
      //Debug::message("Extension");die();
   //   console.log("hello");
      $link = $this->owner->Link();
$source = Director::test($link, null, null, 'GET');
//console.log($source);
//Debug::show($source);die();
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
