<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Serve question type files
 *
 * @since      2.0
 * @package    filter
 * @subpackage chart
 * @copyright  2014 onwards Carl LeBlond 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

class filter_chart extends moodle_text_filter {
    

    public function filter($text, array $options = array()) {
        global $CFG, $USER;
        //$str = '<table>test</table><table class="chart someOtherClass">content</table>';
        $search = '/<div.*?class="eo_chart".*?chart="(.*?)".*?type="(.*?)".*?group="(.*?)".*?readonly="(.*?)".*?uid="(.*?)">(.*?)<\/div>/';
        //$numofmatches = preg_match_all($search, $text, $matches);
        $id = 1;

        $newtext = preg_replace_callback($search, function($matches) use (&$id) {
        global $CFG, $DB;    

        $result = $DB->get_record('filter_chart', array('id'=>$matches[1]));

print_object($matches);
$script = '<script>
	var chart;
	window.onload = function(){
	chart =  new dhtmlXChart({
		view:"'.$result->type.'",
                //view:"bar",
		color:"#66ccff",
		//gradient:"3d",
		container:"chart_container",
                xValue: "#data0#",
	        value:"#data1#",
		//label:"#data0#",
                yAxis:{
                title:"Value B"
                },
                xAxis:{
                title:"Value A"
                },
	        item:{
                   radius:5,
                   borderColor:"#f38f00",
                   borderWidth:1,
                   color:"#ff9600",
                   type:"d",
                   shadow:true
                },
                tooltip:{
                  template:"(#data0# , #data1#)"
                },
		//radius:3,
		//width:50,
		//origin:0,
		//yAxis:{
		//	start:-1000,
		//	step: 500,
		//	end:2000	
		//},
		//group:{
		//	by:"#data2#",
		//	map:{
		//		data0:["#data0#","sum"]
		//	}
		//},
		//xAxis:{
		//	template:"#id#"
		//},
		border:false
	});
	
	function refresh_chart(){
		chart.clearAll();
		chart.parse(mygrid,"dhtmlxgrid");
	};
	
	mygrid = new dhtmlXGridObject(\'gridbox\');
        mygrid.setHeader("X1,Y1,X2,Y2,X3,Y3,X4,Y4,X5,Y5");
        mygrid.setInitWidths("75,75,75,75,75,75,75,75,75,75")
	mygrid.setImagePath(\''.$CFG->wwwroot.'/filter/chart/codebase/imgs/\');
	mygrid.setSkin("dhx_skyblue")
	mygrid.enableSmartRendering(true);
        mygrid.init();
        mygrid.loadXML("'.$CFG->wwwroot.'/filter/chart/get.php?id='.$matches[1].'",refresh_chart);
	//mygrid.loadXML("'.$CFG->wwwroot.'/filter/chart/gridH.xml",refresh_chart);
	mygrid.attachEvent("onEditCell",function(stage){
		if (stage == 2)
			refresh_chart();
		return true;
	});

        myDataProcessor = new dataProcessor("'.$CFG->wwwroot.'/filter/chart/update_all.php?chartid='.$matches[1].'"); //lock feed url
	myDataProcessor.setTransactionMode("POST",true); //set mode as send-all-by-post
	myDataProcessor.setUpdateMode("off"); //disable auto-update
	myDataProcessor.init(mygrid); //link dataprocessor to the grid




    }
</script>
	<div id="chart_container" style="width:600px;height:300px;"></div>
	<div id="gridbox" style="width:600px; height:170px; background-color:white;"></div>

       <p><a href="javascript:void(0)" onclick="mygrid.addRow((new Date()).valueOf(),[0,\'\',\'\',\'\',false,\'na\',false,\'\'],mygrid.getRowIndex(mygrid.getSelectedId()))">Add row</a></p>
				<p><a href="javascript:void(0)" onclick="mygrid.deleteSelectedItem()">Remove Selected Row</a></p>
				<input type="button" name="some_name" value="update" onclick="myDataProcessor.sendData();">





';

            return $script;
        }, $text, -1, $count);

//echo "spreadinit=".$count;
    if ($count !==0) {
    $onload = '<script type="text/javascript">window.onload = function() {';
//    $onload = '';
    for ($i=1; $i<$id; $i++) {
        $onload .= 'func'.$i.'();';
    } 
    $onload .= '}</script>';
    $newtext = $onload.$newtext; 
    }
    $script = '<link rel="stylesheet" href="'.$CFG->wwwroot.'/filter/chart/codebase/dhtmlx.css">
                       <script src="'.$CFG->wwwroot.'/filter/chart/codebase/dhtmlx.js"></script>';
    //$script = '<script src="'.$CFG->wwwroot.'/filter/chart/codebase/dhtmlx.js"></script>';
    return $script.$newtext;
    }
}

