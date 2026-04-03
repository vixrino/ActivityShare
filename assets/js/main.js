document.addEventListener("DOMContentLoaded", function () {
  const navbarToggle = document.getElementById("navbarToggle");
  const navbarMenu = document.getElementById("navbarMenu");

  if (navbarToggle && navbarMenu) {
    navbarToggle.addEventListener("click", function () {
      navbarMenu.classList.toggle("active");
    });

    document.addEventListener("click", function (e) {
      if (!navbarToggle.contains(e.target) && !navbarMenu.contains(e.target)) {
        navbarMenu.classList.remove("active");
      }
    });
  }

  const dropdownToggles = document.querySelectorAll(".dropdown-toggle");
  dropdownToggles.forEach(function (toggle) {
    toggle.addEventListener("click", function (e) {
      e.stopPropagation();
      const dropdown = this.closest(".navbar-dropdown");
      dropdown.classList.toggle("open");
    });
  });

  document.addEventListener("click", function () {
    document.querySelectorAll(".navbar-dropdown.open").forEach(function (d) {
      d.classList.remove("open");
    });
  });

  document.querySelectorAll(".alert").forEach(function (alert) {
    setTimeout(function () {
      alert.style.opacity = "0";
      alert.style.transform = "translateY(-10px)";
      setTimeout(function () {
        alert.remove();
      }, 300);
    }, 5000);
  });

  document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
    anchor.addEventListener("click", function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute("href"));
      if (target) {
        target.scrollIntoView({ behavior: "smooth", block: "start" });
      }
    });
  });

  document.querySelectorAll(".form-control").forEach(function (input) {
    input.addEventListener("blur", function () {
      if (this.required && !this.value.trim()) {
        this.style.borderColor = "#E53935";
      } else {
        this.style.borderColor = "";
      }
    });

    input.addEventListener("focus", function () {
      this.style.borderColor = "";
    });
  });

  document.querySelectorAll('input[type="file"]').forEach(function (input) {
    input.addEventListener("change", function () {
      const file = this.files[0];
      if (file && file.type.startsWith("image/")) {
        const reader = new FileReader();
        const existingPreview =
          this.parentElement.querySelector(".file-preview");
        if (existingPreview) existingPreview.remove();

        reader.onload = function (e) {
          const preview = document.createElement("div");
          preview.className = "file-preview";
          preview.style.cssText = "margin-top:10px;";
          preview.innerHTML =
            '<img src="' +
            e.target.result +
            '" style="max-width:200px;border-radius:8px;border:1px solid #E0E0E0;">';
          input.parentElement.appendChild(preview);
        };
        reader.readAsDataURL(file);
      }
    });
  });

  document.querySelectorAll("[data-confirm]").forEach(function (el) {
    el.addEventListener("click", function (e) {
      if (!confirm(this.dataset.confirm)) {
        e.preventDefault();
      }
    });
  });
});

function togglePassword(inputId) {
  const input = document.getElementById(inputId);
  const icon = input.nextElementSibling
    ? input.parentElement.querySelector(".password-toggle i")
    : null;

  if (input.type === "password") {
    input.type = "text";
    if (icon) icon.className = "fas fa-eye-slash";
  } else {
    input.type = "password";
    if (icon) icon.className = "fas fa-eye";
  }
}

function toggleFaq(button) {
  const item = button.closest(".faq-item");
  const allItems = document.querySelectorAll(".faq-item");

  allItems.forEach(function (otherItem) {
    if (otherItem !== item) {
      otherItem.classList.remove("active");
    }
  });

  item.classList.toggle("active");
}
