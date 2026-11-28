
// ============================================================================
// Shopping Cart Function
// ============================================================================
const $modal = $("#simpleModal");

$("#modalBtn").on("click", () => {
  $modal.css("display", "block");
});

$(".closeBtn").on("click", () => {
  $modal.css("display", "none");
});

$(window).on("click", (e) => {
  if (e.target === $modal[0]) {
    $modal.css("display", "none");
  }
});
