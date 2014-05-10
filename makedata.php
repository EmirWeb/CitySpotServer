<?php



$torontoCode = array('lat'=>'43.7000','lng' =>'-79.4000','data'=>'greenPParking.json','url'=>'http://cityspot.org/img/activity_glass_background.png','name' => 'Toronto');
$ottawaCode = array('lat'=>'45.4214','lng' =>'-75.6919','data'=>'ottawaParking.json','url'=>'http://cityspot.org/img/activity_glass_background_ottawa.png','name' => 'Ottawa');
$sanfranCode = array('lat'=>'37.7833','lng'=>'-122.4167','data'=>'sf_onstreet_parking.json','url'=>'http://cityspot.org/img/activity_glass_background_sanfran.png','name' => 'San Francisco');



$cities = array($torontoCode,$ottawaCode,$sanfranCode);

// $string1 = file_get_contents($cities[2]['data']);
// $dataSet = json_decode($string1,true);
//

//$result = array_merge($dataSet,$dataSet2,$dataSet3);
//var_dump($result);
//Accepts php array loops through the list to find the top 5 closest parking lots

function searchGreenP($userLat,$userLong,$data){
  $printArr = Array();

  for($y = 0; $y < count($data) ; $y++){

    $getDis = distance($userLat, $userLong,floatval($data[$y]['lat']),floatval($data[$y]['lng']),"K");
    if($getDis < 250){
      $thisCity = $y;
      $string1 = file_get_contents($data[$thisCity]['data']);
      $dataSet = json_decode($string1,true);

      break;
    }
  }
  if(isset($thisCity) == 0){
    return '';
  }

    foreach($dataSet as $key){

      $thisLng = floatval($key['lng']);
      $thisLat = floatval($key['lat']);

      //$thisLng = $key['lng'];
      //$thisLat = $key['lat'];

      $getDis = distance($userLat, $userLong,$thisLat,$thisLng,"K");

      if ($getDis < 5){
        $key['url'] = $data[$thisCity]['url'];
        $key['name'] = $data[$thisCity]['name'];
        $key['getDis'] = $getDis;
        $printArr[] = $key;

        //$count++;
        // if($count > 25){
        //   break;
        // }
      }else{
        continue;
      }
    }

  return $printArr;
}


// messures distance between two points
function distance($lat1, $lon1, $lat2, $lon2, $unit) {

  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);

  if ($unit == "K") {
    return ($miles * 1.609344);
  } else if ($unit == "N") {
      return ($miles * 0.8684);
    } else {
        return $miles;
      }

}

function array_orderby()
{
    $args = func_get_args();
    $data = array_shift($args);
    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = array();
            foreach ($data as $key => $row)
                $tmp[$key] = $row[$field];
            $args[$n] = $tmp;
            }
    }
    $args[] = &$data;
    call_user_func_array('array_multisort', $args);
    return array_pop($args);
}


//Hardcoded user location data.
$userLat =$_GET['latitude'];//
$userLng =$_GET['longitude'];//

//Set up radius
//$closest='asdasdaadasdads'+1231231231231;
$closest = searchGreenP($userLat,$userLng,$cities);
$closest = array_orderby($closest, 'getDis', SORT_ASC);

//create silly json.stuff.
$jsonArr = Array();
$type= "greenParking";
//echo "your not crazy";
foreach($closest as $item){
  $jsonArr[] = Array('address' => $item['address'], 'lng' => $item['lng'], 'lat' => $item['lat'], 'rate_half_hour' => $item['rate_half_hour'], 'type'=> "greenParking", 'distance' => $item['getDis'],'url'=> $item['url'],'name'=>item['name']);
}
$jsonArr = json_encode($jsonArr);
echo $jsonArr;
//var_dump($jsonArr);






?>
