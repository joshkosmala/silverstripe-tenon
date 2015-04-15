<?php

class TenonPageExtension extends SiteTreeExtension {

    public function onAfterPublish(&$original) {
        $link = $this->owner->Link();
        //Debug::message($link);die();
        $source = Director::test($link, null, null, 'GET');
        Debug::message($source);die();
        $processor = new TenonProcessor();
        $processor->analyse();
    }

}