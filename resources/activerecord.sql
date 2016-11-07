
CREATE DATABASE IF NOT EXISTS activerecord;
USE activerecord;

CREATE TABLE IF NOT EXISTS cars (
  id INT NOT NULL AUTO_INCREMENT,
  brand VARCHAR(20) NOT NULL,
  license_plate VARCHAR(10),
  PRIMARY KEY (id)
);

INSERT INTO cars (brand, license_plate) VALUES ('mercedes', 'A454FG'),
 ('BMW', 'FDF343'), ('lada', 'GF4345');