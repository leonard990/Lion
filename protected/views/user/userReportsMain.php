<?php
$this->pageTitle = Yii::app()->name . ' - Reports';
$this->breadcrumbs = array(
    'Reports',
);
?>

<h1>Main reports site</h1>
<p>Here you can find all the sensor you have privilege to see. On the next page you will be able to chose from 2 different report types. Daily report, which consists of
the data from only one day. Monthly report, which consist of the data from a certain month. Both options also have ability to provide subscription. If you subscribe
to a sensor, you will get reports on daily/monthly basis.</p>
<br/>
<br/>

<?php
Yii::import('application.extensions.EGMap.*');

$gMap = new EGMap();
$gMap->setHeight(400);
$gMap->setWidth(600);

$gMap->zoom = 7;
$mapTypeControlOptions = array(
  'position'=> EGMapControlPosition::LEFT_BOTTOM,
  'style'=>EGMap::MAPTYPECONTROL_STYLE_DROPDOWN_MENU
);

$gMap->mapTypeControlOptions= $mapTypeControlOptions;
//centar je zagreb
$gMap->setCenter(45.817, 15.983);

$all_user_sensors = Yii::app()->db->createCommand()
            ->selectDistinct('s.*')
            ->from('di_sensors s')
            ->join('di_gsn_privileges p', 'p.gsn_id=s.gsn_id')
            ->where('p.user_id=:id', array(':id'=>Yii::app()->user->id))
            ->queryAll();

foreach ($all_user_sensors as $sensor)
{
                        $info_window_a = new EGMapInfoWindow('<div>Sensor name '.$sensor['sensor_user_name'].'</div>');
    // Create marker for every sensor user can see
                    $icon = new EGMapMarkerImage("http://mapicons.nicolasmollet.com/wp-content/uploads/mapicons/shape-default/color-128e4d/shapecolor-color/shadow-1/border-dark/symbolstyle-white/symbolshadowstyle-dark/gradient-no/water.png");

                    $icon->setSize(22,22);
                    $icon->setAnchor(16, 16.5);
                    $icon->setOrigin(0, 0);

    $marker = new EGMapMarker($sensor['location_y'],$sensor['location_x'], array('title' => "Sensor ".$sensor['sensor_user_name'],'icon' => $icon));

    $marker->addHtmlInfoWindow($info_window_a);
    $gMap->addMarker($marker);
}

// enabling marker clusterer just for fun
// to view it zoom-out the map
//$gMap->enableMarkerClusterer(new EGMapMarkerClusterer());

$gMap->renderMap();

?>