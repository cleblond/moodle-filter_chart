<?php


define('AJAX_SCRIPT', true);

if (!isset($CFG)) {
    require_once("../../config.php");
}

global $CFG, $DB, $USER;
require_once($CFG->libdir . '/filelib.php');
//$chartid  = optional_param('id', 0, PARAM_INT);

error_reporting(E_ALL ^ E_NOTICE);

function add_row($rowId, $chartid){
	global $newId, $DB;
	


		$record = new stdClass();
		//$record->id = $id;
		$record->chartid = $chartid;
		$record->x1 = optional_param($rowId."_c0", 0, PARAM_TEXT);
		$record->y1 = optional_param($rowId."_c1", 0, PARAM_TEXT);
                $newid = $DB->insert_record('filter_chart_data', $record, true);


/*

	$sql = 	"INSERT INTO samples_grid(sales,title,author,price,instore,shipping,bestseller,pub_date)
			VALUES ('".$_POST[$rowId."_c0"]."',
					'".addslashes($_POST[$rowId."_c1"])."',
					'".addslashes($_POST[$rowId."_c2"])."',
					'".$_POST[$rowId."_c3"]."',
					'".$_POST[$rowId."_c4"]."',
					'".$_POST[$rowId."_c5"]."',
					'".$_POST[$rowId."_c6"]."',
					'".$_POST[$rowId."_c7"]."')";
	$res = mysql_query($sql);
	//set value to use in response
	$newId = mysql_insert_id();  */
	return "insert";	
}

function update_row($rowId, $chartid){
                global $DB;

		$record = new stdClass();
		//$record->id = $id;
                $record->id = $rowId;
		$record->chartid = $chartid;
		$record->x1 = optional_param($rowId."_c0", '', PARAM_TEXT);
		$record->y1 = optional_param($rowId."_c1", '', PARAM_TEXT);
		$record->x2 = optional_param($rowId."_c2", '', PARAM_TEXT);
		$record->y2 = optional_param($rowId."_c3", '', PARAM_TEXT);
		$record->x3 = optional_param($rowId."_c4", '', PARAM_TEXT);
		$record->y3 = optional_param($rowId."_c5", '', PARAM_TEXT);
		$record->x4 = optional_param($rowId."_c6", '', PARAM_TEXT);
		$record->y4 = optional_param($rowId."_c7", '', PARAM_TEXT);
		$record->x5 = optional_param($rowId."_c8", '', PARAM_TEXT);
		$record->y5 = optional_param($rowId."_c9", '', PARAM_TEXT); 
                print_r($record);

                //$DB->insert_record('filter_chart_data', $record, true);

		if($DB->update_record('filter_chart_data', $record)){
                ///success
                } else {
                //failure
                echo "Database updated failed";
                }
	
	return "update";	
}

function delete_row($rowId, $chartid){
	global $DB;
        //$record->id = $rowId;
	//$record->chartid = $chartid;

        $DB->delete_records('filter_chart_data', array('id'=>$rowId, 'chartid'=>$chartid));
	//$d_sql = "DELETE FROM samples_grid WHERE book_id=".$rowId;
	//$resDel = mysql_query($d_sql);
	return "delete";
}


//include XML Header (as response will be in xml format)
header("Content-type: text/xml");
//encoding may differ in your case
echo('<?xml version="1.0" encoding="utf-8"?>');
//output update results
echo "<data>";

$ids  = optional_param('ids', '', PARAM_TEXT);
$chartid  = optional_param('chartid', 0, PARAM_INT);
//echo "chartid=$chartid";
$ids = explode("," , $ids);
//$ids = explode(",",$_POST["ids"]);
//for each row
//echo "ids=".print_r($ids);

for ($i=0; $i < sizeof($ids); $i++) { 
	$rowId = $ids[$i]; //id or row which was updated
        echo "rowId=".$rowId;
	$newId = $rowId; //will be used for insert operation	
	//$mode = $_POST[$rowId."_!nativeeditor_status"]; //get request mode
        $mode = optional_param($rowId."_!nativeeditor_status", 0, PARAM_TEXT);
        //echo "mode=$mode";
	switch($mode){
		case "inserted":
			//row adding request
			$action = add_row($rowId, $chartid);
		break;
		case "deleted":
			//row deleting request
			$action = delete_row($rowId, $chartid);
		break;
		default:
			//row updating request
			$action = update_row($rowId, $chartid);
		break;
	}	
	echo "<action type='".$action."' sid='".$rowId."' tid='".$newId."'/>";
	
}

echo "</data>";

?>
