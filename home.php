<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   setcookie('user_id', create_unique_id(),time() + 60*60*24*30, '/');
   header('location:home.php');
}

if(isset($_POST['check'])){

   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);

   $total_rooms = 0;

   $check_bookings = $conn->prepare("SELECT * FROM 'bookings' WHERE check_in = ?");
   $check_bookings->execute([$check_in]);

   while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
      $total_rooms += $fetch_bookings['rooms'];
   }
   if($total_rooms >= 30){
      $warning_msg[] = 'rooms are not available';
   }else{
      $success_msg[] = 'rooms are available';
   }

}

if(isset($_POST['book'])){

   $booking_id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $rooms = $_POST['rooms'];
   $rooms = filter_var($rooms, FILTER_SANITIZE_STRING);
   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);
   $check_out = $_POST['check_out'];
   $check_out = filter_var($check_out, FILTER_SANITIZE_STRING);
   $adults = $_POST['adults'];
   $adults = filter_var($adults, FILTER_SANITIZE_STRING);
   $childs = $_POST['childs'];
   $childs = filter_var($childs, FILTER_SANITIZE_STRING);

   $total_rooms = 0;

   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
   $check_bookings->execute([$check_in]);

   while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
      $total_rooms += $fetch_bookings['rooms'];
   }

   if($total_rooms >= 30){
      $warning_msg[] = 'rooms are not available';
   }else{

      $verify_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE user_id = ? AND name = ? AND email = ? AND number = ? AND rooms = ? AND check_in = ? AND check_out = ? AND adults = ? AND childs = ?");
      $verify_bookings->execute([$user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs]);

      if($verify_bookings->rowCount() > 0){
         $warning_msg[] = 'room booked alredy!';
      }else{
         $book_room = $conn->prepare("INSERT INTO `bookings`(booking_id, user_id, name, email, number, rooms, check_in, check_out, adults, childs) VALUES(?,?,?,?,?,?,?,?,?,?)");
         $book_room->execute([$booking_id, $user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs]);
         $success_msg[] = 'room booked successfully!';
      }

   }

}

if(isset($_POST['send'])){

   $id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $message = $_POST['message'];
   $message = filter_var($message, FILTER_SANITIZE_STRING);

   $verify_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
   $verify_message->execute([$name, $email, $number, $message]);

   if($verify_message->rowCount() > 0){
      $warning_msg[] = 'message sent already!';
   }else{
      $insert_message = $conn->prepare("INSERT INTO `messages`(id, name, email, number, message) VALUES(?,?,?,?,?)");
      $insert_message->execute([$id, $name, $email, $number, $message]);
      $success_msg[] = 'message send successfully!';
   }

}

?>



















<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Royal Lake View</title>

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="home.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>
   

<!-- home section starts  -->

<section class="home" id="home">

    <div class="swiper home-slider">
 
       <div class="swiper-wrapper">
 
          <div class="box swiper-slide">
             <img src="Images/superior twin 2.jpeg" alt="">
             <div class="flex">
                <h3>Luxurious Rooms</h3>
                <a href="#availability" class="btn">check availability</a>
             </div>
          </div>
 
          <div class="box swiper-slide">
             <img src="Images/swimming pool.jpeg" alt="">
             <div class="flex">
                <h3>Swimming Pool</h3>
                <a href="#reservation" class="btn">make a reservation</a>
             </div>
          </div>
 
          <div class="box swiper-slide">
             <img src="Images/lobby.jpeg" alt="">
             <div class="flex">
                <h3>Luxurious Hall</h3>
                <a href="#contact" class="btn">contact us</a>
             </div>
          </div>

          <div class="box swiper-slide">
            <img src="Images/restaurant.jpeg" alt="">
            <div class="flex">
               <h3>Dining Area</h3>
               <a href="#reservation" class="btn">make a reservation</a>
            </div>

        
         </div>
 
       </div>
 
       <div class="swiper-button-next"></div>
       <div class="swiper-button-prev"></div>
 
    </div>
 
 </section>
 
 <!-- home section ends -->
 <!-- availability section starts  -->

<section class="availability" id="availability">

    <form action="" method="post">
       <div class="flex">
          <div class="box">
             <p>Check In</p>
             <input type="date" name="check_in" class="input" required>
          </div>
          <div class="box">
             <p>Check Out</p>
             <input type="date" name="check_out" class="input" required>
          </div>
          <div class="box">
             <p>Adult</p>
             <select name="adults" class="input" required>
                <option value="1">1 adult</option>
                <option value="2">2 adults</option>
                <option value="3">3 adults</option>
                <option value="4">4 adults</option>
             </select>
          </div>
          <div class="box">
             <p>Children</p>
             <select name="childs" class="input" required>
                <option value="-">0 child</option>
                <option value="1">1 child</option>
                <option value="2">2 children</option>
             </select>
          </div>
          <div class="box">
             <p>Room Type</p>
             <select name="rooms" class="input" required>
             <option value="1">1 room</option>
             <option value="2">2 rooms</option>
               <option value="3">3 rooms</option>
               <option value="4">4 rooms</option>
               <option value="5">5 rooms</option>
               <option value="6">6 rooms</option>
             </select>
          </div>
       </div>
       <input type="submit" value="check availability" name="check" class="btn">
    </form>
 
 </section>
 
 <!-- availability section ends -->
 
 <!-- about section starts  -->

<section class="about" id="about">

    <div class="row">
       <div class="image">
          <img src="Images/reception.jpeg" alt="">
       </div>
       <div class="content">
          <h3>reception</h3>
          <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Animi laborum maxime eius aliquid temporibus unde?</p>
          <a href="#contact" class="btn">contact us</a>
       </div>
    </div>
 
    <div class="row revers">
       <div class="image">
          <img src="Images/dining.jpeg" alt="">
       </div>
       <div class="content">
          <h3>dining area</h3>
          <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Animi laborum maxime eius aliquid temporibus unde?</p>
          <a href="#contact" class="btn">contact us</a>
       </div>
    </div>
 
    <div class="row">
       <div class="image">
          <img src="Images/swimming pool sunset.jpeg" alt="">
       </div>
       <div class="content">
          <h3>swimming pool</h3>
          <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Animi laborum maxime eius aliquid temporibus unde?</p>
          <a href="#contact" class="btn">contact us</a>
       </div>
    </div>

    <div class="row revers">
        <div class="image">
           <img src="Images/spa.jpeg" alt="">
        </div>
        <div class="content">
           <h3>Spa</h3>
           <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Animi laborum maxime eius aliquid temporibus unde?</p>
           <a href="#contact" class="btn">contact us</a>
        </div>
     </div>
  

    
 
 <!-- about section ends -->

 <!-- reservation section starts  -->

<section class="reservation" id="reservation">

    <form action="" method="post">
       <h3>make a reservation</h3>
       <div class="flex">
       <div class="box">
            <p>your name <span>*</span></p>
            <input type="text" name="name" maxlength="20" required placeholder="enter your name" class="input">
         </div>
         <div class="box">
            <p>your email <span>*</span></p>
            <input type="email" name="email" maxlength="20" required placeholder="enter your email" class="input">
         </div>
         <div class="box">
            <p>your number <span>*</span></p>
            <input type="number" name="number" maxlength="10" min="0" max="9999999999" required placeholder="enter your number" class="input">
         </div>
       <div class="box">
             <p>Room Type</p>
             <select name="rooms" class="input" required>
             <option value="1">1 room</option>
               <option value="2">2 rooms</option>
               <option value="3">3 rooms</option>
               <option value="4">4 rooms</option>
               <option value="5">5 rooms</option>
               <option value="6">6 rooms</option>
             </select>
          </div>
          <div class="box">
             <p>Check In</p>
             <input type="date" name="check_in" class="input" required>
          </div>
          <div class="box">
             <p>Check Out</p>
             <input type="date" name="check_out" class="input" required>
          </div>
          <div class="box">
             <p>Adult</p>
             <select name="adults" class="input" required>
                <option value="1">1 adult</option>
                <option value="2">2 adults</option>
                <option value="3">3 adults</option>
                <option value="4">4 adults</option>
             </select>
          </div>
          <div class="box">
             <p>Children</p>
             <select name="childs" class="input" required>
                <option value="-">0 child</option>
                <option value="1">1 child</option>
                <option value="2">2 children</option>
             </select>
          </div>
          
       </div>
       <input type="submit" value="Reserve Now" name="book" class="btn">
    </form>
 
 </section>
 
 <!-- reservation section ends -->
 
 <!-- gallery section starts  -->

<section class="gallery" id="gallery">

    <div class="swiper gallery-slider">
       <div class="swiper-wrapper">
          <img src="Images/superior king 3.jpeg" class="swiper-slide" alt="">
          <img src="Images/outdoor area .jpeg" class="swiper-slide" alt="">
          <img src="Images/sunset.jpeg" class="swiper-slide" alt="">
          <img src="Images/gym.jpeg" class="swiper-slide" alt="">
          <img src="Images/front view.jpeg" class="swiper-slide" alt="">
          <img src="Images/view.jpeg" class="swiper-slide" alt="">
       </div>
       <div class="swiper-pagination"></div>
    </div>
 
 </section>
 
 <!-- gallery section ends -->

 <!-- contact section starts  --> 

 <section class="contact" id="contact">

    <div class="row">
 
       <form action="" method="post">
          <h3>send us message</h3>
          <input type="text" name="name" required maxlength="20" placeholder="Name" class="box">
          <input type="email" name="email" required maxlength="20" placeholder="Email" class="box">
          <input type="number" name="number" required maxlength="10" min="0" max="9999999999" placeholder=" Phone Number" class="box">
          <textarea name="msg" class="box" required maxlength="100" placeholder="Message" cols="30" rows="10"></textarea>
          <input type="submit" value="send message" name="send" class="btn">
       </form>
    </div>

    <!-- contact section ends -->

<?php include 'components/footer.php'; ?>
 
 <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

 <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

 <!-- custom js file link  -->
<script src="script.js"></script>

<?php include 'components/message.php'; ?>

</body>
</html>