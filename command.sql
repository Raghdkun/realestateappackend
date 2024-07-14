CREATE OR REPLACE VIEW  items1view AS
SELECT items.* , cat.* , (items_price - (items_price * items_descount / 100 )) as itemspricedisscount FROM items 
INNER JOIN  cat on  items.items_cat = cat.cat_id 


CREATE OR REPLACE VIEW  items1view AS
SELECT items.* , categories.* , (items_price - (items_price * items_descount / 100 )) as itemspricedisscount , itemscolors.itemscolors_name AS color, itemssizes.itemssizes_title AS size FROM items 
INNER JOIN  categories on  items.items_cat = categories.categories_id 
LEFT JOIN itemsvariations ON items.items_id = itemsvariations.itemsvariations_itemsid
LEFT JOIN itemscolors ON itemsvariations.itemsvariations_color_id = itemscolors.itemscolors_id
LEFT JOIN itemssizes ON itemsvariations.itemsvariations_size_id = itemssizes.itemssizes_id


/// items view with colors and sizes


SELECT items.*,  cat.* , (items_price - (items_price * items_descount / 100 )) as itemspricedisscount , itemscolors.itemscolors_name AS color, itemssizes.itemssizes_title AS size, itemsvariations_quantity
FROM items
INNER JOIN  cat on  items.items_cat = cat.cat_id 
INNER JOIN itemsvariations ON items.items_id = itemsvariations.itemsvariations_itemsid
LEFT JOIN itemscolors ON itemsvariations.itemsvariations_color_id = itemscolors.itemscolors_id
LEFT JOIN itemssizes ON itemsvariations.itemsvariations_size_id = itemssizes.itemssizes_id


/// available view for colors and sizes 
CREATE OR REPLACE VIEW  subitemsview AS
SELECT DISTINCT itemscolors.itemscolors_name, itemssizes.itemssizes_title , itemsvariations.itemsvariations_quantity
FROM itemsvariations
INNER JOIN itemscolors ON itemsvariations.itemsvariations_color_id = itemscolors.itemscolors_id
INNER JOIN itemssizes ON itemsvariations.itemsvariations_size_id = itemssizes.itemssizes_id
WHERE itemsvariations.itemsvariations_itemsid
GROUP BY itemscolors.itemscolors_name, itemssizes.itemssizes_title;

// for colors
CREATE OR REPLACE VIEW  colorsitemsview AS
SELECT DISTINCT itemscolors.itemscolors_name , itemsvariations.itemsvariations_itemsid
FROM itemsvariations
INNER JOIN itemscolors ON itemsvariations.itemsvariations_color_id = itemscolors.itemscolors_id

WHERE itemsvariations.itemsvariations_itemsid
GROUP BY itemscolors.itemscolors_name

for sizes
CREATE OR REPLACE VIEW  sizesitemsview AS
SELECT DISTINCT itemssizes.itemssizes_title , itemsvariations.itemsvariations_quantity , itemsvariations.itemsvariations_itemsid
FROM itemsvariations
INNER JOIN itemssizes ON itemsvariations.itemsvariations_size_id = itemssizes.itemssizes_id
WHERE itemsvariations.itemsvariations_itemsid
GROUP BY  itemssizes.itemssizes_title;

CREATE OR REPLACE VIEW myfavorite AS
SELECT favorite.* , items.* , users.users_id  ,  FROM favorite 
INNER JOIN users ON users.users_id  = favorite.favorite_usersid
INNER JOIN items ON items.items_id  = favorite.favorite_itemsid


CREATE OR REPLACE VIEW cartview AS
SELECT SUM(items.items_price - items.items_price * items.items_descount / 100 ) AS itemsprice , COUNT(cart.cart_itemsid) as countitems , cart.* , items.* FROM cart
INNER JOIN items ON items.items_id = cart.cart_itemsid 
WHERE cart_orders = 0
GROUP BY cart.cart_itemsid , cart.cart_usersid ;  

CREATE OR REPLACE VIEW cartview AS
SELECT SUM(items.items_price - items.items_price * items.items_descount / 100 ) AS itemsprice ,  cart.* , items.* FROM cart
INNER JOIN items ON items.items_id = cart.cart_itemsid 
WHERE cart_orders = 0
GROUP BY cart.cart_itemsid , cart.cart_usersid ;  

CREATE OR REPLACE VIEW ordersview AS
SELECT orders.* , address.* FROM orders 
INNER JOIN address ON address.address_id = orders.orders_address ; 
///////////////// LEFT. or RIGHT /////////////////////////////

CREATE OR REPLACE VIEW ordersdetailsview AS
SELECT SUM(items.items_price - items.items_price * items.items_descount / 100 ) AS itemsprice , COUNT(cart.cart_itemsid) as countitems , cart.* , items.* , ordersview.*  FROM cart
INNER JOIN items ON items.items_id = cart.cart_itemsid 
INNER JOIN ordersview ON ordersview.orders_id = cart.cart_orders 

WHERE cart_orders != 0
GROUP BY cart.cart_itemsid , cart.cart_usersid , cart.cart_orders ;  


// topselling VIEW
CREATE OR REPLACE VIEW itemstopselling AS
SELECT COUNT(cart.cart_id) AS countitems , cart.* , items.* ,  (items_price - (items_price * items_descount / 100 )) as itemspricedisscount FROM cart 
INNER JOIN items ON items.items_id = cart.cart_itemsid 
WHERE cart_orders != 0 
GROUP BY cart_itemsid


SELECT items.*, itemscolors.itemscolors_name AS color, itemssizes.itemssizes_title AS size
FROM items
JOIN itemsvariations ON items.items_id = itemsvariations.itemsvariations_itemsid
JOIN itemscolors ON itemsvariations.itemsvariations_color_id = itemscolors.itemscolors_id
JOIN itemssizes ON itemsvariations.itemsvariations_size_id = itemssizes.itemssizes_id


SELECT * FROM markers
WHERE ST_Distance_Sphere(point(lng, lat), point(-122.085823, 37.386337)) <= 5000;
