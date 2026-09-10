<?php
require "_base.php";
// burger_home.php
$_title = "About Us";
include "_head.php";



?>
<!DOCTYPE html>
<html lang="en">
<head>

</header>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/app.css">
    <link rel="stylesheet" href="css/aboutUs.css">
    <title>About Us</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #fff8e1;
      text-align: center;
      margin: 0;
      padding: 0;
    }

    .intro {
      margin: 30px auto;
      font-size: 18px;
      width: 80%;
      animation: fadeInUp 1s ease-in-out;
    }
    .burger-float {
      width: 100px;
      animation: floatBurger 3s ease-in-out infinite;
      margin-top: 20px;
    }
    .menu-container {
      margin-top: 40px;
      width: 80%;
      margin-left: auto;
      margin-right: auto;
    }
    .burger-item {
      background-color: #fff3e0;
      margin: 20px auto;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
      animation: bounceIn 1s ease forwards;
    }
    .burger-item:nth-child(1) { animation-delay: 0.3s; }
    .burger-item:nth-child(2) { animation-delay: 0.6s; }
    .burger-item:nth-child(3) { animation-delay: 0.9s; }
    .burger-item:nth-child(4) { animation-delay: 1.2s; }
    .burger-item h3 {
      margin: 0;
    }
    .burger-item p {
      margin: 5px 0 0;
    }
    .story {
      position: absolute;
      margin: 10px;
      margin-left: 18%;
      padding: 20px;
      position: relative;
      width: 65%;
      text-align: left;
      animation: fadeInUp 2s ease-in-out;
      background-color: #fff8e1;
    }

.story p {
  font-size: larger;

}

    @keyframes floatBurger {
      0% { transform: translateY(0); }
      50% { transform: translateY(-15px); }
      100% { transform: translateY(0); }
    }
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    @keyframes popIn {
      from {
        opacity: 0;
        transform: scale(0.8);
      }
      to {
        opacity: 1;
        transform: scale(1);
      }
    }
    @keyframes bounceIn {
      0% {
        transform: scale(0.9);
        opacity: 0;
      }
      60% {
        transform: scale(1.05);
        opacity: 1;
      }
      100% {
        transform: scale(1);
      }
    }
  </style>
</head>
<body>
  <h1 style="font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;">Welcome XBURGER</h1>
  <div class="intro">
    <p style="font-family: 'Times New Roman', Times, serif;">Enjoy delicious, custom-made burgers from the comfort of your own home!</p>
  </div>
  <div class="floating-burger">
  <img src="floating-burger/giphy.gif" alt="floating burger" class="burger-float">
</div>
  

  <div class="story">
    <h2 style="font-family:'Courier New', Courier, monospace;">XBURGER</h2>
    <p style="font-family: 'Times New Roman', Times, serif;">
    Every day, more than 11 million guests visit XBURGER restaurants around the world. And they do so because our restaurants are known for serving high-quality, great tasting, and affordable food. Founded in 1954, XBURGER is the second largest fast food hamburger chain in the world.

XBURGER commenced operations in Malaysia in 1997 with the opening of its first restaurant at Sungai Buloh Overhead Bridge. Today, Cosmo Restaurants Sdn Bhd operates more than 120 XBURGER restaurants in Malaysia where customers across the country can enjoy the great and healthy flame-grilled taste of XBurger products.

Our commitment to premium ingredients, signature recipes, and family-friendly dining experiences is what has defined our brand for more than 50 successful years.
    </p>
  </div>

  <div class="story">
    <h2 style="font-family:'Courier New', Courier, monospace;">3G CAPITAL</h2>
    <p style="font-family: 'Times New Roman', Times, serif;">
    In 2010, 3G Capital, a global multi-million dollar investment firm focused on long term value creation, purchased XBurger Corporation, making it a privately-held company.
    </p>
  </div>

</body>


</html>

<?php
include "_foot.php";
?>