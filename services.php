<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
  <!-- Basic -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Mobile Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <!-- Site Metas -->
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="author" content="" />

  <title>Barangay Blue Ridge B</title>

  <!-- slider stylesheet -->
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.1.3/assets/owl.carousel.min.css" />

  <!-- bootstrap core css -->
  <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />

  <!-- fonts style -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:400,600,700&display=swap" rel="stylesheet">
  <!-- Custom styles for this template -->
  <link href="css/style.css" rel="stylesheet" />
  <!-- responsive style -->
  <link href="css/responsive.css" rel="stylesheet" />
  <link href="css/services.css" rel="stylesheet" />
  <!-- AOS Animation CSS -->
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" />
</head>

<body class="sub_page">

  <div class="hero_area">
    <!-- header section strats -->
    <?php include 'header.php'; ?>
    <!-- end header section -->
  </div>


  <!-- service section -->
  <section class="service_section">
    <div class="container">
      <div class="service_container">
          <div class="box" data-aos="fade-up" data-aos-delay="100">
            <div class="img-box">
              <img src="images/s1.png" class="img1" alt="">
            </div>
            <div class="detail-box">
              <h5>
                Message Barangay Authorities
              </h5>
              <p>
                Ask questions and get assistance from the barangay authorities
              </p>
              <a href="messages.php" class="btn btn-primary mt-2">Go to Messages</a>
            </div>
          </div>
          <div class="box active" data-aos="fade-up" data-aos-delay="200">
            <div class="img-box">
              <img src="images/s2.png" class="img1" alt="">
            </div>
            <div class="detail-box">
              <h5>
                Reserve Facilities and Items
              </h5>
              <p>
                Reserve barangay facilities and items for community events and activities
              </p>
              <a href="reservation.php" class="btn btn-primary mt-2">Go to Reservations</a>
            </div>
          </div>
          <div class="box" data-aos="fade-up" data-aos-delay="300">
            <div class="img-box">
              <img src="images/s3.png" class="img1" alt="">
            </div>
            <div class="detail-box">
              <h5>
                View Announcements
              </h5>
              <p>
                Stay updated with the latest news and events in our community.
              </p>
              <a href="announcement.php" class="btn btn-primary mt-2">Go to Announcements</a>
            </div>
          </div>
      <!-- <div class="btn-box">
        <a href="">
          Read More
        </a>
      </div> -->
    </div>
  </section>
  <!-- end service section -->


  <!-- info section -->
  <?php include 'info_section.php'; ?>
  <!-- end info section -->

  <!-- footer section -->
  <?php include 'footer.php'; ?>
  <!-- footer section -->


  <script src="js/jquery-3.4.1.min.js"></script>
  <script src="js/bootstrap.js"></script>
  <!-- AOS Animation JS -->
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script>
    AOS.init();
  </script>
</body>

</html>