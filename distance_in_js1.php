

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "myhmsdb";

?>
<?php
$conn = new mysqli($servername, $username, $password, $dbname);
$patient = $_SESSION['pid'];
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT username, lat, lng FROM doctb";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  $num = ($result->num_rows);
  $row = $result->fetch_assoc();
  for ($i = 0; $i < $num; $i++){
      $doc_lat[] = $row["lat"];
      $doc_lng[] = $row["lng"];
      $row = $result->fetch_assoc();
  }
  for ($i = 0; $i < $num; $i++){
}

}

else {


}

?>




<script>
var spage = '<?php echo $row["lat"] ;?>';
var spage = 's';

</script>

<?php 
$conn -> close();
?>
<?php
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT fname, lname, lat, lng FROM patreg";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  $num = ($result->num_rows);
  $row = $result->fetch_assoc();

  for ($i = 0; $i < $num; $i++){
      $pat_lat[] = $row["lat"];
      $pat_lng[] = $row["lng"];
      $row = $result->fetch_assoc();
  }

  for ($i = 0; $i < $num; $i++){
}
}

else {
}
$conn->close();
?>








<?php
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT lat, lng FROM patreg WHERE pid='$patient'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$pat_lat = $row["lat"];
$pat_lng = $row["lng"];
?>

<script>
var spage = '<?php echo $row["lat"] ;?>';
var spage = 's';
var pat_lat_js = "<?php echo $pat_lat; ?>";
var pat_lng_js = "<?php echo $pat_lng; ?>";
var doc_lat_js = <?php echo json_encode($doc_lat); ?>;
var doc_lng_js = <?php echo json_encode($doc_lng); ?>;


 for(let i = 0; i < doc_lat_js.length; i++){ 
    //console.log(doc_lat_js[i]);
   // document.write(doc_lat_js[i]);
    //document.write("<br>");
    
    }
 for(let i = 0; i < doc_lng_js.length; i++){ 
    console.log(doc_lat_js[i]);
    document.write(doc_lng_js[i]);
   document.write("<br>");
    
    }

 
</script>

<?php 
$conn -> close();
?>

<script>
</script>

<script type="text/javascript">
function distance(lat1, lon1, lat2, lon2, unit) {
        var radlat1 = Math.PI * lat1/180;
        var radlat2 = Math.PI * lat2/180;
        var radlon1 = Math.PI * lon1/180;
        var radlon2 = Math.PI * lon2/180;
        var theta = lon1-lon2;
        var radtheta = Math.PI * theta/180;
        var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
        dist = Math.acos(dist);
        dist = dist * 180/Math.PI;
        dist = dist * 60 * 1.1515;
        if (unit=="K") { dist = dist * 1.609344 };
        if (unit=="N") { dist = dist * 0.8684 };
        return dist;
}
var size = doc_lat_js.length;
var distance_km = new Array(size);

for (var i = 0; i <= size; i++) {

    Number((15.46974).toFixed(2))
    distance_km[i] = Number(distance(doc_lat_js[i], doc_lng_js[i], pat_lat_js, pat_lng_js, 'K').toFixed(6));
  }



</script>



<script>

function createVariables(){
  var distance = [];

  for (var i = 0; i <= size; i++) {
      distance[i] = distance(doc_lat_js[i], doc_lng_js[i], pat_lat_js, pat_lng_js, 'K');
  }

  return distance;
}



for(let i = 0; i < size; i++)
{ 
}
</script>














