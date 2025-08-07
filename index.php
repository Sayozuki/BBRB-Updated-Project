<?php ?>
<!DOCTYPE html>
<html lang="en">

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
    <link rel="stylesheet" href="css/bootstrap.css">

    <!-- fonts style -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,600,700&display=swap" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet" />
    <!-- responsive style -->
    <link href="css/responsive.css" rel="stylesheet" />
    <link href="css/index.css" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.4.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- AOS Animation CSS and JS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
</head>

<body>

    <div class="hero_area">

        <?php include 'header.php'; ?>

    <!-- slider section -->
    <section class="slider_section" data-aos="fade-up">
        <div class="container">
            <div class="row">
                <div class="col-md-6" data-aos="fade-right">
                    <div class="detail_box">
                        <h1 data-aos="fade-down">
                            Barangay <br>
                            Blue Ridge B <br>
                        </h1>
                        <p data-aos="fade-up">
                            Barangay Blue Ridge B is a vibrant community located in Quezon City, Philippines. We are dedicated to providing our residents with the best services and facilities to enhance their quality of life. Our barangay is committed to fostering a sense of community and ensuring the well-being of all our residents.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end slider section -->
    </div>

    <!-- service section -->
    <section class="service_section navy-bg" data-aos="fade-up">
        <div class="container">
            <div class="heading_container animate-text" data-aos="fade-right">
                <h2 class="animate-text">Blue Ridge B Services:</h2>
            </div>
        <div class="service_container" data-aos="fade-up">
            <div class="box" data-aos="zoom-in">
                    <div class="img-box" data-aos="flip-left">
                        <img src="images/s1.png" class="img1" alt="">
            </div>
            <div class="detail-box" data-aos="fade-up">
                <h5>
                    Message Barangay Authorities
                </h5>
                <p>
                    Ask questions and get assistance from the barangay authorities
                </p>
                <a href="messages.php" class="btn btn-primary mt-2" data-aos="fade-right">Go to Messages</a>
            </div>
        </div>
            <div class="box active" data-aos="zoom-in">
                <div class="img-box" data-aos="flip-left">
                    <img src="images/s2.png" class="img1" alt="">
            </div>
            <div class="detail-box" data-aos="fade-up">
                <h5>
                    Reserve Facilities and Items
                </h5>
                <p>
                    Reserve barangay facilities and items for community events and activities
                </p>
                <a href="reservation.php" class="btn btn-primary mt-2" data-aos="fade-right">Go to Reservations</a>
            </div>
        </div>
        <div class="box" data-aos="zoom-in">
            <div class="img-box" data-aos="flip-left">
                <img src="images/s3.png" class="img1" alt="">
            </div>
            <div class="detail-box" data-aos="fade-up">
                <h5>
                    View Announcements
                </h5>
                <p>
                    Stay updated with the latest news and events in our community.
                </p>
                <a href="announcement.php" class="btn btn-primary mt-2" data-aos="fade-right">Go to Announcements</a>
            </div>
        </div>
    </div>
</div>
    </section>
    <!-- end service section -->

    <!-- blog section -->
    <?php
    require 'db.php';

    // Fetch the two most recent announcements from the database
    $stmt = $pdo->query("SELECT title, content, genre, created_at, thumbnail FROM announcements ORDER BY created_at DESC LIMIT 2");
    $recent_announcements = $stmt->fetchAll();
    ?>

    <section class="blog_section navy-bg layout_padding" data-aos="fade-up">
        <div class="container">
            <div class="heading_container animate-text" data-aos="fade-right">
                <h2 class="animate-text">Announcements</h2>
            </div>
            <div class="row">
                <?php foreach ($recent_announcements as $announcement): ?>
                <div class="col-md-6" data-aos="fade-up">
                    <div class="box announcement-card">
                        <div class="img-box">
                            <img src="<?php echo htmlspecialchars($announcement['thumbnail']); ?>" alt="Announcement Image" class="img-fluid">
                        </div>
                        <div class="detail-box">
                            <h5><?php echo htmlspecialchars($announcement['title']); ?></h5>
                            <p><?php echo htmlspecialchars($announcement['genre']); ?> | <?php echo htmlspecialchars($announcement['created_at']); ?></p>
                            <p><?php echo htmlspecialchars($announcement['content']); ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <!-- end blog section -->

    <!-- info section -->
    <?php include 'info_section.php'; ?>
    <!-- end info section -->

    <!-- footer section -->
    <?php include 'footer.php'; ?>
    <!-- footer section -->

    <script>
AOS.init({
    duration: 1000, // Animation duration in milliseconds
    once: true, // Whether animation should happen only once
});
</script>
</body>

</html>