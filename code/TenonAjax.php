<?php

class TenonAjax extends Controller {

    const
        DO_SS_LOG = true;

    protected
        $hash_object = null,
        $tenon_page = '',
        $tenon_hash = '',
        $tenon_response = array(),
        $tenon_url = '';

    private static $allowed_actions = array(
        'analyse' => true
    );

    public function init() {
        parent::init();
    }

    /**
     * Ajax call from browser
     * @param SS_HTTPRequest $request sent by browser
     * @return string json response to send to back to the browser
     */
    public function analyse(SS_HTTPRequest $request) {
        // Set the tenon options
        $tenon_options = $this->buildOptions($request);

        // Store the page and create a hash of its contents
        $this->tenon_page = $request->postVar('tURL');
        $this->tenon_hash = $this->createHash($request);
        $this->log("TenonAjax.requestTenon", "url=".$this->tenon_url.", options=".print_r($tenon_options, true));

        // If the page/hash combination has not already been checked, do it now
        if (!$this->existingPageHash()) {
            if ($this->requestSend($tenon_options) && $this->responseSave() && $this->savePageHash()) {
                $out = $this->jsonResponse(true);
                $this->log("TenonAjax.analyse", "out=$out");
                return $out;
            }
        }
        return $this->jsonResponse(false);
    }

    /**
     * Called by analyse function
     * @param $request POST data from the web page
     * @return array Tenon API -ready parameters
     */
    private function buildOptions($request){
        $out = array();

        // Create an array with the values populated by the Javascript
        $params = array(
            'tURL',
            'level',
            'certainty',
            'priority',
            'docID',
            'systemID',
            'reportID',
            'viewPortHeight',
            'viewPortWidth',
            'uaString',
            'importance',
            'ref',
            'importance',
            'fragment',
            'store'
        );
        foreach ($request->postVars() AS $key => $value) {
            if (in_array($key, $params)) {
                if (strlen(trim($value)) > 0)
                    $out[$key] = $value;
            }
        }

        // Rename the URL parameter
        $out['url'] = $out['tURL'];
        unset($out['tURL']);

        // Update these options with those set in TenonConfig
        $config = SiteConfig::current_site_config();
        $out['key'] = $config->TenonAPIKey;
        $out['certainty'] = $config->TenonCertainty;
        $out['level'] = $config->TenonWCAGLevel;
        $out['priority'] = $config->TenonPriority;
        $this->tenon_url = $config->TenonURL;

        // Return the options
        return $out;
    }

    /**
     * Called by analyse function
     * @param $request is page HTML
     * @return string is hash
     */
    private function createHash($request){
        return md5(serialize($request->postVar('src')));
    }

    /**
     * Called by analyse function
     * Checks whether there's an existing page in the PageHash table with the same hash
     * @return bool true if a matching entry exists
     */
    private function existingPageHash(){
        $this->hash_object = TenonHash::get()->filter(array(
            'Page' => $this->tenon_page
        ))->First();
        $this->log("TenonAjax.existingPageHash", "result=".print_r($this->hash_object, true));
        return (isset($this->hash_object) && $this->hash_object->exists() && $this->hash_object->getField('Hash') === $this->tenon_hash);
    }

    /**
     * Called by analyse function
     * @param $value boolean to send back to browser
     * Note that the browser does nothing with these values at this time, but we want to change that in the future
     * @return string json encoded success value
     */
    private function jsonResponse($value){
        $this->response->addHeader('Content-Type', 'application/json');
        $data = array();
        $data['success'] = $value;
        $out = json_encode($data);
        return $out;
    }

    /**
     * Utility function to log events if DO_SS_LOG is set
     * @param $where
     * @param $what
     */
    private function log($where = '', $what = ''){
        if (self::DO_SS_LOG)
            SS_Log::log("$where: $what", SS_Log::NOTICE);
    }

    /**
     * Called by analyse function
     * @param $tenon_options sent to Tenon API as is
     * @return bool if the HTTP response = 200
     */
    private function requestSend($tenon_options){
        $this->log("TenonAjax.requestSend", "options=".print_r($tenon_options, true).", url=".$this->tenon_url);

        // Initialise cURL
        $curlObj = curl_init();
        curl_setopt($curlObj, CURLOPT_URL, $this->tenon_url);
        curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlObj, CURLOPT_POST, true);
        curl_setopt($curlObj, CURLOPT_FAILONERROR, true);
        curl_setopt($curlObj, CURLOPT_POSTFIELDS, $tenon_options);

        // Execute post, get results, close connection
        $data = curl_exec($curlObj);
        $code = curl_getinfo($curlObj, CURLINFO_HTTP_CODE);
        curl_close($curlObj);

        // Evaluate response
        $this->log("TenonAjax.requestSend", "code=".$code." data=".print_r($data, true));
        $out = ($code === 200);
        if ($out)
            $this->tenon_response = json_decode($data);
        return $out;
    }

    /**
     * Called by analyse function
     * @return bool whether data has been saved or not
     */
    private function responseSave(){
        $saved = false;
        if ($this->responseSaveCount() > 0)
            $this->responseSaveDeleteExisting();
        // Process each of the response types in turn
        if ($this->responseSaveFailure($saved)) {
            $this->responseSaveScript($saved);
            $this->responseSaveErrorWarning($saved);
        }
        return $saved;
    }

    /**
     * Called by responseSave function
     * @return int number of entries to be saved to the TenonResult table
     */
    private function responseSaveCount(){
        $total = ((int)$this->tenon_response->status === 200) ? 0 : 1;
        $total += (isset($this->tenon_response->resultSet)) ? count($this->tenon_response->resultSet) : 0;
        $total += (isset($this->tenon_response->clientScriptErrors)) ? count($this->tenon_response->clientScriptErrors) : 0;
        $this->log("TenonAjax.responseSaveCount", "total=$total");
        return $total;
    }

    /**
     * Called by responseSave function
     * Deletes all existing matches for the current path in the TenonResult table
     */
    private function responseSaveDeleteExisting(){
        /*
        $query = new SQLQuery();
        $query->setDelete(true);
        $query->setFrom('TenonResult');
        $query->setWhere('PageURL = ' . $this->tenon_page);
        $query->execute();
        $this->log("TenonAjax.responseSaveDeleteExisting", "query=".$query->sql());
        */
        $datalist = TenonResult::get()->filter(array(
           'PageURL' => $this->tenon_page
        ));
        $this->log("TenonAjax.responseSaveDeleteExisting", "deleteCount=".$datalist->count());
        foreach ($datalist as $dataitem)
            $dataitem->delete();
    }

    /**
     * Called by responseSave function
     * Saves any Errors and Warnings to the database
     * @param $saved set to true if data is saved
     */
    private function responseSaveErrorWarning(&$saved){
        if (count($this->tenon_response->resultSet) > 0){
            foreach($this->tenon_response->resultSet as $rsItem){
                $result = new TenonResult();
                $timestamp = new SS_DateTime();
                $timestamp->setValue(date('Y-m-d H:i:s'));
                $result->setField('PageURL', $this->tenon_page);
                $result->setField('Timestamp', $timestamp);
                $result->setField('PageDensity', $this->tenon_response->resultSummary->errorDensity / 100);
                $result->setField('ErrorTitle', $rsItem->errorTitle);
                $result->setField('Title', $this->tenon_page . ' ' . $rsItem->errorTitle);
                $result->setField('Description', $rsItem->errorDescription);
                $result->setField('Snippet', $rsItem->errorSnippet);
                $result->setField('Location', 'line: ' . $rsItem->position->line . ', column: ' . $rsItem->position->column);
                $result->setField('ResultType', 'Error');
                $result->write();
            }
            $saved = true;
        }
        $this->log("TenonAjax.responseSaveErrorWarning", "count=" . count($this->tenon_response->resultSet));
    }

    /**
     * Called by responseSave function
     * Saves any internal Tenon failure to the database
     * @param $saved set to true if data is saved
     * @return bool true if there is no Tenon internal error
     */
    private function responseSaveFailure(&$saved){
        $out = ((int)$this->tenon_response->status === 200);
        $this->log("TenonAjax.responseSaveFailure", "status=".$this->tenon_response->status.", out=$out");
        if (!$out){
            $result = new TenonResult();
            $timestamp = new SS_DateTime();
            $timestamp->setValue(date('Y-m-d H:i:s'));
            $result->setField('PageURL', $this->tenon_page);
            $result->setField('ResultType', 'Failure');
            $result->setField('Timestamp', $timestamp);
            //$result->setField('PageDensity', );
            $result->setField('ErrorTitle', 'HTTP status ' . $this->tenon_response->status);
            $result->setField('Title', $this->tenon_page . ' HTTP status: ' . $this->tenon_response->status);
            $result->setField('Description', $this->tenon_response->message);
            //$result->setField('Snippet', '');
            //$result->setField('Location', '');
            $result->write();
            $saved = true;
        }
        return $out;
    }

    /**
     * Called by responseSave function
     * Saves any client script errors to the database
     * @param $saved set to true if data is saved
     */
    private function responseSaveScript(&$saved){
        $this->log("TenonAjax.responseSaveScript", "count=".count($this->tenon_response->clientScriptErrors));
        if (count($this->tenon_response->clientScriptErrors) > 0){
            foreach($this->tenon_response->clientScriptErrors as $csError){
                $result = new TenonResult();
                $timestamp = new SS_DateTime();
                $timestamp->setValue(date('Y-m-d H:i:s'));
                $result->setField('PageURL', $this->tenon_page);
                $result->setField('ResultType', 'Script');
                $result->setField('Timestamp', $timestamp);
                if (isset($this->tenon_response->resultSummary->errorDensity))
                    $result->setField('PageDensity', $this->tenon_response->resultSummary->errorDensity / 100);
                $result->setField('Title', $this->tenon_page . 'script error');
                $result->setField('ErrorTitle', 'Script error');
                $result->setField('Description', $csError->message);
                if (isset($csError->stacktrace)) {
                    $stFiles = $this->responseSaveScriptCollate($csError->stacktrace, 'file');
                    $stFunctions = $this->responseSaveScriptCollate($csError->stacktrace, 'function');
                    $stLines = $this->responseSaveScriptCollate($csError->stacktrace, 'line');
                    $result->setField('Snippet', "$stFiles<br />$stFunctions");
                    $result->setField('Location', "$stLines");
                }
                $result->write();
            }
            $saved = true;
        }
        $this->log("TenonAjax.responseSaveScript", "count=".count($this->tenon_response->clientScriptErrors));
    }

    /**
     * Called by responseSaveScript
     * Concatenates stacktrace elements where there's more than one
     * @param $stacktraces $this->tenon_response->clientScriptErrors->stacktrace
     * @param $item "file", "function" or "line"
     * @return string
     */
    private function responseSaveScriptCollate($stacktraces, $item){
        $mult = count($stacktraces) > 1;
        $pos = 1;
        $out = "$item" . (($mult) ? "s: " : ": ");
        foreach($stacktraces as $trace){
            switch($item){
                case "file":
                    $atom = $trace->file;
                    break;
                case "function":
                    $atom = $trace->function;
                    break;
                case "line":
                    $atom = $trace->line;
                    break;
                default:
                    $atom = '';
                    $this->log("TenonAjax.responseSaveScriptCollate", "Unexpected item: $item");
            }
            $out .= (($mult) ? "[$pos] " : "");
            $out .= $atom . "; ";
            $pos++;
        }
        $out = substr($out, 0, strlen($out) - 2);
        $this->log("TenonAjax.responseSaveScriptCollate", "stacktraces=".count($stacktraces).", out=".$out);
        return $out;
    }

    /**
     * Called by analyse function
     * @return bool if PageHash is saved successfully
     */
    private function savePageHash(){
        $this->log("TenonAjax.savePageHash", "page=".$this->tenon_page.", hash=".$this->tenon_hash.", hashobjectset=".isset($this->hash_object));
        if (isset($this->hash_object)) {
            if (!$this->hash_object->exists())
                $this->hash_object->setField('Page', $this->tenon_page);
            $this->hash_object->setField('Hash', $this->tenon_hash);
            $this->hash_object->write();
        }
        else{
            $hash = new TenonHash();
            $hash->setField("Page", $this->tenon_page);
            $hash->setField("Hash", $this->tenon_hash);
            $hash->write();
        }
        return true;
    }

}
