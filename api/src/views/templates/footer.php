</main> <!-- Closes the main tag opened in header.php -->

<!-- ======================================================= -->
<!-- NEW SITE FOOTER -->
<!-- ======================================================= -->
<footer class="site-footer">
    <div class="container">
        <!-- Main Footer Content -->
        <div class="row pt-5 pb-5">
            <!-- Column 1: Logo and Description -->
            <div class="col-lg-4 mb-4 mb-lg-0">
                <div class="footer-logo-container mb-3">
                    <img src="assets/images/logo.png" alt="<?php echo SITE_NAME; ?>" style="height: 250px; filter: brightness(0) invert(1);">
                </div>
                <p class="text-secondary-light">
                    Authentic Tunisian extra virgin olive oil, rooted in generations of Mediterranean heritage.
                </p>
            </div>

            <!-- Column 2: Company Links -->
            <div class="col-lg-2 col-md-3 col-6 mb-4 mb-lg-0">
                <h5 class="footer-heading">COMPANY</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Workshops</a></li>
                    <li><a href="#">Our Services</a></li>
                    <li><a href="#">Our Team</a></li>
                    <li><a href="#">What We Do</a></li>
                </ul>
            </div>

            <!-- Column 3: More Links -->
            <div class="col-lg-2 col-md-3 col-6 mb-4 mb-lg-0">
                <h5 class="footer-heading">MORE</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="#">Pricing</a></li>
                    <li><a href="#">Testimonials</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Contact Us</a></li>
                </ul>
            </div>

            <!-- Column 4: Newsletter -->
            <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                <h5 class="footer-heading-large">Join Our Newsletter</h5>
                <p class="text-secondary-light">Sign up to receive our latest news and discounts</p>
                <form action="#" class="newsletter-form">
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Email" required>
                        <button type="submit" class="btn-submit">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <hr class="footer-hr">

        <!-- Bottom Footer Bar -->
        <div class="row pt-4 pb-4 align-items-center">
            <!-- Social Icons -->
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-pinterest-p"></i></a>
                </div>
            </div>
            <!-- Copyright -->
            <div class="col-md-6 text-center text-md-end">
                <p class="copyright mb-0">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All Rights Reserved. Licensing</p>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        once: true,
        easing: 'ease-out'
    });
</script>
<!-- Custom JS -->
<script src="assets/js/script.js"></script>
</body>
</html>
