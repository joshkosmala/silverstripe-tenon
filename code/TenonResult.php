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
        'PageDensity' => 'Percentage',
        'ErrorTitle' => 'Varchar(255)',
        'Description' => 'Text',
        'Snippet' => 'Text',
        'Location' => 'Varchar(255)',
    );

    private static $has_one = array(
      'Page' => 'Page'
   );

    private static $singular_name = 'Tenon Result';

    private static $indexes = array(
        'PageURL' => true,
        'ResultType' => true,
        'Timestamp' => true,
        'PageDensity' => true
    );

    private static $summary_fields = array(
        'PageURL' => 'Page URL',
        'ResultType' => 'Result Type',
        'PageDensity.Nice' => 'Page Density',
        'ErrorTitle' => 'Error Title',
        'Description' => 'Description'
    );

    private static $searchable_fields = array(
        'PageURL',
        'ResultType',
        'ErrorTitle',
        'Description'
    );

    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName('Title');

        $fieldResultType = $fields->fieldByName('Root.Main.ResultType');
        $fieldResultType
            ->setDescription('&middot; &nbsp; <strong>Error</strong> and <strong>Warning</strong> are accessibility issues<br />'
                .'&middot; &nbsp; <strong>Script</strong> is a javascript problem on the page that prevents a full assessment<br />'
                .'&middot; &nbsp; <strong>Failure</strong> indicates the requested analysis could not be completed');
        $fieldResultType->setTitle('Result type');

        $fieldPageDensity = $fields->fieldByName('Root.Main.PageDensity');
        $fieldPageDensity->setDescription('<p>Errors (not warnings) as a percentage of the page content - multiply this value by 100</p>');
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
