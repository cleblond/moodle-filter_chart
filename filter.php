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
        $search = '/<div.*?class="eo_chart".*?sheet="(.*?)".*?math="(.*?)".*?group="(.*?)".*?readonly="(.*?)".*?uid="(.*?)">(.*?)<\/div>/';
        //$numofmatches = preg_match_all($search, $text, $matches);
        $id = 1;

        $newtext = preg_replace_callback($search, function($matches) use (&$id) {
        global $CFG;    
        

$script = '<script>
	var chart;
	window.onload = function(){
	chart =  new dhtmlXChart({
		view:"bar",
		color:"#66ccff",
		gradient:"3d",
		container:"chart_container",
	        value:"#data0#",
		label:"#data0#",
		radius:3,
		tooltip:{
			template:"#data0#"
		},
		width:50,
		origin:0,
		yAxis:{
			start:-1000,
			step: 500,
			end:2000	
		},
		group:{
			by:"#data2#",
			map:{
				data0:["#data0#","sum"]
			}
		},
		xAxis:{
			template:"#id#"
		},
		border:false
	});
	
	function refresh_chart(){
		chart.clearAll();
		chart.parse(mygrid,"dhtmlxgrid");
	};
	
	mygrid = new dhtmlXGridObject(\'gridbox\');
	mygrid.setImagePath(\'../../../codebase/imgs/\');
	mygrid.setSkin("dhx_skyblue")
	mygrid.enableSmartRendering(true);
	mygrid.loadXML("'.$CFG->wwwroot.'/filter/chart/gridH.xml",refresh_chart);
	mygrid.attachEvent("onEditCell",function(stage){
		if (stage == 2)
			refresh_chart();
		return true;
	});
    }
</script>
	<div id="chart_container" style="width:600px;height:300px;"></div>
	<div id="gridbox" style="width:600px; height:170px; background-color:white;"></div>
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

