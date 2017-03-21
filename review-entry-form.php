<!DOCTYPE HTML>
<html>
<head>
  <link href="https://fonts.googleapis.com/css?family=Dosis:300" rel="stylesheet">
  <link href="lamp-styles.css" rel="stylesheet">
  <title>Review Entry</title>
</head>
<body>
  <h1>Enter a Review!</h1>
  <form action="add-review.php" method="post">
    <h4>Restaurant Name</h4>
    <input type="text" name="rs-name">
    <h4>Your Review</h4>
    <input type="text" name="rs-review">

    <?php
    // include ('/home/eh1842/de_config.php');
    // // For testing purposes only
    // // print("<p>User: $db_user </p>");
    //
    // print("<p>Artist's nationality:<br />");
    // print("<select name=country>");
    //
    // $db_link = new mysqli($db_server, $db_user, $db_password, $db_name);
    // if ($db_link->connect_errno) {
    // print( "Failed to connect to MySQL: (" .$db_link->connect_errno . ") ".$db_link->connect_error);
    // }
    // print("Connection: ".$db_link->host_info . "\n");
    //
    // $query = "SELECT country FROM artistCountries ORDER BY country; ";
    //
    // $result = mysqli_query($db_link,$query);
    // /* see how many records are returned */
    // $num_rows = mysqli_num_rows($result);
    // print("<p>Rows: ".$num_rows."</p>");
    //
    // while ($line = mysqli_fetch_array($result))   {
    //     print("<option value=\"$line[0]\">$line[0]</option>\n");
    // } // end of while
    //
    // print("</select>");
    //
    // /* Free resultset */
    // mysqli_free_result($result);
    // /* Closing connection */
    // mysqli_close($db_link);
    ?>
    <br>
    <input type=submit value=Submit>
    <input type=reset value=Cancel>
  </form>

</body>
</html>
