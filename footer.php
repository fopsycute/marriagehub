
  <footer id="footer" class="footer dark-background">

    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-4 col-md-6 footer-about">
           <a href="<?php echo $siteurl; ?>" class="logo d-flex align-items-center">
             <img src="<?php echo $siteurl; ?>assets/img/<?php echo $siteimg; ?>" alt="">
             <span><?php echo $sitename; ?></span>
          </a>
          <div class="footer-contact pt-3">
            <p><?php echo $site_address; ?></p>
           
            <p class="mt-3"><strong>Phone:</strong> <span><?php echo $sitenumber; ?></span></p>
            <p><strong>Email:</strong> <span><?php echo $sitemail; ?></span></p>
          </div>
          <div class="social-links d-flex mt-4">
            <a href=""><i class="bi bi-twitter-x"></i></a>
            <a href=""><i class="bi bi-facebook"></i></a>
            <a href=""><i class="bi bi-instagram"></i></a>
            <a href=""><i class="bi bi-linkedin"></i></a>
          </div>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Company</h4>
          <ul>
            <li><a href="<?php echo $siteurl; ?>index.php">Home</a></li>
            <li><a href="<?php echo $siteurl; ?>about.php">About us</a></li>
            <li><a href="<?php echo $siteurl; ?>terms.php">Terms of service</a></li>
            <li><a href="<?php echo $siteurl; ?>privacy.php">Privacy policy</a></li>
             <li><a href="<?php echo $siteurl; ?>why-us.php">Why Us</a></li>
              <li><a href="<?php echo $siteurl; ?>cookie-policy.php">Cookie Policy</a></li>
          </ul>
        </div>
   <div class="col-lg-2 col-md-3 footer-links">
          <h4>Our Services</h4>
          <ul>
            <li><a href="<?php echo $siteurl; ?>blog.php">Blog</a></li>
            <li><a href="<?php echo $siteurl; ?>questions-answers.php">Questions & Answers</a></li>
            <li><a href="<?php echo $siteurl; ?>all-groups.php">Groups & Tribes</a></li>
            <li><a href="<?php echo $siteurl; ?>events.php">Events & Programs</a></li>
            <li><a href="<?php echo $siteurl; ?>marketplace.php">Marketplace</a></li>
            <li><a href="<?php echo $siteurl; ?>vendors.php">Vendor Directory</a></li>
            <li><a href="<?php echo $siteurl; ?>find-therapist.php">Find Therapists & Counselors</a></li>
            <li><a href="<?php echo $siteurl; ?>advertise.php">Advertise With Us</a></li>
          </ul>
        </div>
<div class="col-lg-4 col-md-3">
<div class="newsletter-box">
    <h3>Subscribe to Our Newsletter</h3>
    <form id="newsletterForm" method="POST">
        <input type="email" id="newsletter_email" name="email" class="form-control" placeholder="Enter your email" required>
        <br>
        <input type="hidden" name="action" value="subscribeNewsletter">
        <button type="submit" class="btn btn-primary" id="submitNewsletter">SUBMIT</button>
         <p id="newsletter_message" class="mt-1"></p>
    </form>
   
</div>
</div>
     
    <!---

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Hic solutasetp</h4>
          <ul>
            <li><a href="#">Molestiae accusamus iure</a></li>
            <li><a href="#">Excepturi dignissimos</a></li>
            <li><a href="#">Suscipit distinctio</a></li>
            <li><a href="#">Dilecta</a></li>
            <li><a href="#">Sit quas consectetur</a></li>
          </ul>
        </div>
        -->
<!----
        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Nobis illum</h4>
          <ul>
            <li><a href="#">Ipsam</a></li>
            <li><a href="#">Laudantium dolorum</a></li>
            <li><a href="#">Dinera</a></li>
            <li><a href="#">Trodelas</a></li>
            <li><a href="#">Flexo</a></li>
          </ul>
        </div>
--->
      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename"><?php echo $sitename; ?></strong> <span>All Rights Reserved</span></p>
      <div class="credits">
        <!-- All the links in the footer should remain intact. -->
        <!-- You can delete the links only if you've purchased the pro version. -->
        <!-- Licensing information: https://bootstrapmade.com/license/ -->
        <!-- Purchase the pro version with working PHP/AJAX contact form: [buy-url] -->
       
      </div>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Vendor JS Files -->
  <script src="<?php echo $siteurl; ?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="<?php echo $siteurl; ?>assets/vendor/php-email-form/validate.js"></script>
  <script src="<?php echo $siteurl; ?>assets/vendor/aos/aos.js"></script>
  <script src="<?php echo $siteurl; ?>assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="<?php echo $siteurl;?>assets/vendor/glightbox/js/glightbox.min.js"></script>
  <!-- DataTables JS -->


  <!-- Main JS File -->
    <!-- VPay Payment Library - dynamically loaded based on domain -->
    <script>
        const vpayDomain = document.getElementById('vpay-domain')?.value || 'sandbox';
        const vpayScriptUrl = vpayDomain === 'live' 
            ? 'https://dropin.vpay.africa/dropin/v1/initialise.js'
            : 'https://dropin-sandbox.vpay.africa/dropin/v1/initialise.js';
        
        const vpayScript = document.createElement('script');
        vpayScript.src = vpayScriptUrl;
        vpayScript.async = true;
        document.head.appendChild(vpayScript);
    </script>
     <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
     <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>
    <!-- VPay Payment Helper -->
    <script src="<?php echo $siteurl; ?>assets/js/vpay-helper.js"></script>
  <script src="<?php echo $siteurl; ?>assets/js/main.js"></script>
    <script src="<?php echo $siteurl; ?>assets/js/api.js"></script>
     <script src="<?php echo $siteurl; ?>assets/js/other.js"></script>
     <script src="<?php echo $siteurl; ?>assets/js/state-select.js"></script>
    <!-- jsPDF & html2canvas -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
   

        <?php
    // Get current page filename
    $currentPage = basename($_SERVER['PHP_SELF']);
    ?>
    
    <?php if ($currentPage == 'verify.php') : ?>
    <script src="<?php echo $siteurl; ?>assets/js/reset.js"></script>
    <?php endif; ?>

<?php include "sidebar-ad.php"; ?>


</body>
</html>