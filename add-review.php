<!DOCTYPE HTML>
<html>
<head>
  <link href="https://fonts.googleapis.com/css?family=Dosis:300" rel="stylesheet">
  <link href="lamp-styles.css" rel="stylesheet">
  <title>Review Entry</title>
</head>
<body>
  <p>
  <?php
  include ('/home/eh1842/config.php');
  $db_link = new mysqli($db_server, $db_user, $db_password, $db_name);
  if ($db_link->connect_errno) {
      print( "Failed to connect to MySQL: (" .$db_link->connect_errno . ") ".$db_link->connect_error);
  }
  print("Connection: ".$db_link->host_info . "\n");

  /* setting a variable for the nation of origin  on the HTML form
     Notice that "mysqli_real_escape_string()" is used here to "clean up" the data entry
     This is not needed because we use "bind" below, but this is so you can see how it used: */

  $rRsName   = mysqli_real_escape_string($db_link,$_POST['rs-name']);
  $rReview   = mysqli_real_escape_string($db_link,$_POST['rs-review']);
  $rSource = "Foodie";
  $dataCheck = 1;

  // Check that both fields are filled in
  if ((empty ($rRsName)) OR (empty($rReview))) {
     print("<h3>Please fill in both restaurant name and review.</h3>\n");
     $dataCheck = 0;
     // Testing
    //  print("<p>Name: $rRsName<br>Review: $rReview</p>");
     print("<button onclick=\"location.href='http://i6.cims.nyu.edu/~eh1842/LAMP/review-entry-form.php';\">Return</button>\n");
  }
  else {
  // Check that restaurant exists in database
    $query_name = "SELECT rsKey, rsName from rs";
    $name_check = False;
    $rs_key = 0;
    $result = mysqli_query($db_link,$query_name);
    while ($line = $result->fetch_assoc()) {
      foreach ($line as $key => $col_value) {
        if ($key == 'rsName') {
          // Case insensitive comparison
          if (strcasecmp($col_value,$rRsName)==0) {
            $name_check = True;
            $rs_key = $line['rsKey']; // Store the restaurant's key value
          }
        }
      }
    }
    if ($name_check == False) {
      $dataCheck = 0;
      print("<h3>This restaurant does not exist in the Foodie database.</h3>\n");
      print("<button onclick=\"location.href='http://i6.cims.nyu.edu/~eh1842/LAMP/review-entry-form.php';\">Return</button>\n");
    }
    // Testing
    // print("<p>key: $rs_key</p>\n");
    /* Free resultset */
    mysqli_free_result($result);
  }
  if ($dataCheck > 0) {
    print("<h3><i>Your review: </i></h3>\n");
    print("\t<i>Restaurant:</i> $rRsName <br>\n");
    print("\t<i>Review:</i> $rReview<br>\n ");
    print("\t<i>Source: Foodie</i><br>\n");

    // Retrieve new reviewKey
    $rev_newKey = 0;
    $query_revKey = "SELECT revKey from rev ORDER BY revKey DESC LIMIT 1";
    $result = mysqli_query($db_link,$query_revKey);
    while ($line = $result->fetch_assoc()) {
      foreach ($line as $col_value) {
        $rev_newKey = ($col_value + 1);
      }
    }
    print("<button onclick=\"location.href='http://i6.cims.nyu.edu/~eh1842/LAMP/review-entry-form.php';\">Add Another Review</button>\n");
    // Testing
    // print("<p>Review key: $rev_newKey</p>\n");
    /* Free resultset */
    mysqli_free_result($result);

    /* Creating the SQL INSERT query for testing purposes */
    $query1 = "INSERT INTO rev (rsKey, revKey, review, revSource)
               VALUES ($rs_key, $rev_newKey, \"$rReview\", \"$rSource\")";

    /* for testing purposes */
    // print("\n<p>Test the fields: An INSERT query would be: ". $query1 . "</p>\n");

    /* This is how you would run an INSERT without protection from SQL Injection:
    $insertResult =  mysqli_query($db_link,$query1);
    */

    /* However ... To run this with protection from SQL Injection: */
    if ($stmt = $db_link->prepare("INSERT INTO rev (rsKey, revKey, review, revSource)
               VALUES (?, ?, ?, ?)")) {

      // Bind the variables to the parameter as strings.
      // Note that the "sssss" specifies strings for the following fields
      $stmt->bind_param("ssss", $rs_key, $rev_newKey,$rReview,$rSource);
      // Execute the query
      $stmt->execute();
      // Close the prepared statement.
      $stmt->close();
    }

    /* Closing connection */
    mysqli_close($db_link);

  } // end of dataCheck >0
  ?>
</body>
</html>
