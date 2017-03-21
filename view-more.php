<!DOCTYPE html>
<html>

<head>
  <link href="https://fonts.googleapis.com/css?family=Dosis:300" rel="stylesheet">
  <link href="lamp-styles.css" rel="stylesheet">
  <title>View More</title>
</head>
<body id='view-more'>
  <?php
    include("/home/eh1842/config.php");
    $db_link = new mysqli($db_server,$db_user,$db_password,$db_name);
    if ($db_link->connect_errno) {
    print( "Failed to connect to MySQL: (" .$db_link->connect_errno . ") ".$db_link->connect_error);
    }

    $restaurant = $_POST['view-more'];
    print("<h1>$restaurant</h1>\n");

  // Query for image source
    $query_img = "SELECT rsName, image
      FROM rs
      WHERE rsName = '$restaurant'";
  // Query for restaurant hours
    $query_hr = "SELECT rs.rsName, hr.hrDay, hr.hrOpen, hr.hrClose
      FROM rs INNER JOIN hr
      ON rs.rsKey = hr.rsKey
      WHERE rs.rsName = '$restaurant'";
  // Query for restaurant contact info
    $query_cont = "SELECT rs.rsName, cont.contPosition, cont.contName, cont.contPhone
      FROM rs INNER JOIN cont
      ON rs.rsKey = cont.rsKey
      WHERE rs.rsName = '$restaurant'";
  // Query for restaurant reviews
    $query_rev = "SELECT rs.rsName, rev.review, rev.revSource
      FROM rs INNER JOIN rev
      ON rs.rsKey = rev.rsKey
      WHERE rs.rsName = '$restaurant'";

  // Print restaurant image
    $result = mysqli_query($db_link,$query_img);
    while ($line = $result->fetch_assoc()) {
      $hours = "";
      foreach ($line as $key => $col_value) {
        if($key=='image') print("<img src=\"$col_value\">\n");
      }
    }
    /* Free resultset */
    mysqli_free_result($result);

  // Print restaurant hours
    $result = mysqli_query($db_link,$query_hr);
    print("<h2>Hours</h2>\n");
    while ($line = $result->fetch_assoc()) {
      $hours = "";
      foreach ($line as $key => $col_value) {
        if($key=='hrDay') $hours .= "$col_value: ";
        if($key=='hrOpen') $hours .= "$col_value - ";
        if($key=='hrClose') $hours .= "$col_value";
      }
      print("<p>$hours</p>\n");
    }
    /* Free resultset */
    mysqli_free_result($result);

  // Print restaurant contact info
    $result = mysqli_query($db_link,$query_cont);
    print("<h2>Contact Info</h2>\n");
    while ($line = $result->fetch_assoc()) {
      $contact = "";
      foreach ($line as $key => $col_value) {
        if($key=='contPosition') $contact .= "$col_value: ";
        if($key=='contName') $contact .= "$col_value<br>";
        if($key=='contPhone') $contact .= "Phone: $col_value";
      }
      print("<p>$contact</p>\n");
    }
    /* Free resultset */
    mysqli_free_result($result);

  // Print restaurant reviews
    $result = mysqli_query($db_link,$query_rev);
    print("<h2>Reviews</h2>\n");
    while ($line = $result->fetch_assoc()) {
      $review = "";
      foreach ($line as $key => $col_value) {
        if($key=='review') $review .= "$col_value - ";
        if($key=='revSource') $review .= "$col_value";
      }
      print("<p>$review</p>\n");
    }
    /* Free resultset */
    mysqli_free_result($result);

    /* Closing connection */
    mysqli_close($db_link);
  ?>
</body>
</html>
