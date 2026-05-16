<footer>
    <div class="footer-container">
        <div class="footer-content">
            <div class="footer-section">
                <h4><?= $content['footer_title'] ?></h4>
                <p><?= $content['footer_text'] ?></p>
            </div>

            <div class="footer-section" style="visibility: hidden;">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="#home">Shop</a></li>
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><a href="#">FAQs</a></li>
                </ul>
            </div>

            <div class="footer-section" style="visibility: hidden;">
                <h4>Policies</h4>
                <ul>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms & Conditions</a></li>
                    <li><a href="#">Shipping Policy</a></li>
                    <li><a href="#">Return Policy</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Follow Us</h4>
                <div class="social-links">
                    <a href="<?= $content['fb_url'] ?>"><i class="fab fa-facebook"></i></a>
                    <a href="<?= $content['insta_url'] ?>"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p><?= $content['copyright_text'] ?></p>
        </div>
    </div>
</footer>
<script>
    const stylesFromDB = {
        primary: "<?= $content['primary_color'] ?>",
    };

    const root = document.documentElement; // <html> element
    root.style.setProperty('--primary', stylesFromDB.primary);
</script>