document.addEventListener("DOMContentLoaded", function () {
  var loadingDiv = document.createElement("div");
  loadingDiv.className = "loading-state";
  loadingDiv.id = "loading-state";
  loadingDiv.innerHTML = '<div class="loading"></div>';
  var body = document.querySelector("body");
  body.insertAdjacentElement("afterbegin", loadingDiv);
});