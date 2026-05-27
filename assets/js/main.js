document.addEventListener("DOMContentLoaded", function () {
  // ===== Navbar mobile =====
  const navbarToggle = document.getElementById("navbarToggle");
  const navbarMenu = document.getElementById("navbarMenu");

  if (navbarToggle && navbarMenu) {
    navbarToggle.addEventListener("click", function () {
      const isOpen = navbarMenu.classList.toggle("active");
      navbarToggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
    });

    document.addEventListener("click", function (e) {
      if (!navbarToggle.contains(e.target) && !navbarMenu.contains(e.target)) {
        navbarMenu.classList.remove("active");
        navbarToggle.setAttribute("aria-expanded", "false");
      }
    });
  }

  // ===== Dropdowns =====
  const dropdownToggles = document.querySelectorAll(".dropdown-toggle");
  dropdownToggles.forEach(function (toggle) {
    toggle.addEventListener("click", function (e) {
      e.stopPropagation();
      const dropdown = this.closest(".navbar-dropdown");
      const isOpen = dropdown.classList.toggle("open");
      this.setAttribute("aria-expanded", isOpen ? "true" : "false");
    });
  });

  document.addEventListener("click", function () {
    document.querySelectorAll(".navbar-dropdown.open").forEach(function (d) {
      d.classList.remove("open");
      const toggle = d.querySelector(".dropdown-toggle");
      if (toggle) toggle.setAttribute("aria-expanded", "false");
    });
  });

  // ===== Alerts auto dismiss =====
  document.querySelectorAll(".alert").forEach(function (alert) {
    if (alert.getAttribute("role") === "alert") return; // ne pas auto-fermer les erreurs
    setTimeout(function () {
      alert.style.opacity = "0";
      alert.style.transform = "translateY(-10px)";
      setTimeout(function () { alert.remove(); }, 300);
    }, 6000);
  });

  // ===== Smooth scroll =====
  document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
    anchor.addEventListener("click", function (e) {
      const href = this.getAttribute("href");
      if (href === "#" || href.length < 2) return;
      const target = document.querySelector(href);
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: "smooth", block: "start" });
        target.focus({ preventScroll: true });
      }
    });
  });

  // ===== Input visual feedback =====
  document.querySelectorAll(".form-control").forEach(function (input) {
    input.addEventListener("blur", function () {
      if (this.required && !this.value.trim()) {
        this.style.borderColor = "#E53935";
      } else {
        this.style.borderColor = "";
      }
    });
    input.addEventListener("focus", function () { this.style.borderColor = ""; });
  });

  // ===== File preview =====
  document.querySelectorAll('input[type="file"]').forEach(function (input) {
    input.addEventListener("change", function () {
      const file = this.files[0];
      if (file && file.type.startsWith("image/")) {
        const reader = new FileReader();
        const existingPreview = this.parentElement.querySelector(".file-preview");
        if (existingPreview) existingPreview.remove();
        reader.onload = function (e) {
          const preview = document.createElement("div");
          preview.className = "file-preview";
          preview.style.cssText = "margin-top:10px;";
          preview.innerHTML = '<img src="' + e.target.result + '" alt="Aperçu" style="max-width:200px;border-radius:8px;border:1px solid #E0E0E0;">';
          input.parentElement.appendChild(preview);
        };
        reader.readAsDataURL(file);
      }
    });
  });

  // ===== data-confirm =====
  document.querySelectorAll("[data-confirm]").forEach(function (el) {
    el.addEventListener("click", function (e) {
      if (!confirm(this.dataset.confirm)) e.preventDefault();
    });
  });

  // ===== Card number formatting =====
  document.querySelectorAll("[data-card-input]").forEach(function (input) {
    input.addEventListener("input", function () {
      let v = this.value.replace(/\D/g, "").slice(0, 16);
      this.value = v.replace(/(.{4})/g, "$1 ").trim();
    });
  });

  document.querySelectorAll("[data-exp-input]").forEach(function (input) {
    input.addEventListener("input", function () {
      let v = this.value.replace(/\D/g, "").slice(0, 4);
      if (v.length >= 3) v = v.slice(0, 2) + "/" + v.slice(2);
      this.value = v;
    });
  });

  // ===== Payment method radios =====
  document.querySelectorAll(".payment-method input[type=radio]").forEach(function (radio) {
    radio.addEventListener("change", function () {
      document.querySelectorAll(".payment-method").forEach(function (m) {
        m.classList.remove("active");
      });
      if (this.checked) this.closest(".payment-method").classList.add("active");
    });
  });

  // ===== Auto-scroll thread / chat =====
  ["thread-messages", "chat-messages"].forEach(function (id) {
    const el = document.getElementById(id);
    if (el) el.scrollTop = el.scrollHeight;
  });

  // ===== Accessibility panel =====
  const a11yToggle = document.getElementById("accessibility-toggle");
  const a11yPanel = document.getElementById("accessibility-panel");
  if (a11yToggle && a11yPanel) {
    a11yToggle.addEventListener("click", function () {
      const hidden = a11yPanel.hasAttribute("hidden");
      if (hidden) {
        a11yPanel.removeAttribute("hidden");
        a11yToggle.setAttribute("aria-expanded", "true");
      } else {
        a11yPanel.setAttribute("hidden", "");
        a11yToggle.setAttribute("aria-expanded", "false");
      }
    });
  }

  const a11yButtons = document.querySelectorAll("[data-a11y]");
  a11yButtons.forEach(function (btn) {
    btn.addEventListener("click", function () {
      const key = this.dataset.a11y;
      const value = this.dataset.value;
      applyA11y(key, value);
      localStorage.setItem("a11y-" + key, value);
      document.querySelectorAll('[data-a11y="' + key + '"]').forEach(function (b) {
        b.classList.toggle("active", b.dataset.value === value);
      });
    });
  });

  const a11yReset = document.getElementById("a11y-reset");
  if (a11yReset) {
    a11yReset.addEventListener("click", function () {
      ["font", "contrast", "dyslexia", "motion"].forEach(function (k) {
        localStorage.removeItem("a11y-" + k);
      });
      applyA11y("font", "normal");
      applyA11y("contrast", "normal");
      applyA11y("dyslexia", "off");
      applyA11y("motion", "on");
      document.querySelectorAll("[data-a11y]").forEach(function (b) {
        const isDefault = (b.dataset.a11y === "font" && b.dataset.value === "normal")
          || (b.dataset.a11y === "contrast" && b.dataset.value === "normal")
          || (b.dataset.a11y === "dyslexia" && b.dataset.value === "off")
          || (b.dataset.a11y === "motion" && b.dataset.value === "on");
        b.classList.toggle("active", isDefault);
      });
    });
  }

  function applyA11y(key, value) {
    const html = document.documentElement;
    if (key === "font") {
      html.classList.remove("a11y-font-small", "a11y-font-large", "a11y-font-xlarge");
      if (value === "small") html.classList.add("a11y-font-small");
      else if (value === "large") html.classList.add("a11y-font-large");
      else if (value === "xlarge") html.classList.add("a11y-font-xlarge");
    } else if (key === "contrast") {
      html.classList.remove("a11y-contrast-high", "a11y-contrast-dark");
      if (value === "high") html.classList.add("a11y-contrast-high");
      else if (value === "dark") html.classList.add("a11y-contrast-dark");
    } else if (key === "dyslexia") {
      html.classList.toggle("a11y-dyslexia", value === "on");
    } else if (key === "motion") {
      html.classList.toggle("a11y-motion-off", value === "off");
    }
  }

  ["font", "contrast", "dyslexia", "motion"].forEach(function (key) {
    const saved = localStorage.getItem("a11y-" + key);
    if (saved) {
      applyA11y(key, saved);
      document.querySelectorAll('[data-a11y="' + key + '"]').forEach(function (b) {
        b.classList.toggle("active", b.dataset.value === saved);
      });
    }
  });

  // ===== Partage : bouton "Copier le lien" =====
  const copyBtn = document.getElementById("copyShareBtn");
  if (copyBtn) {
    copyBtn.addEventListener("click", function () {
      const targetId = this.dataset.target;
      const input = document.getElementById(targetId);
      if (!input) return;
      const value = input.value;
      const finish = () => {
        const original = copyBtn.innerHTML;
        copyBtn.innerHTML = '<i class="fas fa-check"></i> Copié !';
        setTimeout(function () { copyBtn.innerHTML = original; }, 1500);
      };
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(value).then(finish, function () {
          input.select(); document.execCommand("copy"); finish();
        });
      } else {
        input.select(); document.execCommand("copy"); finish();
      }
    });
  }

  // ===== Partage : afficher/masquer le QR code =====
  const qrBtn = document.getElementById("toggleQrBtn");
  const qrPanel = document.getElementById("qrPanel");
  if (qrBtn && qrPanel) {
    qrBtn.addEventListener("click", function () {
      const isHidden = qrPanel.hasAttribute("hidden");
      if (isHidden) {
        qrPanel.removeAttribute("hidden");
        qrBtn.setAttribute("aria-expanded", "true");
      } else {
        qrPanel.setAttribute("hidden", "");
        qrBtn.setAttribute("aria-expanded", "false");
      }
    });
  }
});

function togglePassword(inputId) {
  const input = document.getElementById(inputId);
  if (!input) return;
  const icon = input.parentElement.querySelector(".password-toggle i");
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
  document.querySelectorAll(".faq-item").forEach(function (otherItem) {
    if (otherItem !== item) otherItem.classList.remove("active");
  });
  item.classList.toggle("active");
}
