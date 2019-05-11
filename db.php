<?php
$connect = mysqli_connect('localhost', 'root', '159357', 'cart');
$query = "SELECT * FROM `products`";
$stmt = mysqli_prepare($connect, $query);
if ($stmt){
      mysqli_stmt_bind_result($stmt, $id, $name, $image, $price);
    mysqli_stmt_execute($stmt);
    while (mysqli_stmt_fetch($stmt)){
        echo $id .' - '.$name.' - '. $image.' - '. $price;
    }  
}
?>
