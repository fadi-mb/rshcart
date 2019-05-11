<?php
session_start();
$product_ids = array();

// Check if Add to Cart Button has been submitted

if (filter_input(INPUT_POST, 'add_to_cart')) {
    if (isset($_SESSION['shopping_cart'])) {
        // keep track that how many products are in shopping cart
        $count = count($_SESSION['shopping_cart']);

        // create squantial array fro matching array keys to product id's
        $product_ids = array_column($_SESSION['shopping_cart'], 'id');

        if (!in_array(filter_input(INPUT_GET, 'id'), $product_ids)) {
            $_SESSION['shopping_cart'][$count] = array
                (
                'id' => filter_input(INPUT_GET, 'id'),
                'name' => filter_input(INPUT_POST, 'name'),
                'price' => filter_input(INPUT_POST, 'price'),
                'quantity' => filter_input(INPUT_POST, 'quantity'),
            );
        } else { //product already exist, increase quantity
            // match array key to id of the product being added to the cart
            for ($i = 0; $i < count($product_ids); $i++) {
                if ($product_ids[$i] == filter_input(INPUT_GET, 'id')) {
                    // add items quanity to the existing product in the array
                    $_SESSION['shopping_cart'][$i]['quantity'] += filter_input(INPUT_POST, 'quantity');
                }
            }
        }

    } else { //if shopping cart does't exist, create first product with array key 0
        //create array using submitted form data, start from key 0 and fill it with values
        $_SESSION['shopping_cart'][0] = array
            (
            'id' => filter_input(INPUT_GET, 'id'),
            'name' => filter_input(INPUT_POST, 'name'),
            'price' => filter_input(INPUT_POST, 'price'),
            'quantity' => filter_input(INPUT_POST, 'quantity'),
        );
    }
}

if (filter_input(INPUT_GET, 'action') == 'delete') {
    // loop through all product in shopping cart until it matches with get Id variable
    foreach ($_SESSION['shopping_cart'] as $key => $product) {
        if ($product['id'] == filter_input(INPUT_GET, 'id')) {
            // remove product form the shopping cart when it mactches with the GET id
            unset($_SESSION['shopping_cart'][$key]);
        }
    }
    // reset session array keys so they match with $product_ids numeric array
    $_SESSION['shopping_cart'] = array_values($_SESSION['shopping_cart']);
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="./bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="cart.css">
    <title>Cart</title>
</head>
<body>
<div class="container">
<div class="row">
<?php
$connect = mysqli_connect('localhost', 'root', '159357', 'cart');
$query = "SELECT * FROM `products`";
$stmt = mysqli_prepare($connect, $query);
if ($stmt):
    mysqli_stmt_bind_result($stmt, $id, $name, $image, $price);
    mysqli_stmt_execute($stmt);
    while (mysqli_stmt_fetch($stmt)):
    ?>
					                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
					                        <form method="post" action="cart.php?action=add&id=<?php echo $id; ?>">
					                            <div class="products">
					                                <img src="<?php echo $image; ?>" class="img-responsive" alt="">
					                                <h4 class="text-info"><?php echo $name; ?></h4>
					                                <h4>$ <?php echo $price; ?></h4>
					                                <input type="text" name="quantity" class="form-control" value="1">
					                                <input type="hidden" name="name" value="<?php echo $name; ?>">
					                                <input type="hidden" name="price" value="<?php echo $price; ?>">
					                                <input type="submit" name="add_to_cart" style="margin-top:5px;" class="btn btn-info" value="Add to Cart">
					                            </div>
					                        </form>
					                    </div>
					                    <?php
endwhile;
endif;
?>
</div>
            <div style="clear:both"></div>

            <div class="table-responsive">
                <table class="table">
                    <tr><th colspan="5"><h3>Order Details</h3></th></tr>
                    <tr>
                        <th width="40%">Product Name</th>
                        <th width="10%">Quantity</th>
                        <th width="20%">Price</th>
                        <th width="15%">Total</th>
                        <th width="5%">Action</th>
                    </tr>

                    <?php
if (!empty($_SESSION['shopping_cart'])):
    $total = 0;
    foreach ($_SESSION['shopping_cart'] as $key => $product):
    ?>
					                                <tr>
					                                    <td><?php echo $product['name']; ?></td>
					                                    <td><?php echo $product['quantity']; ?></td>
					                                    <td>$<?php echo $product['price']; ?></td>
					                                    <td>$<?php echo number_format($product['quantity'] * $product['price'], 2); ?></td>
					                                    <td>
					                                        <a href="cart.php?action=delete&id=<?php echo $product['id']; ?>">
					                                            <div class="btn-danger">Remove</div>
					                                        </a>
					                                    </td>
					                                </tr>
					                                <?php
    $total = $total + ($product['quantity'] * $product['price']);
endforeach;
?>
                            <tr>
                                <td colspan="3" align="right">Total</td>
                                <td align="right">$ <?php echo number_format($total, 2); ?></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="5">
                                    <?php
if (isset($_SESSION['shopping_cart'])):
    if (count($_SESSION['shopping_cart']) > 0):
    ?>
					                                            <a href="#" class="btn btn-primary btn-lg">Checkout</a>
					                                    <?php
endif;
endif;
?>
                                </td>

                            </tr>

                            <?php
endif;
?>


                </table>
            </div>
</div>

</body>
</html>


