# Procedure for importing USDA database from scratch

(http://www.ars.usda.gov/Services/docs.htm?docid=22113)

## Abbreviated, ASCII, ABBREV.txt

* Do `:%s/\^\^/\^\\N\^/g` twice on ABBREV.txt to replace empty with \N for NULL.
* Do `:%s/\^$/\^\\N/g` on ABBREV.txt to replace end of line empty with \N for NULL.
* `mysql -p --local-infile=1`
* `use weightcast`
* `truncate table food_usda;`
* `LOAD DATA LOCAL INFILE 'ABBREV.txt' INTO TABLE food_usda FIELDS TERMINATED BY '^' OPTIONALLY ENCLOSED BY '~' LINES TERMINATED BY '\n';`
* `show warnings;`
* should show 4 warnings for row id 23999. Delete that row.

## Full, ASCII, FOOD_DES.txt

* Do `:%s/\^\^/\^\\N\^/g` twice on FOOD_DES.txt to replace empty with \N for NULL.
* Do `:%s/\^$/\^\\N/g` on FOOD_DES.txt to replace end of line empty with \N for NULL.
* `mysql -p --local-infile=1`
* `use weightcast`
* `truncate table food_usda_desc;`
* `LOAD DATA LOCAL INFILE 'FOOD_DES.txt' INTO TABLE food_usda_desc FIELDS TERMINATED BY '^' OPTIONALLY ENCLOSED BY '~' LINES TERMINATED BY '\n';`
* `show warnings;`
* should show 2 warnings for row id 4937. Indeterminate what to do.
