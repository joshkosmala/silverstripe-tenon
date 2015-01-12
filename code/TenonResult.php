<?php
/**
 * A single Tenon result
 *
 * @package silverstripe-tenon
 * @author josh@novaweb.co.nz
 */
class TenonResult extends DataObject implements PermissionProvider {

    private static $db = array(
        'Title' => 'Varchar(255)',
        'PageURL' => 'Varchar(255)',
        'ResultType' => "Enum('Error, Warning, Script, Failure')",
        'Timestamp' => 'SS_Datetime',
        'PageDensity' => 'Percentage',
        'ErrorTitle' => 'Varchar(255)',
        'Description' => 'Text',
        'Snippet' => 'Text',
        'Location' => 'Varchar(255)',
    );

    private static $singular_name = 'Tenon Result';

    private static $indexes = array(
        'PageURL' => true,
        'ResultType' => true,
        'Timestamp' => true,
        'PageDensity' => true
    );

    private static $summary_fields = array(
        'PageURL',
        'ResultType',
        'PageDensity',
        'ErrorTitle',
        'Description'
    );

    private static $searchable_fields = array(
        'PageURL',
        'ResultType',
        'ErrorTitle',
        'Description'
    );

    public function getCMSFields() {
        //SS_Log::log("TenonResult.getCMSFields", SS_Log::NOTICE);
        $fields = parent::getCMSFields();
        $fields->removeByName('Title');


        $fieldResultType = $fields->fieldByName('Root.Main.ResultType');
        $fieldResultType
            ->setDescription('&middot; &nbsp; <strong>Error</strong> and <strong>Warning</strong> are accessibility issues<br />'
                .'&middot; &nbsp; <strong>Script</strong> is a javascript problem on the page that prevents a full assessment<br />'
                .'&middot; &nbsp; <strong>Failure</strong> indicates the requested analysis could not be completed');
        $fieldResultType->setTitle('Result type');

        $fieldPageDensity = $fields->fieldByName('Root.Main.PageDensity');
        $fieldPageDensity->setDescription('Errors (not warnings) as a percentage of the page content');
        $fieldPageDensity->setTitle('Page density %');

        $fieldErrorTitle = $fields->fieldByName("Root.Main.ErrorTitle");
        $fieldErrorTitle->setTitle("Error title");

        return $fields;
    }

    public function providePermissions() {
        return array();
    }

    public function canCreate($member = null) {
        return false;
    }

    public function canDelete($member = null) {
        return false;
    }

    public function canEdit($member = null) {
        return false;
    }

    public function canView($member = null) {
        return true;
    }

}
