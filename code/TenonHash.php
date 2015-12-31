<?php

class TenonHash extends DataObject
{

    private static $db = array(
        'Page' => 'Varchar(255)',
        'Hash' => 'Varchar(255)',
    );

    private static $indexes = array(
        'Page' => true
    );
}
