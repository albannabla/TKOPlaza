<!DOCTYPE html>
<html lang="en">
<head>
    <title>L&C Project</title>
	 <meta charset="UTF-8"/>
	 <link rel="stylesheet" href="stile.css?v={CURRENT_TIMESTAMP}" type="text/css"/>
</head>
<body>
  <form action="index.php"> <button type="submit">Home</button> </form> 
  <div>
  <?php
  $con = mysqli_connect("localhost", "root", "", "tko");

  $sql = "SELECT * FROM Propertyhk";
  echo
		  "<table align='center'>
		   <tr>
		     <th>Date</th>
  			 <th>Floor</th>
	  		 <th>Area</th>
	   		 <th>Unit</th>
         <th>Price</th>
         <th>Pricepersft</th>
		    </tr>
		    <tr>";

  $result = $con->query($sql);
  if ($result->num_rows > 0) {
    // output data of each row
      while($row = $result->fetch_assoc()) {
		  echo
		  "<tr>
		    <td>" . $row["Date"] . "</td>
			  <td>" . $row["Floor"] . "</td>
			  <td>" . $row["Area"] . "</td>
			  <td>" . $row["Unit"] . "</td>
        <td>" . $row["Price"] . "</td>
        <td>" . $row["Pricepersft"] . "</td>    
		    </tr>";
      }
	  echo "</table>";
	  
  } else {
      echo "0 results";
    }
  $con->close();
  ?>
 </div>
</body>
</html>