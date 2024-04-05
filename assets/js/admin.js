
var $ = jQuery.noConflict();
document.addEventListener("DOMContentLoaded", (event) => {

    var buttons = document.querySelectorAll('.renderPDF');
    buttons.forEach((button) => {
        button.addEventListener('click', (event) => {

//            alert(button.dataset.id);
            let requestData = {
                action: "generatePDF",
                id: button.dataset.id
            };
            $.ajax({
                url: ajaxurl,
                method: 'post',
                data: requestData,
                xhrFields: {
                    responseType: 'blob'
                },
                success: function (data) {
                    var blob = new Blob([data]);
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.target = '_blank';
                    link.download = requestData.id+'.pdf';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                }
            });
        });

    })
});