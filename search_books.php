<?php
include 'config.php';

session_start();

if (isset($_SESSION['user_name'])) {
   $user_id = $_SESSION['user_id'];

   if (isset($_POST['add_to_cart'])) {
      $book_name = $_POST['book_name'];
      $book_id = $_POST['book_id'];
      $book_image = $_POST['book_image'];
      $book_price = $_POST['book_price'];
      $book_quantity = '1';

      $total_price = number_format($book_price * $book_quantity);
      $stmt = $conn->prepare("SELECT * FROM cart WHERE bid= ? AND user_id= ?");
      $stmt->bind_param("ii", $book_id, $user_id);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows > 0) {
         $message[] = 'This Book is already in your cart';
      } else {
         $stmt = $conn->prepare("INSERT INTO cart (user_id, book_id, name, price, image, quantity, total) VALUES (?, ?, ?, ?, ?, ?, ?)");
         $stmt->bind_param("iisdssi", $user_id, $book_id, $book_name, $book_price, $book_image, $book_quantity, $total_price);
         $stmt->execute();
         $message[] = 'Book Added Successfully';
         header('location:index.php');
         exit();
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Search Page</title>

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="css/style.css">
   <style>
      .search-form form {
         max-width: 1200px;
         margin: 30px auto;
         display: flex;
         gap: 15px;
      }

      .search-form form .search_btn {
         margin-top: 0;
      }

      .search-form form .box {
         width: 100%;
         padding: 12px 14px;
         border: 2px solid rgb(0, 167, 245);
         font-size: 20px;
         color: black;
         border-radius: 5px;
      }

      .search_btn {
         display: inline-block;
         padding: 10px 25px;
         cursor: pointer;
         color: white;
         font-size: 18px;
         border-radius: 5px;
         text-transform: capitalize;
         background-color: rgb(0, 167, 245);
      }
      
      .msg {
         text-align: center;
      }
      .empty{
         text-align: center; 
      }
   </style>
</head>

<body>

   <?php include 'index_header.php'; ?>

   <section class="search-form">
      <form action="" method="POST">
         <input type="text" class="box" name="search_box" placeholder="Search products...">
         <input type="submit" name="search_btn" value="Search" class="search_btn">
      </form>
   </section>

   <div class="msg">
      <?php
      if (isset($_POST['search_btn'])) {
         $search_box = htmlspecialchars($_POST['search_box'], ENT_QUOTES, 'UTF-8');
         echo '<h4>Search Result for "' . $search_box . '" is:</h4>';
      };
      ?>
   </div>

   <section class="show-products">
      <div class="box-container">
         <?php
         if (isset($_POST['search_btn'])) {
            $search_box = htmlspecialchars($_POST['search_box'], ENT_QUOTES, 'UTF-8');
            $stmt = $conn->prepare("SELECT * FROM book_info WHERE name LIKE ? OR title LIKE ? OR category LIKE ?");
            $search_param = "%" . $search_box . "%";
            $stmt->bind_param("sss", $search_param, $search_param, $search_param);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
               while ($fetch_book = $result->fetch_assoc()) {
         ?>
                  <div class="box" style="width: 255px;height: 342px;">
                     <a href="book_details.php?details=<?php echo $fetch_book['bid']; ?>&name=<?php echo $fetch_book['name']; ?>">
                        <img style="height: 200px;width: 125px;margin: auto;" class="books_images" src="added_books/<?php echo $fetch_book['image']; ?>" alt="">
                     </a>
                     <div style="text-align:left;">
                        <div class="name" style="font-size: 12px;">Author: <?php echo $fetch_book['title']; ?></div>
                        <div style="font-weight: 500; font-size:18px; " class="name">Name: <?php echo $fetch_book['name']; ?></div>
                     </div>
                     <div class="price">Price: ₹ <?php echo $fetch_book['price']; ?>/-</div>
                     <form action="" method="POST">
                        <input type="hidden" name="book_name" value="<?php echo $fetch_book['name']; ?>">
                        <input type="hidden" name="book_id" value="<?php echo $fetch_book['bid']; ?>">
                        <input type="hidden" name="book_image" value="<?php echo $fetch_book['image']; ?>">
                        <input type="hidden" name="book_price" value="<?php echo $fetch_book['price']; ?>">
                        <button type="submit" name="add_to_cart"><img src="./images/cart2.svg" alt="Add to cart"></button>
                        <a href="book_details.php?details=<?php echo $fetch_book['bid']; ?>&name=<?php echo $fetch_book['name']; ?>" class="update_btn">Know More</a>
                     </form>
                  </div>
         <?php
               }
            } else {
               echo '<p class="empty">Could not find "' . $search_box . '"!</p>';
            }
         };
         ?>
      </div>
   </section>

   <?php include 'index_footer.php'; ?>

   <script src="js/script.js"></script>

</body>

</html>
