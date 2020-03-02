<?php 

$con = mysqli_connect("localhost", "root", "", "tko");

// function for the first GRAPH
$sql = "SELECT Date,Pricepersft FROM `Propertyhk` ORDER BY Date";
$data = mysqli_query($con, $sql);

while($row = $data->fetch_array(MYSQLI_ASSOC)) {
	$rows[] = $row;
}

$output = [['Date','Pricepersft']];
$countArrayLength = count($rows);
for($i=0;$i<$countArrayLength;$i++){
		array_push($output, array());
        array_push($output[$i+1], $rows[$i]['Date']);
        array_push($output[$i+1], (float)$rows[$i]['Pricepersft']);
    } 

$source = json_encode($output);


//function for the second GRAPH
$sql0 ="SET @rownum := 0"; 
$sql1 = "SELECT Date, AVG(Pricepersft) as median FROM ( SELECT Date, Pricepersft, (SELECT count(*) FROM Propertyhk t2 WHERE t2.Date = t3.Date) as ct, seq, (SELECT count(*) FROM Propertyhk t2 WHERE t2.Date < t3.Date) as delta FROM (SELECT Date, Pricepersft, @rownum := @rownum + 1 AS seq FROM (SELECT * FROM Propertyhk) t1 ORDER BY Date) t3 CROSS JOIN (SELECT @rownum := 0) x HAVING (ct%2 = 0 and seq-delta between floor((ct+1)/2) and floor((ct+1)/2) +1) or (ct%2 <> 0 and seq-delta = (ct+1)/2) ) T GROUP BY YEAR(`Date`), MONTH(`Date`) ORDER BY Date" ;

$con->query($sql0);
$result1 = $con->query($sql1);

if ($result1->num_rows > 0) {
    // output data of each row
      while($row1 = $result1->fetch_assoc()) {
      $rows1[] = $row1;
      }    
  } else {
      echo "0 results";
    }

$output1 = [['Date','Monthly Average']];
$countArrayLength = count($rows1);
for($i=0;$i<$countArrayLength;$i++){
    array_push($output1, array());
        array_push($output1[$i+1], $rows1[$i]['Date']);
        array_push($output1[$i+1], (float)$rows1[$i]['median']);
    } 

$source1 = json_encode($output1);


//function for the third GRAPH
$sql2 = "SELECT Date,COUNT(Date) AS Transactions FROM Propertyhk GROUP BY YEAR(Date),MONTH(Date)";
$result2 = mysqli_query($con, $sql2);

if ($result2->num_rows > 0) {
    // output data of each row
      while($row2 = $result2->fetch_assoc()) {
      $rows2[] = $row2;
      }    
  } else {
      echo "0 results";
    }

$output2 = [['Date','Transactions Number']];
$countArrayLength = count($rows2);
for($i=0;$i<$countArrayLength;$i++){
    array_push($output2, array());
        array_push($output2[$i+1], $rows2[$i]['Date']);
        array_push($output2[$i+1], (float)$rows2[$i]['Transactions']);
    } 

$source2 = json_encode($output2);


//close connections
mysqli_close($con);

 ?>




<!DOCTYPE html>
 <html>
 <head>
	 <title>TKO Plaza Transactions</title>
	 <meta charset="UTF-8"/>
	 <link rel="stylesheet" href="stile.css?v={CURRENT_TIMESTAMP}" type="text/css"/>
	 <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable(<?= $source ?>);

        var options = {
          title: 'Tseung Kwan O Plaza - Historical Transaction Price per Sft',
          curveType: 'function',
          legend: { position: 'bottom' }
        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

        chart.draw(data, options);
      }
    </script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable(<?= $source1 ?>);

        var options = {
          title: 'Tseung Kwan O Plaza - Median Monthly Price per Sft',
          curveType: 'function',
          legend: { position: 'bottom' },
          pointSize: 5
        };

        var chart = new google.visualization.LineChart(document.getElementById('average_chart'));

        chart.draw(data, options);
      }
    </script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable(<?= $source2 ?>);

        var options = {
          title: 'Tseung Kwan O Plaza - Number of Transactions per Month',
          legend: { position: 'none' },
        };

        var chart = new google.visualization.BarChart(document.getElementById('trans_chart'));

        chart.draw(data, options);
      }
    </script>
 </head>
	 <body>
         <div>
			<h1>Tseung Kwan O Plaza Price Portal</h1>        
       </div>
        <div id="curve_chart" style="width: 900px; height: 500px"></div>
        <div id="average_chart" style="width: 900px; height: 500px"></div>
        <div id="trans_chart" style="width: 900px; height: 500px"></div>
        <div> </div>
        <div> All data is aggregated from and copyrighted by</div>
        <a href="https://www.property.hk"> Property.hk</a>
        <div> </div>
        <div> <i>
        Page was last updated on 
        </i>
        <i class= "inline" id="refresh"></i></div>
        <script type="text/javascript">
            var refresh = document.lastModified; 
             document.getElementById("refresh").innerHTML = refresh; 
        </script>
       	<form action="home.php"> <button type="submit">Display Source Data</button>   
        <div>
          <?php 
          $con = mysqli_connect("localhost", "root", "", "tko");

          //SCRAPER FUNCTION

          $year = date('Y');
          //$year = 2019;  //change this variable to collect past years' data
          $urlstart = "https://www.property.hk/eng/tran.php?bldg=tseung+kwan+o+plaza&prop=&size=&year=".$year."&month=&select=&page=&dt=&tab=TRAN"; 
          $curlstart = curl_init();
          curl_setopt($curlstart, CURLOPT_URL, $urlstart);
          curl_setopt($curlstart, CURLOPT_RETURNTRANSFER, true);
          $resultstart = curl_exec($curlstart);

          preg_match_all('!(\<ul class=\'pagination pagination-md.*?\<li.*?\<\/ul\>).*pagination!',$resultstart, $matchstart);

          $outputstart[] = $matchstart[0];
          
          //determine how many pages of data there are, removing the "next" page
          $numpage = substr_count($outputstart[0][0],'<li');
          $mirrornumpage = $numpage; 
          if ($mirrornumpage > 1) {
            $numpage = $mirrornumpage-1; 
          }

          //definizione array of urls
          $url = array();

        for ($i=1; $i <$numpage+1 ; $i++) { 
          array_push($url,"https://www.property.hk/eng/tran.php?bldg=tseung+kwan+o+plaza&prop=&size=&year=".$year."&month=&select=&page=".$i."&dt=&tab=TRAN");
        }

        //definizione variabili
        $transactions = array(
          'date' =>
          array(
          ),
          'floor' =>
          array(
          ),
          'unit' =>
          array(
          ),
          'area' =>
          array(
          ),
          'price' =>
          array(
          ),
        );
        $tempdate = array();
        $tempfloor = array();
        $tempunit = array();
        $temparea = array();
        $tempprice = array();
        $curl = curl_init();

        //fetching data from Property.hk
        for ($x = 0; $x <$numpage; $x++) {

        curl_setopt($curl, CURLOPT_URL, $url[$x]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $results = curl_exec($curl);

        //match all transactions' date
        preg_match_all('!checknum.*\n.*\n.*?\<.*?\>(.*?)\<.*?\>!',$results, $match);
        array_push($tempdate, $match[1]);

        //match floor
        preg_match_all('!checknum.*\n.*\n.*\n.*\n.*?\<.*?\>(.*?)\<.*?\>!',$results, $match);
        array_push($tempfloor, $match[1]);

        //match unit
        preg_match_all('!checknum.*\n.*\n.*\n.*\n.*\n.*?\<.*?\>(.*?)\<.*?\>!',$results, $match);
        array_push($tempunit, $match[1]);

        //match area
        preg_match_all('!checknum.*\n.*\n.*\n.*\n.*\n.*\n.*?\<.*?\>(.*?)\<.*?\>!',$results, $match);
        array_push($temparea, $match[1]);

        //match price
        preg_match_all('!checknum.*\n.*\n.*\n.*\n.*\n.*\n.*\n.*?\<.*?\>(.*?)\<.*?\>!',$results, $match);
        array_push($tempprice, $match[1]);
        }

        //provision in case there is only 1 page in the year
        if($numpage > 1) {
              $pagelines = 20;
            } 
            else{
              $pagelines = count($tempdate[0]);
            }

        //create array
        for ($x = 0; $x <$numpage; $x++) {
          for ($y = 0; $y <$pagelines; $y++) {
            array_push($transactions['date'],$tempdate[$x][$y]);  
            array_push($transactions['floor'],$tempfloor[$x][$y]);  
            array_push($transactions['unit'],$tempunit[$x][$y]);  
            array_push($transactions['area'],$temparea[$x][$y]);  
            array_push($transactions['price'],$tempprice[$x][$y]);
            if ($x == $numpage-1) {
                  $pagelines = count($tempdate[$x]);
                }      
          }
        }

        $totallines = (20*$numpage) - 20 + $pagelines;  

         for ($x = 0; $x <$totallines; $x++) {
          $date = print_r($transactions['date'][$x],true);
          $floor = print_r($transactions['floor'][$x],true);
          $unit = print_r($transactions['unit'][$x], true);
          $area = print_r($transactions['area'][$x], true);
          $price = print_r($transactions['price'][$x], true);
          if ($area > 0) {
            $pricepersft = (int)$price*1000000/(int)$area;
          }
            else{
              $pricepersft = 0; 
            }
          
          $sql5 = "INSERT INTO Propertyhk (Date, Floor, Area, Unit, Price, Pricepersft) VALUES ('$date','$floor','$area','$unit','$price','$pricepersft')";
          $con -> query($sql5);

        }

        $sql4 = "DELETE t1 FROM Propertyhk t1 INNER JOIN Propertyhk t2 WHERE t1.id < t2.id AND t1.Date = t2.Date AND t1.Floor = t2.Floor AND t1.Unit = t2.Unit";
          $con -> query($sql4);
            


        $sql3 = "DELETE FROM Propertyhk WHERE Price IS NULL OR Area IS NULL OR Pricepersft < 8000";
         $con -> query($sql3);
          
            ?>
        </div>
	 </body>
 </html>