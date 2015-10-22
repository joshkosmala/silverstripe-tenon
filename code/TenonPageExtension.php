<?php

class TenonPageExtension extends DataExtension {

	private static $db = array(
		'TenonCheckOnSave' => 'Boolean'
	);

	private static $has_many = array(
		'TenonResults' => 'TenonResult'
	);

	/*
	 * async determines whether calls to tenon are done asynchonronously or not.
	 * If true (default), tenon is invoked via a controller after shutdown, in
	 * a subprocess. If false, it is done in a sub-process but synchronously.
	 * Ideally the framework would have an interface for executing "subprocesses",
	 * which would have options for linux sub-processes or queued jobs etc.
	 *
	 * @config
	 * @var array
	 */
	private static $async = true;

	// Add an "Accessibility" tab to each page, which shows the grid of results.
	public function updateCMSFields(FieldList $fields) {

		Requirements::javascript('tenon/javascript/tenon_cms.js');

		$config = GridFieldConfig_RelationEditor::create();

		$resultsGrid = new GridField(
			'TenonResults',
			'Results',
			$this->owner->TenonResults()->sort('LastEdited desc'),
			$config
		);

		$fields->addFieldsToTab("Root.Accessibility", array(
			$resultsGrid
		));

		$fields->addFieldToTab("Root.Accessibility", new CheckboxField("TenonCheckOnSave", "Run Tenon checks automatically on save", $this->owner->TenonCheckOnSave));
	}

	// A flag to ensure tenon is not invoked more than once per request,
	private static $_tenon_invoked = false;

	// Which page ID we are checking.
	private static $tenonCheckPage;

	function onAfterWrite() {
		parent::onAfterWrite();

		// // not strictly necessary, this is a guard against a single request
		// // doing a write twice within the same request. Simply because the
		// // call to tenon is expensive.
		// if (self::$_tenon_invoked) {
		// 	return;
		// }
		// self::$_tenon_invoked = true;

		// // If we're going to send this to tenon, we'll register this function
		// // to execute on shutdown. This will initiate the process to
		// // send to tenon.
		// self::$tenonCheckPage = $this->owner;

		// // Trigger invoking tenon.
		// if (self::$async) {
		// 	register_shutdown_function(array(__CLASS__, "invoke_tenon"));
		// } else {
		// 	self::invoke_tenon();
		// }
		// Post save Tenon check is only run if the TenonCheckOnSave is true
		if ($this->owner->TenonCheckOnSave) {
			// not strictly necessary, this is a guard against a single request
			// doing a write twice within the same request. Simply because the
			// call to tenon is expensive.
			if (self::$_tenon_invoked) {
				return;
			}
			self::$_tenon_invoked = true;

			// If we're going to send this to tenon, we'll register this function
			// to execute on shutdown. This will initiate the process to
			// send to tenon.
			self::$tenonCheckPage = $this->owner;

			// Trigger invoking tenon.
			if (self::$async) {
				register_shutdown_function(array(__CLASS__, "invoke_tenon"));
			} else {
				self::invoke_tenon();
			}
 		}
	}

	// Invoke a request to tenon. This is done by creating a sub-process that
	// invokes TenonProcess/analysePage, which does the work. This is necessary
	// because if we call Director::test() within this CMS page save
	// request, it gets totally confused, and renders all pages with the generic
	// framework Controller.ss template. By seperating, we avoid CMS context
	// interferring with the front end page generation.
	static function invoke_tenon() {
		$exec = Director::getAbsFile("framework/sake");
		$cmd = $exec . ' TenonProcessor/analysePage/' . self::$tenonCheckPage->ID;
		$s = `$cmd &`;

		// If $s is anything other than ok, we'll create a TenonResult against this page, so at least the error shows up somewhere.
		if (trim($s) != "ok") {
			TenonResult::createError("Error: $s", self::$tenonCheckPage);
		}
	}
}
