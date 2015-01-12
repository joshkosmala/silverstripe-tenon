<?php
/**
 * Adds the Tenon API Key field to the Settings tab
 *
 * @package silverstripe-tenon
 * @author josh@novaweb.co.nz
 */
class TenonConfig extends DataExtension {

    private static $db = array(
        'TenonURL' => 'Varchar(255)',
        'TenonAPIKey' => 'Varchar(32)',
        'TenonCertainty' => 'Int',
        'TenonWCAGLevel' => 'Varchar(32)',
        'TenonPriority' => 'Int'
    );

    private static $defaults = array(
        'TenonURL' => 'http://www.tenon.io/api/',
        'TenonCertainty' => 60,
        'TenonWCAGLevel' => 'AAA',
        'TenonPriority' => 20
    );
    /*
    function extraStatics($class = NULL, $extension = NULL) {
        return array(
            'defaults' => array(
                'TenonURL' => 'http://www.tenon.io/api/',
                'TenonCertainty' => 60,
                'TenonWCAGLevel' => 'AAA',
                'TenonPriority' => 20
            )
        );
    }

    public function populateDefaults() {
        $this->TenonURL = 'http://www.tenon.io/api/';
        $this->TenonAPIKey = '';
        $this->TenonCertainty = 60;
        $this->TenonWCAGLevel = 'AAA';
        $this->TenonPriority = 20;
        parent::populateDefaults();
    }
    */
    public function updateCMSFields(FieldList $fields) {
        $fieldURL = new TextField("TenonURL", "Tenon API URL");
        $fieldURL->setDescription('The full URL to the Tenon URL');
        $fields->addFieldToTab("Root.Tenon", $fieldURL);

        $fieldAPIKey = new TextField("TenonAPIKey", "Tenon API Key");
        $fieldAPIKey->setDescription('Get your API key from www.tenon.io');
        $fields->addFieldToTab("Root.Tenon", $fieldAPIKey);

        $fieldCertainty = DropdownField::create(
            'TenonCertainty',
            'Tenon Certainty Threshold',
            array(
                0 => '0 - Report everything',
                20 => '20',
                40=> '40',
                60 => '60',
                80 => '80',
                100 => '100 - Report only the most certain'
            ), 60
        );
        $fieldCertainty->setDescription('Set the certainty threshold for your results');
        $fields->addFieldToTab("Root.Tenon", $fieldCertainty);

        $fieldWCAG = DropdownField::create(
            'TenonWCAGLevel',
            'Tenon WCAG Level',
            array(
                'AAA' => 'AAA - Run all tests',
                'AA' => 'AA - Run only AA and A tests',
                'A' => 'A - Run A tests only'
            ), 'AAA'
        );
        $fieldWCAG->setDescription('Web Content Accessibility Guidelines level');
        $fields->addFieldToTab("Root.Tenon", $fieldWCAG);

        $fieldPriority = DropdownField::create(
            'TenonPriority',
            'Tenon Priority Cut-off',
            array(
                0 => '0 - Report all issues regardless of priority',
                20 => '20',
                40=> '40',
                60 => '60',
                80 => '80',
                100 => '100 - Report only the highest priority issues'
            ), 20
        );
        $fieldPriority->setDescription('The priority cut-off for Tenon analysis');
        $fields->addFieldToTab("Root.Tenon", $fieldPriority);
    }
}
