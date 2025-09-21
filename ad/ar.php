<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>


<?php 
ini_set('display_errors', '1');
$curl = curl_init();

$key="x-api-key: fAFIvM5cbH6OwaoKH6J5K1KbdKQRHesg63rSMhyj";

$reg="yd17 pln";


curl_setopt_array($curl, array(
  CURLOPT_URL => "https://driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS =>"{\n\t\"registrationNumber\": \"$reg\"\n}",
  CURLOPT_HTTPHEADER => array(
    "$key",
    "Content-Type: application/json"
  ),
));


$response = curl_exec($curl);

curl_close($curl);

print $response;







$nested_object='['.'{"registrationNumber":"YD17PLN","co2Emissions":162,"engineCapacity":1984,"markedForExport":false,"fuelType":"PETROL","motStatus":"Valid","revenueWeight":1965,"colour":"WHITE","make":"AUDI","typeApproval":"M1","yearOfManufacture":2017,"taxDueDate":"2021-11-07","taxStatus":"Untaxed","dateOfLastV5CIssued":"2021-11-15","motExpiryDate":"2023-06-02","wheelplan":"2 AXLE RIGID BODY","monthOfFirstRegistration":"2017-03"}'.']';

;

$array = json_decode(json_encode($nested_object), true);
print($array);
?>
</body>
</html>