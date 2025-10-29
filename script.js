$(document).ready(function () {
  // --- Global Functions ---

  // Function to update the header cart count
  function updateCartCount(count) {
    $(".cart-count").text(count);
  }

  // --- Mobile Menu ---
  $(".mobile-menu-btn").on("click", function () {
    $(".mobile-nav").addClass("active");
  });

  $(".mobile-nav-close").on("click", function () {
    $(".mobile-nav").removeClass("active");
  });

  // --- Auth Page (Login/Register) ---
  $(".auth-tab").on("click", function () {
    const formToShow = $(this).data("form"); // 'login' or 'register'

    $(".auth-tab").removeClass("active");
    $(this).addClass("active");

    $(".auth-form").removeClass("active");
    $("#" + formToShow + "-form").addClass("active");
    $("#auth-message").hide();
  });

  // AJAX Login
  $("#login-form").on("submit", function (e) {
    e.preventDefault();
    const formData = $(this).serialize() + "&redirect_url=" + $("#redirect-url").val();
    const $messageDiv = $("#auth-message");

    $.ajax({
      type: "POST",
      url: "php/auth_handler.php",
      data: formData,
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          $messageDiv.removeClass("error").addClass("success").text(response.message).show();
          // Redirect after a short delay
          window.setTimeout(function () {
            window.location.href = response.redirect;
          }, 1000);
        } else {
          $messageDiv.removeClass("success").addClass("error").text(response.message).show();
        }
      },
      error: function () {
        $messageDiv.removeClass("success").addClass("error").text("An error occurred. Please try again.").show();
      },
    });
  });

  // AJAX Register
  $("#register-form").on("submit", function (e) {
    e.preventDefault();
    const $messageDiv = $("#auth-message");

    $.ajax({
      type: "POST",
      url: "php/auth_handler.php",
      data: $(this).serialize(),
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          $messageDiv.removeClass("error").addClass("success").text(response.message).show();
          // Switch to login tab
          $('.auth-tab[data-form="login"]').click();
          $("#login-email").val($("#register-email").val()); // Pre-fill email
        } else {
          $messageDiv.removeClass("success").addClass("error").text(response.message).show();
        }
      },
      error: function () {
        $messageDiv.removeClass("success").addClass("error").text("An error occurred. Please try again.").show();
      },
    });
  });

  // --- Add to Cart / Buy Now ---

  // Add to Cart button (delegated for home page)
  $(".products-grid, .product-page-details").on("click", ".add-to-cart", function () {
    const productId = $(this).data("product-id");
    const $button = $(this);

    $.ajax({
      type: "POST",
      url: "php/cart_handler.php",
      data: {
        action: "add",
        product_id: productId,
      },
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          updateCartCount(response.cart_count);
          // Show feedback
          if ($("#add-to-cart-message").length) {
            $("#add-to-cart-message").fadeIn().delay(2000).fadeOut();
          } else {
            $button.text("Added!").prop("disabled", true);
            setTimeout(function () {
              $button.text("Add to Cart").prop("disabled", false);
            }, 1500);
          }
        }
      },
    });
  });

  // Buy Now button (Add to cart, then redirect)
  $(".products-grid, .product-page-details").on("click", ".buy-now", function () {
    const productId = $(this).data("product-id");

    $.ajax({
      type: "POST",
      url: "php/cart_handler.php",
      data: {
        action: "add",
        product_id: productId,
      },
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          // Redirect to checkout page
          window.location.href = "checkout.php";
        }
      },
    });
  });

  // --- Cart Page Logic ---

  // Update Quantity
  $(".cart-section").on("click", ".update-quantity", function () {
    const $button = $(this);
    const productId = $button.data("product-id");
    const change = parseInt($button.data("change"));
    const $quantitySpan = $button.siblings(".quantity");
    let newQuantity = parseInt($quantitySpan.text()) + change;

    if (newQuantity < 0) newQuantity = 0; // Don't allow negative

    $.ajax({
      type: "POST",
      url: "php/cart_handler.php",
      data: {
        action: "update",
        product_id: productId,
        quantity: newQuantity,
      },
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          updateCartCount(response.cart_count);
          $("#cart-total-price").text(response.total_price_formatted);

          if (newQuantity === 0) {
            // Remove the item row
            $button.closest(".cart-item").fadeOut(300, function () {
              $(this).remove();
              if ($(".cart-item").length === 0) {
                location.reload(); // Reload to show "empty cart" message
              }
            });
          } else {
            // Update quantity and subtotal
            $quantitySpan.text(newQuantity);
            // This is a simplification; full subtotal update would need price
            // A simple page reload is often easiest here
            location.reload();
          }
        }
      },
    });
  });

  // Remove Item
  $(".cart-section").on("click", ".cart-item-remove", function () {
    const productId = $(this).data("product-id");
    const $itemRow = $(this).closest(".cart-item");

    $.ajax({
      type: "POST",
      url: "php/cart_handler.php",
      data: {
        action: "remove",
        product_id: productId,
      },
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          updateCartCount(response.cart_count);
          $("#cart-total-price").text(response.total_price_formatted);
          $itemRow.fadeOut(300, function () {
            $(this).remove();
            if ($(".cart-item").length === 0) {
              location.reload(); // Reload to show "empty cart" message
            }
          });
        }
      },
    });
  });

  // --- Checkout Page ---
  $("#checkout-form").on("submit", function (e) {
    e.preventDefault();
    const $form = $(this);
    const $button = $form.find(".submit-order");
    const $errorDiv = $("#checkout-error");

    $button.prop("disabled", true).text("Placing Order...");
    $errorDiv.hide();

    $.ajax({
      type: "POST",
      url: "php/order_handler.php",
      data: $form.serialize(),
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          // Hide checkout, show confirmation
          $(".checkout-section").hide();
          updateCartCount(0); // Clear header count
          $("#order-confirmation-popup").css("display", "flex");
        } else {
          $errorDiv.text(response.message).show();
          $button.prop("disabled", false).text("Place Order");
        }
      },
      error: function () {
        $errorDiv.text("An unexpected error occurred. Please try again.").show();
        $button.prop("disabled", false).text("Place Order");
      },
    });
  });

  // Back to Shop button on confirmation popup
  $("#back-to-shop-btn").on("click", function () {
    window.location.href = "index.php";
  });
});