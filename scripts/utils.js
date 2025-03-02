function openSidebar() {
  document.getElementById("sidebar").classList.remove("translate-x-full");
}

function closeSidebar() {
  document.getElementById("sidebar").classList.add("translate-x-full");
}

// Helper function to escape HTML special characters
function escapeHtml(unsafe) {
  return unsafe
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

function showToast(message, type = "success") {
  if (message == null) return;
  var x = $("#snackbar");
  x.html(message);
  // x.innerHTML = message;
  // x.backgroundColor =
  //   type == "success" ? "rgb(12, 165, 81)" : "rgb(204, 67, 25)";
  if (type == "success") {
    x.addClass("bg-green-400");
    x.removeClass("bg-red-400");
  } else {
    x.addClass("bg-red-400");
    x.removeClass("bg-green-400");
  }
  x.removeClass("hidden");
  x.addClass("show");
  // x.className = "show";
  setTimeout(function () {
    x.removeClass("show");
    x.addClass("hidden");
  }, 3000);
}
