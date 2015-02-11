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
 * Filter converting URLs in the text to HTML links
 *
 * @package    filter
 * @subpackage chart
 * @copyright  2014 onwards Carl LeBlond
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
class filter_chart extends moodle_text_filter {
    protected static $globalconfig;
    /**
     * Apply the filter to the text
     *
     * @see filter_manager::apply_filter_chain()
     * @param string $text to be processed by the text
     * @param array $options filter options
     * @return string text after processing
     */
    public function filter($text, array $options = array()) {
        global $CFG, $PAGE, $chartconfigured;
        $search  = '/<div.*?class="eo_chart (.*?)".*?<\/div>/';
        $pattern = '/.*?spreadsheet.php\.*?/';
        $newtext = preg_replace_callback($search, array(
            $this,
            'filter_chart_callback'
        ), $text);
        if (($newtext != $text) && !isset($chartconfigured)) {
            //echo "filter_chart";
            $chartconfigured = true;
            $css             = '<link rel="stylesheet" href="' . $CFG->wwwroot . '/filter/chart/codebase/dhtmlx.css">';
            $dhtmlxmods      = "<script type='text/javascript'>
            //Y.on('load', function () {
            YUI().applyConfig({
                modules: {
                    'dhtmlxcommon': {
                    fullpath: M.cfg.wwwroot + '/filter/chart/codebase/dhtmlxcommon.js'
                    },
                    'dhtmlxchart': {
                    fullpath: M.cfg.wwwroot + '/filter/chart/codebase/dhtmlxchart.js'
                    },";
            if (!preg_match($pattern, $text)) {
                $dhtmlxmods .= "'dhtmlxgrid': {
                fullpath: M.cfg.wwwroot + '/filter/chart/codebase/dhtmlxgrid.js'
                },";
            } else {
                $dhtmlxmods .= "'dhtmlxgrid': {
                fullpath: M.cfg.wwwroot + '/filter/chart/codebase/dhtmlxdataprocessor.js'
                },";
            }
            $dhtmlxmods .= "}
            });
         //});
        </script>";
            $newtext = $css . $dhtmlxmods . $newtext;
        }
        return $newtext;
    }
    private function filter_chart_callback(array $matches) {
        global $CFG, $DB, $USER;
        $result  = $DB->get_record('filter_chart_users', array(
            'id' => $matches[1]
        ));
        $hidediv = '';
        $color   = array(
            "red",
            "yellow",
            "green",
            "blue",
            "black"
        );
        $marker  = array(
            "c",
            "s",
            "t",
            "b",
            "x"
        );
        $ro      = "";
        ///don't allow editing, add legend and hide the options and data grids if not creator
        if ($USER->id !== $result->userid) {
            $hidediv       = 'style="display: none;"';
            //Build up legend text for ro mode
            $seriesvisible = explode("~", $result->chartoptions);
            $rolegend      = "";
            for ($i = 1; $i <= 5; $i++) {
                //echo $seriesvisible[$i-1];
                if ($seriesvisible[$i - 1] == "true") {
                    $leg = "series" . $i;
                    $rolegend .= '{text:"' . $result->$leg . '",color:"' . $color[$i - 1] . '"},';
                }
            }
            //add all series text
            $addroseries = '';
            $j           = 2;
            if ($result->type == "scatter") {
                for ($i = 2; $i <= 5; $i++) {
                    if ($seriesvisible[$i - 1] == "true") {
                        $addroseries .= 'charttype.addSeries({
                    xValue: "#data' . $j . '#",
                    value: "#data' . ($j + 1) . '#",
                                        line:{color:"' . $color[$i - 1] . '"},
                    item:{
                    radius:3,
                        type:"' . $marker[$i - 1] . '",
                    borderWidth:2,
                    color:"' . $color[$i - 1] . '"}
                    });';
                        $j = $j + 2;
                    }
                }
            } else {
                for ($i = 2; $i <= 5; $i++) {
                    if ($seriesvisible[$i - 1] == "true") {
                        $addroseries .= 'charttype.addSeries({
                    //xValue: "#data' . $j . '#",
                    value: "#data' . ($i) . '#",
                                        line:{color:"' . $color[$i - 1] . '"},
                    item:{
                    radius:3,
                        type:"' . $marker[$i - 1] . '",
                    borderWidth:2,
                    color:"' . $color[$i - 1] . '"}
                    });';
                        $j = $j + 2;
                    }
                }
            }
            $addseries = $addroseries;
            $legend    = $rolegend;
            $ro        = "ro";
        } else {
            //Build up legend text all curves
            $legend = "";
            for ($i = 1; $i <= 5; $i++) {
                $leg = "series" . $i;
                $legend .= '{text:"' . $result->$leg . '",color:"' . $color[$i - 1] . '"},';
            }
            //add all series text
            $addseries = '';
            $j         = 2;
            if ($result->type == "scatter") {
                for ($i = 2; $i <= 5; $i++) {
                    $addseries .= 'charttype.addSeries({
                xValue: "#data' . $j . '#",
                value: "#data' . ($j + 1) . '#",
                                line:{color:"' . $color[$i - 1] . '"},
                item:{
                radius:3,
                type:"' . $marker[$i - 1] . '",
                borderWidth:2,
                color:"' . $color[$i - 1] . '"}
                });';
                    $j = $j + 2;
                }
            } else {
                for ($i = 2; $i <= 5; $i++) {
                    $addseries .= 'charttype.addSeries({
                //xValue: "#data' . $j . '#",
                value: "#data' . ($i) . '#",
                                line:{color:"' . $color[$i - 1] . '"},
                item:{
                radius:3,
                type:"' . $marker[$i - 1] . '",
                borderWidth:2,
                color:"' . $color[$i - 1] . '"}
                });';
                    $j = $j + 2;
                }
            }
        }
        //Take care of pie type graphs.
        $pietypes = array(
            "pie",
            "pie3D",
            "donut"
        );
        $bartype  = '';
        $pietype  = '';
        if (in_array($result->type, $pietypes)) {
            $pietype = "pie";
            $type    = "pie";
        }
        $bartypes = array(
            "bar",
            "barH"
        );
        if (in_array($result->type, $bartypes)) {
            $bartype = "bar";
        }
        $linetype  = '';
        $linetypes = array(
            "line",
            "spline",
            "scatter"
        );
        if (in_array($result->type, $linetypes)) {
            $linetype = "line";
        }
        $pre = '       <table>
        <tr><td style="text-align: center;"><b>' . $result->title . '</b></td></tr>
        <tr><td><div id="chart_container" style="width:600px;height:300px;"></div></td></tr></table>
        <div ' . $hidediv . '>
        <button id="toggle" >Show/Hide</button><br>
        <div id="chartoptions" >
        <input type="button" name="some_name" value="Save" onclick="myDataProcessor.sendData();myDataProcessorFG.sendData();">
        <table>
        <tr><td><div id="gridboxuser" style="width:600px; height:95px; background-color:white; float:left;"></div></td></tr>
        <tr><td><div id="gridboxdata" style="width:600px; height:170px; background-color:white; float:left;"></div></td></tr>
        </table>
        <p><a href="javascript:void(0)" onclick="mygrid.addRow((new Date()).valueOf(),[\'\',\'\',\'\',\'\',\'\',\'\',\'\',\'\',\'\',\'\'],mygrid.getRowIndex(mygrid.getSelectedId())+1)">Add row</a>&nbsp;&nbsp;<a href="javascript:void(0)" onclick="mygrid.deleteSelectedItem()">Remove Selected Row</a></p>
        <input type="button" name="some_name" value="Save" onclick="myDataProcessor.sendData();myDataProcessorFG.sendData();">
        </div>
        </div>
        <script type="text/javascript">
    YUI().use(\'node\', function(Y) {
        Y.delegate(\'click\', function(e) {
        var buttonID = e.currentTarget.get(\'id\'),
            node = Y.one(\'#chartoptions\');
        if (buttonID === \'show\') {
            node.show();
        } else if (buttonID === \'hide\') {
            node.hide();
        } else if (buttonID === \'toggle\') {
            node.toggleView();
        }
        }, document, \'button\');
    });
    </script>
        <script>
    YUI().use(\'node\', \'dhtmlxcommon\', \'dhtmlxchart\', \'dhtmlxgrid\', function(Y) {
        function refresh_chart(){
                charttype.clearAll();
                charttype.parse(mygrid,"dhtmlxgrid");

                        //charttype.hideSeries(0);
        };
        function init_rochart(){
        charttype.parse(mygrid,"dhtmlxgrid");
        }
        function init_rochartline(){
        charttype.parse(mygrid,"dhtmlxgrid");
        }

        function init_chart(){
                charttype.parse(mygrid,"dhtmlxgrid");
                //charttype.hideSeries(0);
                //mygrid.hdr.rows[2].cells[0].firstChild.firstChild.checked = "false";
                cbxs = "' . $result->chartoptions . '";
                cbx = cbxs.split("~");
                //console.log(cbx);
                   if ("' . $linetype . '" == "line"){
                        j = 0;
            for (i = 0; i < cbx.length; i++) {

                //text += cars[i] + "<br>";
                            if (cbx[i]=="false"){
                              //console.log("init_chart");
                              charttype.hideSeries(i);
                              mygrid.hdr.rows[2].cells[j].firstChild.firstChild.checked = false;
                            }
                            j = j + 1;
            }
                   }
                 //       charttype.hideSeries(0);

        };

        function init_chartline(){

                charttype.parse(mygrid,"dhtmlxgrid");
                //mygrid.hdr.rows[2].cells[0].firstChild.firstChild.checked = "false";
                cbxs = "' . $result->chartoptions . '";
                cbx = cbxs.split("~");
                //console.log(cbx);

            for (i = 0; i < cbx.length; i++) {

                //text += cars[i] + "<br>";
                            if (cbx[i]=="false"){
                              charttype.hideSeries(i);
                              mygrid.hdr.rows[2].cells[i+1].firstChild.firstChild.checked = false;
                            }
            }
        };

        function doOnColorChanged(stage,rId,cIn){
                //console.log("HERERERE");
                if(stage==2){
                        if(cIn==2){
                                mygrid.cells(rId,3).setValue(mygrid.cells(rId,2).getValue())
                        }else if(cIn==3){
                                mygrid.cells(rId,2).setValue(mygrid.cells(rId,3).getValue())
                        }
                }
                return true;
        }

        function doOnCheck(rowId,cellInd,state){
                //console.log(myformgrid.cells(' . $matches[1] . ',4).getValue());

                if(state == 0) {
                charttype.hideSeries(cellInd/2);
        } else {
                charttype.showSeries(cellInd/2);
        }

                ///build up new options string
                j = 0;
                var options = "";
                for (i = 0; i < 5; i++) {
        options = options + "~" + mygrid.hdr.rows[2].cells[j].firstChild.firstChild.checked;
                //console.log(j);
                j = j + 1;
                }
                options=options.substring(1);
                //console.log(options);
                myformgrid.cells(' . $matches[1] . ',4).setValue(options);
        charttype.refresh();

                myDataProcessorFG.setUpdated(' . $matches[1] . ',"updated");
                myDataProcessorFG.sendData();


    }


        function doOnCheckline(rowId,cellInd,state){
                //console.log(myformgrid.cells(' . $matches[1] . ',4).getValue());
                //console.log(rowId+","+cellInd);
                if(state == 0) {
                charttype.hideSeries(cellInd-1);
                } else {
                charttype.showSeries(cellInd-1);
                }
                //charttype.refresh();

                ///build up new options string
                var j = 0;
                var options = "";
                for (i = 0; i < 5; i++) {
        options = options + "~" + mygrid.hdr.rows[2].cells[i+1].firstChild.firstChild.checked;
                //console.log(i);

                }
                options=options.substring(1);
                //console.log(options);
                myformgrid.cells(' . $matches[1] . ',4).setValue(options);
        //charttype.refresh();
                myDataProcessorFG.setUpdated(' . $matches[1] . ',"updated");
                myDataProcessorFG.sendData();

    }';
        switch ($result->type) {
            case "scatter":
                $script = '
        charttype =  new dhtmlXChart({
                view:"' . $result->type . '",
                color:"red",
                container:"chart_container",
                xValue: "#data0#",
                value:"#data1#",
                //label:"#data0#",  //Bar only
                yAxis:{
                title:"' . $result->yaxistitle . '"
                },
                xAxis:{
                title:"' . $result->xaxistitle . '",
                },
                legend:{
            layout:"y",
            align:"right",
            valign:"middle",
            width:120,
            toggle:true,
                         marker:{
                        type: "item"
                        },
            values:[' . $legend . ']},
                item:{
                   radius:5,
                  // borderColor:"red",
                   borderWidth:1,
                   color:"red",
                   type:"d",
                   shadow:true
                },
                tooltip:{
                  template:"(#data0# , #data1#)"
                },
                border:false
        });
        ' . $addseries . '
        mygrid = new dhtmlXGridObject(\'gridboxdata\');
        mygrid.setHeader("x1,y1,x2,y2,x3,y3,x4,y4,x5,y5");
        mygrid.setInitWidths("75,75,75,75,75,75,75,75,75,75");
        //mygrid.attachHeader("#master_checkbox,,#master_checkbox,,#master_checkbox,,#master_checkbox,,#master_checkbox");
        mygrid.attachHeader("#master_checkbox,#cspan,#master_checkbox,#cspan,#master_checkbox,#cspan,#master_checkbox,#cspan,#master_checkbox,#cspan");
        mygrid.setColTypes("ed,ed,ed,ed,ed,ed,ed,ed,ed,ed");
        mygrid.setColSorting("int,int,int,int,int,int,int,int,int,int");
        //mygrid.attachEvent("onCheckbox",doOnCheck);
        mygrid.setColumnColor("silver,silver,lightgrey,lightgrey,silver,silver,lightgrey,lightgrey,silver,silver");
        mygrid.checkAll(true);


        mygrid.setImagePath(\'' . $CFG->wwwroot . '/filter/chart/codebase/imgs/\');
        mygrid.setSkin("dhx_skyblue");

        mygrid.enableSmartRendering(true);
        mygrid.attachEvent("customMasterChecked", doOnCheck);
        mygrid.enableMultiselect(true);
        mygrid.enableBlockSelection(true);
        mygrid.forceLabelSelection(true);

        mygrid.init();
        mygrid.loadXML("' . $CFG->wwwroot . '/filter/chart/get.php?id=' . $matches[1] . '&grid=data",init_' . $ro . 'chart);
        mygrid.attachEvent("onEditCell",function(stage){
                if (stage == 2)
                        refresh_chart();
                return true;
        });

        var myformgrid = new dhtmlXGridObject(\'gridboxuser\');
        myformgrid.setInitWidths("75,75,150,150,75");
        myformgrid.setSkin("dhx_skyblue");
        myformgrid.init();
        myformgrid.loadXML("' . $CFG->wwwroot . '/filter/chart/get.php?id=' . $matches[1] . '&grid=user");

        myDataProcessor = new dataProcessor("' . $CFG->wwwroot . '/filter/chart/update.php?chartid=' . $matches[1] . '"); //lock feed url
        myDataProcessor.setTransactionMode("POST",true); //set mode as send-all-by-post
        myDataProcessor.setUpdateMode("off"); //disable auto-update
        myDataProcessor.init(mygrid); //link dataprocessor to the grid

        myDataProcessorFG = new dataProcessor("' . $CFG->wwwroot . '/filter/chart/updateform.php?chartid=' . $matches[1] . '"); //lock feed url
        myDataProcessorFG.setTransactionMode("POST",true); //set mode as send-all-by-post
        myDataProcessorFG.setUpdateMode("off"); //disable auto-update
        myDataProcessorFG.init(myformgrid); //link dataprocessor to the grid
//    }    ///end window.onload
    });   //end Y.use
</script>';
                break;
            case "line":
            case "spline":
                $script = '

        charttype =  new dhtmlXChart({
                view:"' . $result->type . '",
                color:"red",
                container:"chart_container",
                xValue: "#data0#",
                value:"#data1#",
                yAxis:{
                title:"' . $result->yaxistitle . '"
                },
                line:{
                     color:"red",
                },
                xAxis:{
                title:"' . $result->xaxistitle . '",
                template:"#data0#"
                },
                item:{
                radius:3,
                type:"s",
                borderWidth:1,
                color:"red"},
                legend:{
            layout:"y",
            align:"right",
            valign:"middle",
            width:120,
            toggle:true,
                        marker:{type: "item"},
            values:[' . $legend . ']},
              /*  tooltip:{
                  template:"(#data0# , #data1#)"
                }, */
                border:false
        });

        ' . $addseries . '

        //var charttype = chartscatter;

        mygrid = new dhtmlXGridObject(\'gridboxdata\');
        mygrid.setHeader("x1,y1,y2,y3,y4,y5,j,j,j,j");
        mygrid.setInitWidths("75,75,75,75,75,75,75,75,75,75");
        mygrid.attachHeader(",#master_checkbox,#master_checkbox,#master_checkbox,#master_checkbox,#master_checkbox,,,,");
        mygrid.setColTypes("ed,ed,ed,ed,ed,ed,ed,ed,ed,ed");
        mygrid.setColumnColor("grey,lightgrey,silver,lightgrey,silver,silver,silver,silver,silver,silver");
        mygrid.checkAll(true);
        mygrid.setImagePath(\'' . $CFG->wwwroot . '/filter/chart/codebase/imgs/\');
        mygrid.setSkin("dhx_skyblue");
        mygrid.enableSmartRendering(true);
        mygrid.attachEvent("customMasterChecked", doOnCheckline);
        mygrid.enableMultiselect(true);
        mygrid.enableBlockSelection(true);
        mygrid.forceLabelSelection(true);
        mygrid.init();
        mygrid.loadXML("' . $CFG->wwwroot . '/filter/chart/get.php?id=' . $matches[1] . '&grid=data",init_' . $ro . 'chartline);
        mygrid.attachEvent("onEditCell",function(stage){
                if (stage == 2)
                        refresh_chart();
                return true;
        });

        var myformgrid = new dhtmlXGridObject(\'gridboxuser\');
        myformgrid.setInitWidths("75,75,150,150,75");
        myformgrid.setSkin("dhx_skyblue");
        myformgrid.init();
        myformgrid.loadXML("' . $CFG->wwwroot . '/filter/chart/get.php?id=' . $matches[1] . '&grid=user");
       /*
        myformgrid.attachEvent("onEditCell",function(stage){
                if (stage == 2) {
                        //charttype.parse(myformgrid,"dhtmlxgrid");
                        //xtit = myformgrid.cells2(0,2).getValue();
                        //alert(xtit);
                        //console.log(charttype);
                        //console.log(charttype._configXAxis.title);
            //chart.clearAll();
            //chart.load("/data/test.json","json");
            //setTimeout(refreshchart,60000);
            //charttype._configXAxis.title = "NEW AXIS TITLE";
                        //charttype.clearAll();
                        //charttype.refresh();
            //console.log(charttype._configXAxis.title);

                        //xtit = charttype.update(123, { text:"abc", value:22 });
                        //alert(charttype.parse(myformgrid,"dhtmlxgrid"));
                        //refresh_chart();
                }
                return true;
        }); */

        myDataProcessor = new dataProcessor("' . $CFG->wwwroot . '/filter/chart/update.php?chartid=' . $matches[1] . '"); //lock feed url
        myDataProcessor.setTransactionMode("POST",true); //set mode as send-all-by-post
        myDataProcessor.setUpdateMode("off"); //disable auto-update
        myDataProcessor.init(mygrid); //link dataprocessor to the grid

        myDataProcessorFG = new dataProcessor("' . $CFG->wwwroot . '/filter/chart/updateform.php?chartid=' . $matches[1] . '"); //lock feed url
        myDataProcessorFG.setTransactionMode("POST",true); //set mode as send-all-by-post
        myDataProcessorFG.setUpdateMode("off"); //disable auto-update
        myDataProcessorFG.init(myformgrid); //link dataprocessor to the grid
//    }    ///end window.onload
    });   //end Y.use
</script>';
                break;
            case "barH";
            case "bar":
                $script = '
        chartbarh =  new dhtmlXChart({
                view:"' . $result->type . '",
                color:"#data2#",
                gradient:"rising",
                container:"chart_container",
                value:"#data1#",
                label:"#data1#",  //Bar only
                yAxis:{
                template:"#data0#",
                title:"' . $result->yaxistitle . '"
                },
                xAxis:{
                title:"' . $result->xaxistitle . '",
                //template:"#data0#"
                template:function(obj){
                    return (obj%20?"":obj)
                }

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
                view:"' . $result->type . '",
                color:"#data2#",
                gradient:"rising",
                container:"chart_container",
                value:"#data1#",
                label:"#data0#",  //Bar only
                yAxis:{
                title:"' . $result->yaxistitle . '"
                },
                xAxis:{
                title:"' . $result->xaxistitle . '",
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
        if (\'' . $result->type . '\' === \'barH\') {var charttype = chartbarh}else{charttype = chartbar;}
        mygrid = new dhtmlXGridObject(\'gridboxdata\');
        mygrid.setHeader("Bar Label,Bar Value, Color Code, Color");
        mygrid.setInitWidths("75, 75, 75, 75")
        mygrid.attachEvent("onEditCell",doOnColorChanged);
        mygrid.setColTypes("ed,ed,ed,cp");
        mygrid.setColSorting("str,str,str,str")
        mygrid.setSkin("dhx_skyblue");

        mygrid.enableSmartRendering(true);
        mygrid.enableMultiselect(true);
        mygrid.enableBlockSelection(true);
        mygrid.forceLabelSelection(true);

        mygrid.init();
        mygrid.loadXML("' . $CFG->wwwroot . '/filter/chart/get.php?id=' . $matches[1] . '&grid=data",init_chart);
        mygrid.attachEvent("onEditCell",function(stage){
                if (stage == 2)
                        refresh_chart();
                return true;
        });

        var myformgrid = new dhtmlXGridObject(\'gridboxuser\');
        myformgrid.setInitWidths("75,75,150,150,75");
        myformgrid.setSkin("dhx_skyblue");
        myformgrid.init();
        myformgrid.loadXML("' . $CFG->wwwroot . '/filter/chart/get.php?id=' . $matches[1] . '&grid=user");

        myDataProcessor = new dataProcessor("' . $CFG->wwwroot . '/filter/chart/update.php?chartid=' . $matches[1] . '"); //lock feed url
        myDataProcessor.setTransactionMode("POST",true); //set mode as send-all-by-post
        myDataProcessor.setUpdateMode("off"); //disable auto-update
        myDataProcessor.init(mygrid); //link dataprocessor to the grid

        myDataProcessorFG = new dataProcessor("' . $CFG->wwwroot . '/filter/chart/updateform.php?chartid=' . $matches[1] . '"); //lock feed url
        myDataProcessorFG.setTransactionMode("POST",true); //set mode as send-all-by-post
        myDataProcessorFG.setUpdateMode("off"); //disable auto-update
        myDataProcessorFG.init(myformgrid); //link dataprocessor to the grid
//    }    ///end window.onload
    });   //end Y.use

</script>';
                break;
            case "pie3D";
            case "donut";
            case "pie":
                $script = '
        chartpie =  new dhtmlXChart({
            view:"' . $result->type . '",
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
        var charttype = chartpie;
        mygrid = new dhtmlXGridObject(\'gridboxdata\');
        mygrid.setHeader("Slice Label,Slice Value, Color Code, Color");
        mygrid.setInitWidths("75, 75, 75, 75")
        mygrid.attachEvent("onEditCell",doOnColorChanged);
        mygrid.setColTypes("ed,ed,ed,cp");
        mygrid.setColSorting("str,str,str,str")
        mygrid.setSkin("dhx_skyblue");
        mygrid.enableMultiselect(true);
        mygrid.enableBlockSelection(true);
        mygrid.forceLabelSelection(true);

        mygrid.init();
        mygrid.loadXML("' . $CFG->wwwroot . '/filter/chart/get.php?id=' . $matches[1] . '&grid=data",init_chart);
        mygrid.attachEvent("onEditCell",function(stage){
                if (stage == 2)
                        refresh_chart();
                return true;
        });

        var myformgrid = new dhtmlXGridObject(\'gridboxuser\');
        myformgrid.setInitWidths("75,75,150,150,75");
        myformgrid.setSkin("dhx_skyblue");
        myformgrid.init();
        myformgrid.loadXML("' . $CFG->wwwroot . '/filter/chart/get.php?id=' . $matches[1] . '&grid=user");

        myDataProcessor = new dataProcessor("' . $CFG->wwwroot . '/filter/chart/update.php?chartid=' . $matches[1] . '"); //lock feed url
        myDataProcessor.setTransactionMode("POST",true); //set mode as send-all-by-post
        myDataProcessor.setUpdateMode("off"); //disable auto-update
        myDataProcessor.init(mygrid); //link dataprocessor to the grid

        myDataProcessorFG = new dataProcessor("' . $CFG->wwwroot . '/filter/chart/updateform.php?chartid=' . $matches[1] . '"); //lock feed url
        myDataProcessorFG.setTransactionMode("POST",true); //set mode as send-all-by-post
        myDataProcessorFG.setUpdateMode("off"); //disable auto-update
        myDataProcessorFG.init(myformgrid); //link dataprocessor to the grid
    //});    ///end Y.on(load)
    });   //end Y.use
</script>';
                break;
        }
        //print_object($pre.$script);
        return $pre . $script;
    } ///end callback
}
