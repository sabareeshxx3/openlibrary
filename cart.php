<?php
include 'config.php';

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit(); // Stop further execution
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$message = []; // Initialize message array

if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    $result = mysqli_query($conn, "DELETE FROM `cart` WHERE id='$remove_id'");
    if ($result) {
        $message[] = 'Removed Successfully';
        header('Location: cart.php');
        exit(); // Stop further execution after redirect
    } else {
        $message[] = 'Error: Failed to remove item';
    }
}

if (isset($_POST['update'])) {
    $update_cart_id = $_POST['cart_id'];
    $book_price = $_POST['book_price'];
    $update_quantity = $_POST['update_quantity'];
    $total_price = $book_price * $update_quantity;
    $result = mysqli_query($conn, "UPDATE `cart` SET `quantity`='$update_quantity', `total`='$total_price' WHERE `id`='$update_cart_id'");
    if ($result) {
        $message[] = $user_name . ' your cart updated successfully';
    } else {
        $message[] = 'Error: Failed to update cart';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="css/hello.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <style>
        .cart-btn1,
        .cart-btn2 {

            display: inline-block;
            margin: auto;
            padding: 0.8rem 1.2rem;
            cursor: pointer;
            color: white;
            font-size: 15px;
            border-radius: .5rem;
            text-transform: capitalize;
        }

        .cart-btn1 {
            margin-left: 40%;
            background-color: #ffa41c;
            color: black;
        }

        .cart-btn2 {
            background-color: rgb(0, 167, 245);
            color: black;
        }

        .message {
            position: sticky;
            top: 0;
            margin: 0 auto;
            width: 61%;
            background-color: #fff;
            padding: 6px 9px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 100;
            gap: 0px;
            border: 2px solid rgb(68, 203, 236);
            border-top-right-radius: 8px;
            border-bottom-left-radius: 8px;
        }

        .message span {
            font-size: 22px;
            color: rgb(240, 18, 18);
            font-weight: 400;
        }

        .message i {
            cursor: pointer;
            color: rgb(3, 227, 235);
            font-size: 15px;
        }
    </style>
</head>

<body>
    <?php include 'index_header.php'; ?>
    <div class="cart_form">
        <?php
        if (!empty($message)) {
            foreach ($message as $msg) {
                echo '<div class="message" id="messages"><span>' . $msg . '</span></div>';
            }
        }
        ?>
        <table style="width: 70%; margin: 10px auto;">
            <thead>
                <th>Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total (₹)</th>
            </thead>
            <tbody>
                <?php
                $total = 0;
                if ($conn) {
                    $select_book = $conn->query("SELECT id, name, price, image, quantity, total FROM cart WHERE user_id = $user_id");
                    if ($select_book->num_rows > 0) {
                        while ($row = $select_book->fetch_assoc()) {
                            $sub_total = $row['price'] * $row['quantity'];
                            $total += $sub_total;
                ?>
                            <tr>
                                <td><img style="height: 90px;" src="./added_books/<?php echo $row['image']; ?>" alt=""></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['price']; ?></td>
                                <td>
                                    <form action="" method="POST">
                                        <input type="number" name="update_quantity" min="1" max="10" value="<?php echo $row['quantity']; ?>">
                                        <input type="hidden" name="cart_id" value="<?php echo $row['id']; ?>">
                                        <input class="hidden_input" type="hidden" name="book_price" value="<?php echo $row['price'] ?>">
                                        <button style="background: transparent;height: 26px;" name="update"><img  style="height: 26px; cursor: pointer;" src="./images/trash.svg" alt="update" ></button> |
                                        <a style="color: red;" href="cart.php?remove=<?php echo $row['id']; ?>"> Remove</a>
                                    </form>
                                </td>
                                <td><?php echo number_format($sub_total); ?></td>
                            </tr>
                <?php
                        }
                    } else {
                        echo '<tr><td colspan="5" class="empty">There is nothing in cart yet !!!!!!!!</td></tr>';
                    }
                } else {
                    echo '<tr><td colspan="5" class="empty">Database connection error</td></tr>';
                }
                ?>
                <tr>
                    <td colspan="4" style="text-align:right;">Total:</td>
                    <td>₹ <?php echo number_format($total); ?>/- </td>
                </tr>
            </tbody>
        </table>
        <a href="checkout.php" class="btn cart-btn1" style="display:<?php echo ($total > 1) ? 'inline-block' : 'none'; ?>;">Proceed to Checkout</a>
        <a class="cart-btn2" href="index.php">Continue Shopping</a>
    </div>
    <?php include 'index_footer.php'; ?>
    <script>
        // JavaScript code
        setTimeout(() => {
            const box = document.getElementById('messages');
            if (box) {
                box.style.display = 'none';
            }
        }, 5000);
    </script>
</body>

</html>