// Wait for DOM to be fully ready before initializing
document.addEventListener("DOMContentLoaded", function () {
  // ============ CAROUSEL DATA ============
  const slides = window.carouselSlides || [];
  let currentSlide = 0;

  // ============ DOM ELEMENTS ============
  const heroSection = document.querySelector(".hero");
  const heroContent = document.querySelector(".hero-content");
  const prevBtn = document.getElementById("prevBtn");
  const nextBtn = document.getElementById("nextBtn");

  // Safety check
  if (!heroSection || !heroContent || !prevBtn || !nextBtn) {
    console.error("Required carousel elements not found");
    return;
  }

  // ============ CAROUSEL FUNCTION ============
  function updateSlide(index) {
    if (slides.length === 0) return;

    currentSlide = (index + slides.length) % slides.length;
    const slide = slides[currentSlide];

    // Update content
    heroContent.querySelector("h1").textContent = slide.title;
    heroContent.querySelector("p").textContent = slide.subtitle;
    heroContent.querySelector("a").href = ROOT + getStoreCode() + "/products/" + slide.product_id;

    // Update active dot
    const dots = document.querySelectorAll(".dot");
    dots.forEach((dot) => dot.classList.remove("active"));
    if (dots[currentSlide]) {
      dots[currentSlide].classList.add("active");
    }

    // Set background image with overlay
    heroSection.style.backgroundImage = `linear-gradient(135deg, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('${slide.background}')`;
    heroSection.style.backgroundSize = "cover";
    heroSection.style.backgroundPosition = "center";
  }

  // ============ CAROUSEL EVENT LISTENERS ============
  prevBtn.addEventListener("click", () => updateSlide(currentSlide - 1));
  nextBtn.addEventListener("click", () => updateSlide(currentSlide + 1));

  // Dot click listeners
  const dots = document.querySelectorAll(".dot");
  dots.forEach((dot) => {
    dot.addEventListener("click", () =>
      updateSlide(parseInt(dot.getAttribute("data-slide"))),
    );
  });

  // Auto-rotate slides every 5 seconds
  setInterval(() => updateSlide(currentSlide + 1), 5000);

  // Initialize first slide right away
  updateSlide(0);

  // // ============ WISHLIST FUNCTIONALITY ============
  // document.querySelectorAll(".wishlist-btn").forEach((btn) => {
  //   btn.addEventListener("click", function (e) {
  //     e.stopPropagation();
  //     this.classList.toggle("active");
  //     this.textContent = this.classList.contains("active") ? "♥" : "♡";
  //   });
  // });

  // // ============ NAVIGATION SMOOTH SCROLL ============
  // document.querySelectorAll("nav a").forEach((link) => {
  //   link.addEventListener("click", function (e) {
  //     e.preventDefault();
  //     const targetId = this.getAttribute("href").substring(1);
  //     const targetElement = document.getElementById(targetId);

  //     if (targetElement) {
  //       targetElement.scrollIntoView({ behavior: "smooth" });
  //       document
  //         .querySelectorAll("nav a")
  //         .forEach((a) => a.classList.remove("active"));
  //       this.classList.add("active");
  //     }
  //   });
  // });

  // // Update nav active link on scroll
  // window.addEventListener("scroll", () => {
  //   const sections = document.querySelectorAll("section[id]");
  //   let currentSection = "";

  //   sections.forEach((section) => {
  //     const sectionTop = section.offsetTop;
  //     if (scrollY >= sectionTop - 100) {
  //       currentSection = section.getAttribute("id");
  //     }
  //   });

  //   document.querySelectorAll("nav a").forEach((link) => {
  //     link.classList.remove("active");
  //     if (link.getAttribute("href").substring(1) === currentSection) {
  //       link.classList.add("active");
  //     }
  //   });
  // });

  // // ============ HEADER SCROLL EFFECT ============
  // const header = document.querySelector("header");
  // if (header) {
  //   let lastScrollTop = 0;

  //   window.addEventListener("scroll", () => {
  //     let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

  //     if (scrollTop > 100) {
  //       header.style.boxShadow = "0 4px 12px rgba(0, 0, 0, 0.15)";
  //     } else {
  //       header.style.boxShadow = "var(--box-shadow)";
  //     }

  //     lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
  //   });
  // }

  console.log("Prestige Store ready!");
});

function getStoreCode() {
  return document.body.dataset.storecode || "default";
}
