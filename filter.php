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

        $result = $DB->get_record('filter_chart_users', array('id'=>$matches[1]));
        //echo $result->type;


        //Take care of pie type graphs.
        $pietypes = array("pie", "pie3D", "donut");
        $bartype='';
        $pietype='';
        $linetype='';
        if(in_array($result->type, $pietypes)){
        $pietype = "pie";
        }
        $bartypes = array("bar", "barH");
        if(in_array($result->type, $bartypes)){
        $bartype = "bar";
        }
        $linetypes = array("line", "spline");
        if(in_array($result->type, $linetypes)){
        $linetype = "line";
        }


        //print_object($matches);
$script = '<script>
	var chart;
	window.onload = function(){


	chartbarh =  new dhtmlXChart({
		view:"'.$result->type.'",
                //view:"bar",
		color:"#data2#",
                gradient:"rising",
		//gradient:"3d",
		container:"chart_container",
                //xValue: "#data0#",
	        value:"#data1#",
		label:"#data1#",  //Bar only
                yAxis:{
                template:"#data0#",
                title:"'.$result->yaxistitle.'"
                },
                xAxis:{
                title:"'.$result->xaxistitle.'",
                template:"#data0#"
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
		border:false
	});

	chartbar =  new dhtmlXChart({
		view:"'.$result->type.'",
                //view:"bar",
		color:"#data2#",
                gradient:"rising",
		//gradient:"3d",
		container:"chart_container",
                //xValue: "#data0#",
	        value:"#data1#",
		label:"#data0#",  //Bar only
                yAxis:{
                title:"'.$result->yaxistitle.'"
                },
                xAxis:{
                title:"'.$result->xaxistitle.'",
                template:"#data0#"
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
		border:false
	});

        chartpie =  new dhtmlXChart({
            view:"'.$result->type.'",
            container:"chart_container",
            value:"#data1#",
            color:"#data2#",
            tooltip:"#data1#",
            label:"#data0#",
            shadow:0,
            radius:65,
            x:280,
            y:120
        });

	chartscatter =  new dhtmlXChart({
		view:"scatter",
                //view:"bar",
		color:"#66ccff",
		//gradient:"3d",
		container:"chart_container",
                xValue: "#data0#",
	        value:"#data1#",
		//label:"#data0#",  //Bar only
                yAxis:{
                title:"'.$result->yaxistitle.'"
                },
                xAxis:{
                title:"'.$result->xaxistitle.'"
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
		border:false
	});
        
	chartline =  new dhtmlXChart({
		view:"'.$result->type.'",
                //view:"bar",
		color:"#66ccff",
		//gradient:"3d",
		container:"chart_container",
                xValue: "#data0#",
	        value:"#data1#",
		//label:"#data0#",  //Bar only
                yAxis:{
                title:"'.$result->yaxistitle.'"
                },
                xAxis:{
                title:"'.$result->xaxistitle.'"
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
		border:false
	});




        function refresh_chart(){
		charttype.clearAll();
		charttype.parse(mygrid,"dhtmlxgrid");
	};
	

        function doOnColorChanged(stage,rId,cIn){
		if(stage==2){
			if(cIn==2){
				mygrid.cells(rId,3).setValue(mygrid.cells(rId,2).getValue())
			}else if(cIn==3){
				mygrid.cells(rId,2).setValue(mygrid.cells(rId,3).getValue())
			}
		}
		return true;
	}




        if (\''.$result->type.'\' === \'scatter\') {
		//alert("scatter chart");
		//must be scatter
	var charttype = chartscatter;

	mygrid = new dhtmlXGridObject(\'gridbox\');
        mygrid.setHeader("x1,y1,x2,y2,x3,y3,x4,y4,x5,y5");
        mygrid.setInitWidths("75,75,75,75,75,75,75,75,75,75")
	mygrid.setImagePath(\''.$CFG->wwwroot.'/filter/chart/codebase/imgs/\');
	mygrid.setSkin("dhx_skyblue")
	mygrid.enableSmartRendering(true);

        mygrid.setColTypes("ed,ed,ed,ed,ed,ed,ed,ed,ed,ed");
	mygrid.setColSorting("int,int,int,int,int,int,int,int,int,int")





        } else if (\''.$bartype.'\' === \'bar\') {
  	      alert("bar chart");
		//must be bar
        if (\''.$result->type.'\' === \'barH\') {var charttype = chartbarh}else{charttype = chartbar;}

	mygrid = new dhtmlXGridObject(\'gridbox\');
        mygrid.setHeader("Bar Label,Bar Value, Color Code, Color");
        mygrid.setInitWidths("75, 75, 75, 75")
	mygrid.setImagePath(\''.$CFG->wwwroot.'/filter/chart/codebase/imgs/\');
	mygrid.setSkin("dhx_skyblue")
	mygrid.enableSmartRendering(true);
	mygrid.attachEvent("onEditCell",doOnColorChanged);
        mygrid.setColTypes("ed,ed,ed,cp");
	mygrid.setColSorting("str,str,str,str")


        } else if (\''.$pietype.'\' === \'pie\') {
        var charttype = chartpie;

	mygrid = new dhtmlXGridObject(\'gridbox\');
        mygrid.setHeader("Slice Label,Slice Value, Color Code, Color");
        mygrid.setInitWidths("75, 75, 75, 75")
	mygrid.setImagePath(\''.$CFG->wwwroot.'/filter/chart/codebase/imgs/\');
	mygrid.setSkin("dhx_skyblue")
	mygrid.enableSmartRendering(true);
	mygrid.attachEvent("onEditCell",doOnColorChanged);
        mygrid.setColTypes("ed,ed,ed,cp");
	mygrid.setColSorting("str,str,str,str")

        } else if (\''.$linetype.'\' === \'line\') {
		//alert("scatter chart");
		//must be scatter
	var charttype = chartline;

	mygrid = new dhtmlXGridObject(\'gridbox\');
        mygrid.setHeader("x1,y1,x2,y2,x3,y3,x4,y4,x5,y5");
        mygrid.setInitWidths("75,75,75,75,75,75,75,75,75,75")
	mygrid.setImagePath(\''.$CFG->wwwroot.'/filter/chart/codebase/imgs/\');
	mygrid.setSkin("dhx_skyblue")
	mygrid.enableSmartRendering(true);

        mygrid.setColTypes("ed,ed,ed,ed,ed,ed,ed,ed,ed,ed");
	mygrid.setColSorting("int,int,int,int,int,int,int,int,int,int")





        }




        mygrid.init();
        mygrid.loadXML("'.$CFG->wwwroot.'/filter/chart/get.php?id='.$matches[1].'",refresh_chart);
	//mygrid.loadXML("'.$CFG->wwwroot.'/filter/chart/gridH.xml",refresh_chart);
	mygrid.attachEvent("onEditCell",function(stage){
		if (stage == 2)
			refresh_chart();
		return true;
	});

        myDataProcessor = new dataProcessor("'.$CFG->wwwroot.'/filter/chart/update.php?chartid='.$matches[1].'"); //lock feed url
	myDataProcessor.setTransactionMode("POST",true); //set mode as send-all-by-post
	myDataProcessor.setUpdateMode("off"); //disable auto-update
	myDataProcessor.init(mygrid); //link dataprocessor to the grid
    }
</script>
	<div id="chart_container" style="width:600px;height:300px;"></div>
        <input type="text" name="title" value="Chart Tiele">
	<div id="gridbox" style="width:600px; height:170px; background-color:white;"></div>

       <p><a href="javascript:void(0)" onclick="mygrid.addRow((new Date()).valueOf(),[\'\',\'\',\'\',\'\',\'\',\'\',\'\',\'\',\'\',\'\'],mygrid.getRowIndex(mygrid.getSelectedId()))">Add row</a></p>
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

