-- Create tables
CREATE TABLE rs ( -- Basic info about the restaurants
  rsKey INT NOT NULL,
  rsName VARCHAR(255),
  type VARCHAR(255),
  priceRange INT,
  website VARCHAR(255),
  image VARCHAR(255),
  imageFile VARCHAR(255),
  rating DECIMAL(2,1),
  PRIMARY KEY (rsKey)
);
CREATE TABLE ad ( -- Addresses of the restaurants
  rsKey INT NOT NULL,
  adKey INT NOT NULL,
  street VARCHAR(255),
  city VARCHAR(255),
  state VARCHAR(255),
  zipcode INT,
  KEY (rsKey),
  PRIMARY KEY (adKey)
);
CREATE TABLE menu ( -- Menu items from the restaurants
  rsKey INT NOT NULL,
  itemKey INT NOT NULL,
  itemName VARCHAR(255),
  itemPrice DECIMAL(4,2),
  KEY (rsKey),
  PRIMARY KEY (itemKey)
);
CREATE TABLE rev ( -- Reviews of the restaurants
  rsKey INT NOT NULL,
  revKey INT NOT NULL,
  review VARCHAR(255),
  revSource VARCHAR(255),
  KEY (rsKey),
  PRIMARY KEY (revKey)
);
CREATE TABLE cont ( -- Contacts for each restaurant
  rsKey INT NOT NULL,
  contKey INT NOT NULL,
  contName VARCHAR(255),
  contPhone VARCHAR(255),
  contPosition VARCHAR(255),
  KEY (rsKey),
  PRIMARY KEY (contKey)
);
CREATE TABLE hr ( -- Open/close hours for each day
  rsKey INT NOT NULL,
  hrKey INT NOT NULL,
  hrOpen VARCHAR(255),
  hrClose VARCHAR(255),
  hrDay VARCHAR(255),
  KEY (rsKey),
  PRIMARY KEY (hrKey)
);

-- Load data into tables
LOAD DATA LOCAL INFILE "LAMP_data_files/restaurant.txt"
INTO TABLE rs
FIELDS TERMINATED BY ","
LINES TERMINATED BY "\n"
IGNORE 1 LINES;

LOAD DATA LOCAL INFILE "LAMP_data_files/address.txt"
INTO TABLE ad
FIELDS TERMINATED BY ","
LINES TERMINATED BY "\n"
IGNORE 1 LINES;

LOAD DATA LOCAL INFILE "LAMP_data_files/menu.txt"
INTO TABLE menu
FIELDS TERMINATED BY ","
LINES TERMINATED BY "\n"
IGNORE 1 LINES;

LOAD DATA LOCAL INFILE "LAMP_data_files/review.txt"
INTO TABLE rev
FIELDS TERMINATED BY ","
LINES TERMINATED BY "\n"
IGNORE 1 LINES;

LOAD DATA LOCAL INFILE "LAMP_data_files/contact.txt"
INTO TABLE cont
FIELDS TERMINATED BY ","
LINES TERMINATED BY "\n"
IGNORE 1 LINES;

LOAD DATA LOCAL INFILE "LAMP_data_files/hour.txt"
INTO TABLE hr
FIELDS TERMINATED BY ","
LINES TERMINATED BY "\n"
IGNORE 1 LINES;

-- Example Queries
-- Case R
SELECT rs.rsName, rs.type, rs.priceRange, rs.rating, ad.street, ad.zipcode, rs.website, rs.image
  FROM rs
  INNER JOIN ad
  ON rs.rsKey = ad.rsKey
  ORDER BY $new_orderBy
  ORDER BY rsName ASC
  LIMIT 5;
  -- Example case R
    -- rsType:American, price range:2, zipcode:10003
    -- show:restaurants, order by:ratings, show top:10
    SELECT rs.rsName, rs.type, rs.priceRange, rs.rating, ad.street, ad.zipcode
      FROM rs
      INNER JOIN ad
      ON rs.rsKey = ad.rsKey
      WHERE type='American' AND priceRange='2' AND zipcode='10003'
      ORDER BY rating DESC
      LIMIT 10;
-- Case F
SELECT menu.itemName, menu.itemPrice, rs.rsName, rs.type, rs.priceRange, rs.rating, ad.street, ad.zipcode
  FROM menu
  INNER JOIN rs
  ON menu.rsKey = rs.rsKey
  INNER JOIN ad
  ON menu.rsKey = ad.rsKey
  ORDER BY $new_orderBy
  ORDER BY itemPrice DESC;
  -- Example case F
    -- rsType:Thai, price range:2, zipcode:NULL
    -- show:food dishes, order by:price, show top:10
    SELECT menu.itemName, menu.itemPrice, rs.rsName, rs.type, rs.priceRange, rs.rating, ad.street, ad.zipcode
      FROM menu
      INNER JOIN rs
      ON menu.rsKey = rs.rsKey
      INNER JOIN ad
      ON menu.rsKey = ad.rsKey
      WHERE type='Thai' AND priceRange='1'
      ORDER BY itemPrice ASC
      LIMIT 10;
