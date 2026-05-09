    <!-- Footer -->
    <footer class="footer-section" id="contact">
      <div class="footer-container">
        <div class="footer-brand">
          <h3 class="footer-logo">Dainik Nirman </h3>
          <h5>(A Unit of NIRMA PRAKASHAN)</h5>
          <p class="footer-tagline">Your trusted source for books and writing contests</p>
        </div>
        
        <div class="footer-contact">
          <h4>Contact Us</h4>
          <p><strong>Email:</strong> dainiknirman@gmail.com</p>
          <p>
            <strong>Address:</strong><br>
            House No. 167, Laxman Nagar, Krishan Nagar,<br>
            Lohawat Bisnawas, Phalodi, Rajasthan - 342302
          </p>
        </div>
        
        <div class="footer-links">
          <h4>Quick Links</h4>
          <a href="privacy.html">Privacy Policy</a>
          <a href="terms.html">Terms of Service</a>
          <a href="return.html">Return & Refund Policy</a>
        </div>
      </div>
      
      <div class="footer-bottom">
        <p>© 2026 NIRMA PRAKASHAN. All rights reserved.</p>
      </div>
    </footer>

    <!-- JavaScript for Mobile Menu -->
    <script>
      const mobileMenuToggle = document.getElementById('mobileMenuToggle');
      const mobileMenu = document.getElementById('mobileMenu');

      mobileMenuToggle.addEventListener('click', () => {
        mobileMenu.classList.toggle('active');
      });

  
document.querySelectorAll('.nav-link').forEach(link => {
  link.addEventListener('click', function(e) {
    if (this.getAttribute('href').startsWith('#')) {
      e.preventDefault();

      const target = document.querySelector(this.getAttribute('href'));

      if (target) {
        window.scrollTo({
          top: target.offsetTop - 70,
          behavior: 'smooth'
        });
      }
    }
  });
});

    </script>

  </div>
</body>
</html>