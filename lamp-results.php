<!DOCTYPE html>
<html>
<head>
  <link href="https://fonts.googleapis.com/css?family=Dosis:300" rel="stylesheet">
  <link href="lamp-styles.css" rel="stylesheet">
  <title>Foodie results</title>
</head>
<body id="result-page">
  <form action="view-more.php" method="post">
  <?php

  include("/home/eh1842/config.php");
  $db_link = new mysqli($db_server,$db_user,$db_password,$db_name);
  if ($db_link->connect_errno) {
  print( "Failed to connect to MySQL: (" .$db_link->connect_errno . ") ".$db_link->connect_error);
  }

  // Store user input into variables
  // Search settings
  $rsType = $_POST['rs-type'];
  $priceRange = $_POST['price'];
  $zipcode = $_POST['zip-code'];
  // Display settings
  $d_displayType = $_POST['display-type'];
  $d_orderBy = $_POST['order-by'];
  $d_showTop = $_POST['show-top'];
  // Variables to store "ORDER BY", "WHERE", and "LIMIT" queries
  $query_orderBy = "ORDER BY ";
  $query_where = "WHERE "; // WHERE query can have up to 3 conditions (restaurant type, price range, zip code)
  $query_limit = "";
  // String variables to use later for HTML
  $h_priceRange = ""; // $/$$/$$$
  $h_showTop = "all"; // All, top 5, top 10
  $h_displayType = ""; // Restaurant/Food Dishes
  $h_showing = "<h2>Showing: "; // <h2>Showing: top 10 restaurants</h2>
  $h_thatAre = "<h3>That are: "; // <h3>That are: Japanese, $$, in 10003</h3>
  $h_orderedBy = "<h3>Ordered by: ".$d_orderBy; // <h3>Ordered by: name</h3>

  // Variables holding case R and case F queries
  // rs name,type,price range,rating,address,zip,website,image
  $query_rs = "SELECT rs.website, rs.rsName, rs.type, rs.priceRange, rs.rating, ad.street, ad.city, ad.state, ad.zipcode
    FROM rs
    INNER JOIN ad
    ON rs.rsKey = ad.rsKey ";
  // item name,item price,rs name,rs type,price range,rating,address,zip
  $query_food = "SELECT menu.itemName, menu.itemPrice, rs.rsName, rs.type, rs.priceRange, rs.rating, ad.street, ad.city, ad.state, ad.zipcode
    FROM menu
    INNER JOIN rs
    ON menu.rsKey = rs.rsKey
    INNER JOIN ad
    ON menu.rsKey = ad.rsKey ";

  // Set $h_priceRange ($/$$/$$$)
  if ($priceRange>0) {
    $h_priceRange = str_repeat("$", $priceRange); // Result: $/$$/$$$
  }
  // Create "WHERE" query, Set $h_thatAre
  if ($rsType!="NULL"){
    $query_where .= "type='$rsType'";
    $h_thatAre .= ($rsType);
    if ($priceRange!="NULL"){
    $query_where .= " AND priceRange='$priceRange'";
    $h_thatAre .= (", $h_priceRange</h3>");
    if ($zipcode!="NULL") {
      $query_where .= " AND zipcode='$zipcode'";
      $h_thatAre .= (", in $zipcode</h3>");}
    } else {
      if ($zipcode!="NULL") {
        $query_where .= " AND zipcode='$zipcode'";
        $h_thatAre .= (", in $zipcode</h3>");}
    }
  } else {
    if ($priceRange!="NULL"){
    $query_where .= "priceRange='$priceRange'";
    $h_thatAre .= ("$h_priceRange</h3>");
    if ($zipcode!="NULL") {
      $query_where .= " AND zipcode='$zipcode'";
      $h_thatAre .= (", in $zipcode</h3>");}
    } else {
      if ($zipcode!="NULL") {
        $query_where .= "zipcode='$zipcode'";
        $h_thatAre .= ("in $zipcode</h3>");
      } else {
        $query_where="";
        $h_thatAre="";}
    }
  }

  // Create "LIMIT" query, Set $h_showTop
  if ($d_showTop!="NULL") {
    $query_limit = $d_showTop;
    if ($d_showTop=="LIMIT 5") $h_showTop = "top 5";
    else $h_showTop = "top 10";
  }

  // If statement between "show:restaurants" and "show:food dishes", set $h_displayType
  if ($d_displayType == "rs") { // "show:restaurants"
    $query_x = $query_rs;
    $h_displayType = "Restaurants";
    // Create "ORDER BY" query
    if ($d_orderBy == "name") $query_orderBy .= "rsName ASC";
    elseif ($d_orderBy == "price") $query_orderBy .= "priceRange ASC,rsName ASC";
    else $query_orderBy .= ($d_orderBy.",rsName ASC");
  } else { // "show:food dishes"
    $query_x = $query_food;
    $h_displayType = "Food Dishes";
    if ($d_orderBy == "name") $query_orderBy .= "itemName ASC";
    elseif ($d_orderBy == "price") $query_orderBy .= "itemPrice ASC,rsName ASC";
    else $query_orderBy .= ($d_orderBy.",rsName ASC");
  }

  // Add additional queries to $query_x
  $query_x .= ($query_where." ".$query_orderBy." ".$query_limit.";");

  // For testing purposes: queries
  // print("<p>where query: ".$query_where."</p>\n");
  // print("<p>order by query: ".$query_orderBy."</p>\n");
  // print("<p>limit query: ".$query_limit."</p>\n");
  // print("<p>query x: ".$query_x."</p>\n");

  // Set html variables
  $h_showing .= ($h_showTop." ".$h_displayType."</h2>");
  // $h_thatAre .= "</h3>";
  $h_orderedBy .= "</h3>";

  // For testing purposes: html variables
  // print("<p>price range: ".$h_priceRange."</p>\n");
  // print("<p>show top: ".$h_showTop."</p>\n");
  // print($h_showing."\n");
  // print($h_thatAre."\n");
  // print($h_orderedBy."\n");

  // Populate Table
  $result = mysqli_query($db_link,$query_x);
  $num_rows = mysqli_num_rows($result); // # records returned
  if ($num_rows > 0) {
  /* Printing results in HTML */
  // Print header
  print("<div id='header'>\n<h1>Results for Foodie</h1>\n");
  print($h_showing."\n".$h_thatAre."\n".$h_orderedBy."\n<hr>\n");
  // If displaying restaurants
  if ($d_displayType == "rs") {
    print( "\n<table id='case-R'>\n");
    print("\t<tr>\n
          \t\t<th>Name</th>\n
          \t\t<th>Type</th>\n
          \t\t<th>Price Range</th>\n
          \t\t<th>Rating (out of 5)</th>\n
          \t\t<th>Address</th>\n
          \t\t<th>View Details</th>\n
          \t</tr>\n");
    while ($line = $result->fetch_assoc())   {
      print("\t<tr>\n");
      $name = "";
      foreach ($line as $key => $col_value) {
        // Add website as hyper link to restaurant name
        if($key=='website') print("\t\t<td><a href=$col_value>");
        if($key=='rsName') {
          $name = $col_value;
          print("$col_value</a></td>\n");
        }
        // Print restaurant type
        if($key=='type') print("\t\t<td>$col_value</td>\n");
        // Print $/$$/$$$ according to price range
        if($key=='priceRange') {
          $dollar = str_repeat("$", $col_value);
          print("\t\t<td>$dollar</td>\n");
        }
        // Print rating out of 5
        if($key=='rating') print("\t\t<td>$col_value/5</td>\n");
        // Print address. Don't close the <td> tag until zipcode column
        if($key=='street') print("\t\t<td>$col_value, ");
        if($key=='city') print("$col_value, ");
        if($key=='state') print("$col_value, ");
        if($key=='zipcode') {
          print("$col_value</td>\n");
          print("\t\t<td><input type='submit' name='view-more' value='$name'/></td>\n");}
        // print("\t\t<td><input type='submit' name='view-more' value='$name'/></td>\n");
      }
      print("\t</tr>\n");
    } // end of while
  } else { // If displaying food dishes
    print( "\n<table id='case-R'>\n");
    print("\t<tr>\n
          \t\t<th>Name</th>\n
          \t\t<th>Price</th>\n
          \t\t<th>Restaurant</th>\n
          \t\t<th>Type</th>\n
          \t\t<th>Price Range</th>\n
          \t\t<th>Rating (out of 5)</th>\n
          \t\t<th>Address</th>\n
          \t</tr>\n");
    while ($line = $result->fetch_assoc())   {
      print("\t<tr>\n");
      foreach ($line as $key => $col_value) {
        // Add website as hyper link to restaurant name
        if($key=='itemName') print("\t\t<td>$col_value</td>\n");
        if($key=='itemPrice') print("\t\t<td>$$col_value</td>\n");
        if($key=='rsName') print("\t\t<td>$col_value</td>\n");
        // Print restaurant type
        if($key=='type') print("\t\t<td>$col_value</td>\n");
        // Print $/$$/$$$ according to price range
        if($key=='priceRange') {
          $dollar = str_repeat("$", $col_value);
          print("\t\t<td>$dollar</td>\n");
        }
        // Print rating out of 5
        if($key=='rating') print("\t\t<td>$col_value/5</td>\n");
        // Print address. Don't close the <td> tag until zipcode column
        if($key=='street') print("\t\t<td>$col_value, ");
        if($key=='city') print("$col_value, ");
        if($key=='state') print("$col_value, ");
        if($key=='zipcode') print("$col_value</td>\n");
      }
      print("\t</tr>\n");
    } // end of while
  }
  print("</table>\n");
  print("<p>&nbsp</p><hr /><p class=\"results\">There are $num_rows records on display.</p>");
  }
  else {
    print("<p>There are no restaurants for these specifications. Please try again.</p>");
  }  // end of num_rows > 0

    /* Free resultset */
    mysqli_free_result($result);

   /* Closing connection */
  mysqli_close($db_link);

  ?>
</form>
</body>
</html>
