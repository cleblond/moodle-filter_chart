<?php
define('AJAX_SCRIPT', true);

if (!isset($CFG)) {
    require_once("../../config.php");
}

global $CFG, $DB, $USER;
require_once($CFG->libdir . '/filelib.php');
$id  = optional_param('id', 0, PARAM_INT);
$grid  = optional_param('grid', '', PARAM_ALPHA);

error_reporting(E_ALL ^ E_NOTICE);

//include db connection settings
//change this setting according to your environment
//require_once('../../common/config.php');
//require_once('../../common/config_dp.php');

//include XML Header (as response will be in xml format)
header("Content-type: text/xml");
//encoding may be different in your case
echo('<?xml version="1.0" encoding="utf-8"?>'); 

//start output of data
//print_r($points[1]);
//print_r($points[2]);
echo '<rows id="0">';

//output data from DB as XML
//$sql = "SELECT  * from filter_chart_data WHERE chartid=1";

if ($grid == 'data') {
        $points = $DB->get_records('filter_chart_data', array('chartid'=>$id));
        //$res = mysql_query ($sql);

        if($points){
                foreach ($points as $point) {
                        //create xml tag for grid's row
                        echo ("<row id='".$point->id."'>");
                        print("<cell><![CDATA[".$point->x1."]]></cell>");
                        print("<cell><![CDATA[".$point->y1."]]></cell>");
                        print("<cell><![CDATA[".$point->x2."]]></cell>");
                        print("<cell><![CDATA[".$point->y2."]]></cell>");
                        print("<cell><![CDATA[".$point->x3."]]></cell>");
                        print("<cell><![CDATA[".$point->y3."]]></cell>");
                        print("<cell><![CDATA[".$point->x4."]]></cell>");
                        print("<cell><![CDATA[".$point->y4."]]></cell>");
                        print("<cell><![CDATA[".$point->x5."]]></cell>");
                        print("<cell><![CDATA[".$point->y5."]]></cell>");
                        print("</row>");
                }
        }
} else {
        $points = $DB->get_records('filter_chart_users', array('id'=>$id));
        //$res = mysql_query ($sql);
        //print_r($points);
                

            echo ('<head>');
            echo ('<column type="co"> Chart type');
        if ($points[1]->type == "line"  or $points[1]->type == "spline"){
            echo ('<option value="line"> line </option>');
            echo ('<option value="spline"> spline </option>');
            } else {
            echo ('<option value="scatter"> scatter </option>');
            echo ('<option value="bar"> bar </option>');
            echo ('<option value="barH"> barH </option>');
            echo ('<option value="pie"> pie </option>');
            echo ('<option value="pie3D"> pie3D </option>');
            echo ('<option value="donut"> donut </option>');
            }
        echo ('</column>');
        echo ('<column type="ed"> Title');
        echo ('</column>');
        echo ('<column type="ed"> X-axis title');
        echo ('</column>');
        echo ('<column type="ed"> Y-axis title');
        echo ('</column>');
        echo ('<column type="ed"> Options');
        echo ('</column>');
echo ('</head>');


        if($points){
                foreach ($points as $point) {
                        //create xml tag for grid's row
                        echo ("<row id='".$point->id."'>");
                        print("<cell><![CDATA[".$point->type."]]></cell>");
                        print("<cell><![CDATA[".$point->title."]]></cell>");
                        print("<cell><![CDATA[".$point->xaxistitle."]]></cell>");
                        print("<cell><![CDATA[".$point->yaxistitle."]]></cell>");
                        //$options = explode(":" , $point->options);
                        //print_r($options);
                        print("<cell><![CDATA[".$point->chartoptions."]]></cell>");
                        print("</row>");
                        
                       echo ("<row id='".($point->id + 1)."'>");
                        print("<cell type=\"ed\"><![CDATA[".$point->series1."]]></cell>");
                        print("<cell><![CDATA[".$point->series2."]]></cell>");
                        print("<cell><![CDATA[".$point->series3."]]></cell>");
                        print("<cell><![CDATA[".$point->series4."]]></cell>");
                        print("<cell><![CDATA[".$point->series5."]]></cell>");
                        print("</row>");



                }
        }



}


echo '</rows>';



?>
