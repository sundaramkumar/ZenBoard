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

function showToast(message) {
  if (message == null) return;
  var x = document.getElementById("snackbar");
  x.innerHTML = message;
  x.className = "show";
  setTimeout(function () {
    x.className = x.className.replace("show", "");
  }, 3000);
}
