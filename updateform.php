<?php


define('AJAX_SCRIPT', true);

if (!isset($CFG)) {
    require_once("../../config.php");
}

global $CFG, $DB, $USER;
require_once($CFG->libdir . '/filelib.php');
//$chartid  = optional_param('id', 0, PARAM_INT);

error_reporting(E_ALL ^ E_NOTICE);

//$title = optional_param("title", 0, PARAM_TEXT);
//echo "title=$title";


function update_row($chartid){
                global $DB;
                $rowId = $chartid; 
                //echo "rowid=".$rowId;
                $record = new stdClass();
                $record->id = $rowId;
                //$record->type = optional_param($rowId."_c0", '', PARAM_TEXT);
                //$record->title = optional_param($rowId."_c1", '', PARAM_TEXT);
                //$record->xaxistitle = optional_param($rowId."_c2", '', PARAM_TEXT);
                //$record->yaxistitle = optional_param($rowId."_c3", '', PARAM_TEXT);
                $record->chartoptions = optional_param($rowId."_c4",'', PARAM_TEXT);
                //print_r($record);

                //$DB->insert_record('filter_chart_data', $record, true);
                //$out=$DB->update_record('filter_chart_users', $record);
                //print_r($out);
                if($DB->update_record('filter_chart_users', $record, true)){
                ///success
                echo "success";
                } else {
                //failure
                echo "Database updated failed";
                }
        
        return "update";        
}




//include XML Header (as response will be in xml format)
header("Content-type: text/xml");
//encoding may differ in your case
echo('<?xml version="1.0" encoding="utf-8"?>');
//output update results
echo "<data>";

$ids  = optional_param('ids', '', PARAM_TEXT);
$chartid  = optional_param('chartid', 0, PARAM_INT);
//echo "ids=$ids";
//echo "chartid=$chartid";
//$ids = explode("," , $ids);


        $rowId = $ids; //id or row which was updated
        echo "rowId=".$rowId;
        $newId = $rowId; //will be used for insert operation        
        //$mode = $_POST[$rowId."_!nativeeditor_status"]; //get request mode
        $mode = optional_param($rowId."_!nativeeditor_status", 0, PARAM_TEXT);
        //echo "mode=$mode";
        
        $action = update_row($chartid);        
        echo "<action type='".$action."' sid='".$rowId."' tid='".$newId."'/>";
        


echo "</data>";

?>
