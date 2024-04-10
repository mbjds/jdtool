var $ = jQuery;
document.addEventListener("DOMContentLoaded", function () {
  var loadingDiv = document.createElement("div");
  loadingDiv.className = "loading-state";
  loadingDiv.id = "loading-state";
  loadingDiv.innerHTML = '<div class="loading"></div>';
  var body = document.querySelector("body");
  body.insertAdjacentElement("afterbegin", loadingDiv);
});
document.addEventListener("DOMContentLoaded", (event) => {
  var toastMixin = Swal.mixin({
    toast: true,
    icon: "success",
    title: "General Title",
    animation: true,
    position: "top-right",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.addEventListener("mouseenter", Swal.stopTimer);
      toast.addEventListener("mouseleave", Swal.resumeTimer);
    },
    customClass: {
      container: "container-jd",
      popup: "popup-jd",
    },
  });

  var buttons = document.querySelectorAll(".renderPDF");
  buttons.forEach((button) => {
    button.addEventListener("click", (event) => {
      if (button.classList.contains("rFalse")) {
        toastMixin.fire({
          animation: true,
          icon: "error",
          title: "Token nieaktywny",
        });
      } else if (button.classList.contains("rTrue")) {
        var load = document.getElementById("loading-state");
        load.style.display = "flex";
        let requestData = {
          action: "generatePDF",
          id: button.dataset.id,
          code: button.dataset.code,
        };
        $.ajax({
          url: ajaxurl,
          method: "post",
          data: requestData,
          xhrFields: {
            responseType: "blob",
          },

          success: function (data) {
            var blob = new Blob([data]);
            var link = document.createElement("a");
            link.href = window.URL.createObjectURL(blob);
            link.download = requestData.code + ".pdf";

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            load.style.display = "none";
          },
        }).done(function () {
          toastMixin.fire({
            animation: true,

            icon: "success",
            title: "PDF wygenerowany",
            timer: 3000,
          });
        });
      }
    });
  });

  var activateButtons = document.querySelectorAll(".activateF");
  activateButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
      if (button.classList.contains("rFalse")) {
        toastMixin.fire({
          animation: true,
          icon: "error",
          title: "Token juz aktywny",
        });
      } else if (button.classList.contains("rTrue")) {
        var load = document.getElementById("loading-state");
        load.style.display = "flex";
        let requestData = {
          action: "activateV",
          id: button.dataset.id,
        };
        $.ajax({
          url: ajaxurl,
          method: "post",
          data: requestData,
          success: function (data) {
            load.style.display = "none";
            toastMixin.fire({
              animation: true,
              position: "bottom-end",
              icon: "success",
              title: "Token aktywowany",
              timer: 3000,
            });
            setTimeout(location.reload.bind(location), 2000);
          },
        });
      }
    });
  });
});