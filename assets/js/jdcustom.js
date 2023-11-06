function isValidNip(nip) {
    if (typeof nip !== 'string') return false;

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
    var vatIA = document.getElementById('invoice_choose_field');
    var vatIAA = document.getElementById('invoice_addr_field');
    var vatIZ = document.getElementById('invoice_zipcode_field');
    var vatIC = document.getElementById('invoice_city_field');

    var wrap = document.getElementById('billing_phone_field');
    if (telInp.value != '') {
        if (phoneNumberPattern.test(telInp.value) === true) {
            wrap.classList.remove('woocommerce-invalid', 'woocommerce-invalid-required-field');
        } else {
            wrap.classList.add('woocommerce-invalid', 'woocommerce-invalid-required-field');
        }
    }


    if (vatI.value != '') {
        if (!isValidNip(vatI.value)) {
            vatIF.classList.remove('woocommerce-invalid', 'woocommerce-invalid-required-field');
        } else {
            vatIF.classList.add('woocommerce-invalid', 'woocommerce-invalid-required-field');
        }

    }


    telInp.addEventListener("focusout", (event) => {
        var phoneNumberPattern = /^(?:(?:\+|00)\d{2})?[ -]?(\d{2}[ -]?\d{3}[ -]?\d{2}[ -]?\d{2}|\d{3}[ -]?\d{3}[ -]?\d{3})$/;

        var wrap = document.getElementById('billing_phone_field');
        if (phoneNumberPattern.test(telInp.value) === true) {
            wrap.classList.remove('woocommerce-invalid', 'woocommerce-invalid-required-field');
        } else {
            wrap.classList.add('woocommerce-invalid', 'woocommerce-invalid-required-field');
        }
    })



        if (varC.checked === false) {
            vatI.required = false;
            varC.value = false;

        }

    varC.addEventListener('change', (event) => {
        if (varC.checked === true) {
            vatIF.classList.remove('vat-hidden');
            vatI.required = true;
            vatBCF.classList.remove('vat-hidden');
            vatBC.required = true;
            vatIA.classList.remove('vat-hidden');
            vatIAA.classList.remove('vat-hidden');
            vatIZ.classList.remove('vat-hidden');
            vatIC.classList.remove('vat-hidden');
            varC.value = true;

        } else {
            vatIF.classList.add('vat-hidden');
            vatI.required = false;
            vatBCF.classList.add('vat-hidden');
            vatBC.required = false;
            vatIA.classList.add('vat-hidden');
            vatIAA.classList.add('vat-hidden');
            vatIZ.classList.add('vat-hidden');
            vatIC.classList.add('vat-hidden');
            varC.value = false;


        }

    })

    vatI.addEventListener("focusout", (event) => {
        if (isValidNip(vatI.value)) {
            vatIF.classList.remove('woocommerce-invalid', 'woocommerce-invalid-required-field');
        } else {
            vatIF.classList.add('woocommerce-invalid', 'woocommerce-invalid-required-field');
        }

    })
    var iswitch = document.getElementById('invoice_choose');
    var addr1 = document.getElementById('invoice_addr_field');
    var addr2 = document.getElementById('invoice_zipcode_field');
    var addr3 = document.getElementById('invoice_city_field');

    var addr1f = document.getElementById('invoice_addr');
    var addr2f = document.getElementById('invoice_zipcode');
    var addr3f = document.getElementById('invoice_city');
    if(iswitch.checked){
        addr1f.required = false;
        addr2f.required = false;
        addr3f.required = false;
    }
    iswitch.addEventListener("click", (event) => {
        if (iswitch.checked) {
            addr1.classList.add('addr-hidden');
            addr2.classList.add('addr-hidden');
            addr3.classList.add('addr-hidden');
            iswitch.value = true;
            addr1f.required = false;
            addr2f.required = false;
            addr3f.required = false;

        } else {
            addr1.classList.remove('addr-hidden');
            addr2.classList.remove('addr-hidden');
            addr3.classList.remove('addr-hidden');
            iswitch.value = false;
            addr1f.required = true;
            addr2f.required = true;
            addr3f.required = true;
        }
    })

});

