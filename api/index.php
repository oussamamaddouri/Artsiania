<?php
// Include header
require_once __DIR__ . '/../src/views/templates/header.php';

// --- PRODUCT DATA ARRAY ---
$products = [
    [
        'name' => 'Pot With Graphics',
        'price' => '15.00',
        'image' => 'plant.png'
    ],
    [
        'name' => 'Plate Set',
        'price' => '45.00',
        'image' => 'plate.png'
    ],
    [
        'name' => 'Vivid Vase',
        'price' => '29.00',
        'image' => 'vase.png'
    ],
    [
        'name' => 'Stone Style Vase',
        'price' => '32.00',
        'sale_price' => '25.00',
        'image' => 'vase2.png'
    ],
    [
        'name' => 'Colorful Pot Set',
        'price' => '19.00',
        'image' => 'pot.png'
    ],
    [
        'name' => 'Matches holder',
        'price' => '12.00',
        'sale_price' => '9.00',
        'image' => 'holder.png'
    ],
    [
        'name' => 'Flower Pot',
        'price' => '15.00',
        'sale_price' => '11.00',
        'image' => 'flower.png'
    ],
    [
        'name' => 'Bowl Set',
        'price' => '45.00',
        'image' => 'bowl.png'
    ]
];
?>

<!-- 1. LIGHT TOP SPLASH -->
<section class="top-splash-section">
    <div class="container-fluid p-0">
        <div class="splash-img-wrapper">
            <img src="assets/images/heritage-splash.png" alt="Heritage" class="w-100">
        </div>
    </div>
</section>


<!-- 2. COMPACT SCROLL-REVEAL SECTION -->
<section id="gsap-scroll-area" class="intro-transitional-section" style="background-color: #FFFFFF; padding: 100px 0;">
    <div class="container" style="height: 600px; display: flex; align-items: center;">
        <div class="row align-items-center w-100">
            
            <!-- LEFT COLUMN (Swapping Texts) -->
            <div class="col-lg-6">
                <p class="section-supertitle">(00)</p>
                <h2 class="section-title" style="font-size: 3.5rem; line-height: 1.1; margin-bottom: 2rem;">
                    The Tradition <br>of Cold Press
                </h2>

                <div class="text-swap-container" style="position: relative; height: 180px;">
                    <p class="stxt s-txt1" style="position: absolute; opacity: 1;">It serves as an elegant bridge between the opening visual and our heritage story.</p>
                    <p class="stxt s-txt2" style="position: absolute; opacity: 0;">Explore our journey that connects the sun-drenched Tunisian soil.</p>
                    <p class="stxt s-txt3" style="position: absolute; opacity: 0;">Every olive is carefully selected by local artisans.</p>
                    <p class="stxt s-txt4" style="position: absolute; opacity: 0;">Liquid gold—pure extra virgin olive oil cold-pressed.</p>
                </div>
            </div>

            <!-- RIGHT COLUMN (Swapping Images) -->
            <div class="col-lg-6 text-center">
                <div class="image-swap-container" style="position: relative; height: 400px; display: flex; align-items: center; justify-content: center;">
                    <img src="assets/images/bottle-hero.png" class="spic s-pic1" style="position: absolute; height: 190%; opacity: 1;">
                    <img src="assets/images/bottle-hero-hero.png" class="spic s-pic2" style="position: absolute; height: 170%; opacity: 0;">
                    <img src="assets/images/22.png" class="spic s-pic3" style="position: absolute; height: 170%; opacity: 0;">
                    <img src="assets/images/44.png" class="spic s-pic4" style="position: absolute; height: 195%; opacity: 0;">
                </div>
            </div>

        </div>
    </div>
</section>
<!-- 3. DARK BRAND SECTION -->
<div class="dark-content-section" style="background-color: #4A3B2A; color: #FFFFFF; padding-top: 80px;">
    <div class="container">

        <!-- ROW: CARTHAGE CAVERNE MAIN HERO -->
        <div class="row align-items-start mb-0">
            <div class="col-lg-7">
                <div class="title-mask">
                    <h1 class="hero-title animate-title text-white">Carthage Caverne</h1>
                </div>
            </div>

            <div class="col-lg-5 pt-lg-4">
                <p class="hero-text text-white-50">
                    Join us in discovering authentic Tunisian extra virgin olive oil,
                    produced from carefully selected olives and rooted in generations
                    of Mediterranean heritage.
                </p>
            </div>
        </div>

        <!-- HERO IMAGE -->
        <div class="hero-image-container pt-0">
            <img src="assets/images/hand.png"
                 alt="Hand image"
                 class="img-fluid"
                 data-aos="zoom-in"
                 data-aos-duration="1500">
        </div>

    </div>
</div>

<!-- DARK THEME WRAPPER START -->
<div class="dark-content-section">

    <!-- Section (01): Taste Olive Oil -->
    <section class="content-section py-5">
        <div class="container">
            <div class="row align-items-center">

                <div class="col-lg-5">
                    <p class="section-supertitle dark-theme">(01)</p>

                    <h2 class="section-title text-white">
                        Taste Olive Oil
                    </h2>

                    <p class="text-white-50">
                        Whether you’re a culinary enthusiast, a professional chef,
                        or someone who appreciates pure natural products,
                        Carthage Caverne brings you high-quality extra virgin olive oil
                        directly from Tunisia’s sun-rich groves.
                        Discover rich aromas, balanced flavor,
                        and the essence of Mediterranean tradition in every drop.
                    </p>
                </div>

                <div class="col-lg-7">
                    <div class="image-collage">

                        <div class="image-wrapper image-1 shadow">
                            <img src="assets/images/1.png"
                                 class="collage-img"
                                 alt="">
                        </div>

                        <div class="image-wrapper image-2 shadow">
                            <img src="assets/images/2.png"
                                 alt=""
                                 class="img-fluid">
                        </div>

                        <div class="white-square square-1"></div>
                        <div class="white-square square-2"></div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Section (02): About Us -->
    <section class="content-section py-5">
        <div class="container">
            <div class="row">

                <div class="col-lg-6 position-relative about-us-images">

                    <h1 class="background-title dark-text">
                        About Us
                    </h1>

                    <div class="row">

                        <div class="col-6 pt-5">
                            <div class="about-img-wrapper">
                                <img src="assets/images/3.png"
                                     class="about-img"
                                     alt="">
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="about-img-wrapper">
                                <img src="assets/images/4.png"
                                     class="about-img"
                                     alt="">
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-lg-6 d-flex align-items-center">
                    <div class="ps-lg-5">

                        <p class="section-supertitle dark-theme">(02)</p>

                        <h2 class="section-title text-white mb-3">
                            Discover the Essence of Tunisian Olive Oil
                        </h2>

                        <p class="text-white-50 mb-4">
                            Experience the richness of nature through guided tastings,
                            olive oil education, and curated selections designed for
                            both everyday cooking and gourmet cuisine.
                            Join a journey that connects land, culture, and flavor.
                        </p>

                        <a href="#" class="btn btn-outline-light">
                            LEARN MORE
                        </a>

                    </div>
                </div>

            </div>
        </div>
    </section>

</div>

<!-- FEATURED SPEC-SHEET SECTION -->
<section class="products-section">
    <div class="container pb-5">

        <!-- Top Info Header -->
        <div class="row mb-5 align-items-center">

            <div class="col-md-2">
                <img src="assets/images/recycling_icon.png"
                     alt="100% Recyclé"
                     style="height: 50px;">

                <p class="small text-uppercase mb-0 mt-1"
                   style="font-size: 10px; font-weight: bold;">
                    Carton<br>100% Recyclé
                </p>
            </div>

            <div class="col-md-8 text-center">
                <h2 class="featured-main-title">
                    COFFRET CADEAU – HUILE D'OLIVE DE CARTHAGE
                </h2>

                <p class="text-uppercase featured-subtitle">
                    Éco-responsable — Authentique — Héritage
                </p>
            </div>

            <div class="col-md-2 text-end">
                <i class="fa-solid fa-leaf text-secondary opacity-25"
                   style="font-size: 2rem;"></i>
            </div>

        </div>

        <!-- ROW 1 -->
        <div class="row g-4 text-center mb-5">

            <div class="col-md-3">
                <p class="view-label">VUE DE FACE</p>

                <div class="spec-img-wrapper"
                     data-aos="fade-up"
                     data-aos-delay="0">

                    <img src="assets/images/plant.png"
                         class="img-fluid"
                         alt="Front View">
                </div>
            </div>

            <div class="col-md-3">
                <p class="view-label">VUE 3/4</p>

                <div class="spec-img-wrapper"
                     data-aos="fade-up"
                     data-aos-delay="100">

                    <img src="assets/images/plate.png"
                         class="img-fluid"
                         alt="3/4 View">
                </div>
            </div>

            <div class="col-md-3">
                <p class="view-label">VUE DE DOS</p>

                <div class="spec-img-wrapper"
                     data-aos="fade-up"
                     data-aos-delay="200">

                    <img src="assets/images/vase.png"
                         class="img-fluid"
                         alt="Back View">
                </div>
            </div>

            <div class="col-md-3">
                <p class="view-label">VUE DE CÔTÉ</p>

                <div class="spec-img-wrapper"
                     data-aos="fade-up"
                     data-aos-delay="300">

                    <img src="assets/images/vase2.png"
                         class="img-fluid"
                         alt="Side View">
                </div>
            </div>

        </div>

        <!-- ROW 2 -->
        <div class="row g-4 text-center align-items-end">

            <div class="col-md-3">
                <p class="view-label">VUE DE DESSUS</p>

                <img src="assets/images/pot.png"
                     class="img-fluid rounded-3"
                     alt="Top View">
            </div>

            <div class="col-md-3">
                <p class="view-label">VUE DU DESSUS (FERMÉ)</p>

                <img src="assets/images/holder.png"
                     class="img-fluid rounded-3"
                     alt="Top View Closed">
            </div>

            <div class="col-md-3">
                <p class="view-label">VUE 3/4 OUVERT</p>

                <img src="assets/images/flower.png"
                     class="img-fluid rounded-3"
                     alt="3/4 Open">
            </div>

            <div class="col-md-3 d-flex flex-column align-items-center justify-content-center">

                <div class="circle-detail">
                    <img src="assets/images/bowl.png"
                         class="img-fluid"
                         alt="Detail">
                </div>

                <p class="view-label mt-3">
                    DÉTAIL PORTE-CLÉS
                </p>

            </div>

        </div>

    </div>
</section>

<?php
// Include footer
require_once __DIR__ . '/../src/views/templates/footer.php';
?>