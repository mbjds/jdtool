function isValidNip(nip) {
    if(typeof nip !== 'string')
        return false;

    nip = nip.replace(/[\ \-]/gi, '');

    let weight = [6, 5, 7, 2, 3, 4, 5, 6, 7];
    let sum = 0;
    let controlNumber = parseInt(nip.substring(9, 10));
    let weightCount = weight.length;
    for (let i = 0; i < weightCount; i++) {
        sum += (parseInt(nip.substr(i, 1)) * weight[i]);
    }

    return sum % 11 === controlNumber;
}


document.addEventListener("DOMContentLoaded", (event) => {


	  var telInp = document.getElementById('billing_phone');  
	var phoneNumberPattern = /^(?:(?:\+|00)\d{2})?[ -]?(\d{2}[ -]?\d{3}[ -]?\d{2}[ -]?\d{2}|\d{3}[ -]?\d{3}[ -]?\d{3})$/;
    var varC = document.getElementById('vat_choose');
    var vatI = document.getElementById('vat_no');
    var vatIF = document.getElementById('vat_no_field');
    var vatBC = document.getElementById('billing_company');
    var vatBCF = document.getElementById('billing_company_field');

       var wrap = document.getElementById('billing_phone_field');
       if(telInp.value != ''){
           if(phoneNumberPattern.test(telInp.value) === true ){
               wrap.classList.remove('woocommerce-invalid', 'woocommerce-invalid-required-field');
           }else{
               wrap.classList.add('woocommerce-invalid', 'woocommerce-invalid-required-field');
           }
       }


            if(vatI.value != ''){
                if(!isValidNip(vatI.value)){
                    vatIF.classList.remove('woocommerce-invalid', 'woocommerce-invalid-required-field');
                }else{
                    vatIF.classList.add('woocommerce-invalid', 'woocommerce-invalid-required-field');
                };
            }

  
    telInp.addEventListener("focusout", (event) => {
        var phoneNumberPattern = /^(?:(?:\+|00)\d{2})?[ -]?(\d{2}[ -]?\d{3}[ -]?\d{2}[ -]?\d{2}|\d{3}[ -]?\d{3}[ -]?\d{3})$/;

       var wrap = document.getElementById('billing_phone_field');
        if(phoneNumberPattern.test(telInp.value) === true){
            wrap.classList.remove('woocommerce-invalid', 'woocommerce-invalid-required-field');
        }else{
            wrap.classList.add('woocommerce-invalid', 'woocommerce-invalid-required-field');
        }
    })


    document.addEventListener('DOMContentLoaded', (event)=>{
        if(varC.checked === false){
            vatI.required = false;

        }})

       varC.addEventListener('change', (event)=>{
           if(varC.checked === true){
                vatIF.classList.remove('vat-hidden');
                vatI.required = true;
                vatBCF.classList.remove('vat-hidden');
                vatBC.required = true;
           }else{
               vatIF.classList.add('vat-hidden');
               vatI.required = false;
               vatBCF.classList.add('vat-hidden');
               vatBC.required = false;

           }

       })

    vatI.addEventListener("focusout", (event)=>{
        if(isValidNip(vatI.value)){
            vatIF.classList.remove('woocommerce-invalid', 'woocommerce-invalid-required-field');
        }else{
            vatIF.classList.add('woocommerce-invalid', 'woocommerce-invalid-required-field');
        };
    })
});

