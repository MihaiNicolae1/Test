let productData = [];
let productSkus = [];

function newInvoiceRequest() {

    const Http = new XMLHttpRequest();
    Http.onreadystatechange = function () {
        if (Http.readyState === XMLHttpRequest.DONE) {
            productData = JSON.parse(Http.response);
            productSkus = Object.keys(productData);
            populateAutocomplete();
        }
    }
    let company_cui = $('input#issuer_cui').val();
    const url = '/product/list/' + company_cui;
    Http.open("GET", url);
    Http.send();
}

function addItem($companyId) {

    const div = document.getElementById('invoice-form');
    let productForms = div.getElementsByClassName('invoiceProductForm');
    let firstForm = productForms[0];
    let formClone = firstForm.cloneNode(true);
    div.appendChild(formClone);
    for (let i = 0; i < productForms.length; i++) {
        let counter = i + 1;
        let inputs = productForms[i].getElementsByClassName('product-input');
        for (let j = 0; j < inputs.length; j++) {
            let name = inputs[j].id + '_' + counter;
            inputs[j].name = name;
            inputs[j].value = '';
        }
    }
    populateAutocomplete();
}

function deleteItem(elem) {
    const div = document.getElementById('invoice-form');
    let forms = div.getElementsByClassName('invoiceProductForm');

    if (forms.length > 1) {
        elem.parentNode.parentNode.remove();
    } else {
        alert('You must have at least one item');
    }
    for (let i = 0; i < forms.length; i++) {
        let counter = i + 1;
        let inputs = forms[i].getElementsByClassName('product-input');
        for (let j = 0; j < inputs.length; j++) {
            let name = inputs[j].id + '_' + counter;
            inputs[j].name = name;
            inputs[j].value = '';
        }
    }
}

function deleteInvoice(invoiceId) {

    var result = confirm("Are you sure you want to delete this invoice?");
    if (result) {
        const Http = new XMLHttpRequest();
        Http.onreadystatechange = function () {
            if (Http.readyState === XMLHttpRequest.DONE) {
                location.reload();
            }
        }
        const url = '/invoice/' + invoiceId;
        Http.open("DELETE", url);
        Http.send();
    }
}

function openPrintInvoice(invoiceNumber) {
    // file is a File object, this will also take a blob
    const pdf = '/public/invoices/' + invoiceNumber + '.pdf';
    var iframe = document.createElement('iframe');
    iframe.style.display = "none";
    iframe.src = pdf;

    document.body.appendChild(iframe);
    iframe.contentWindow.focus();
    iframe.contentWindow.print();
}

function printInvoice(invoiceId) {
    const Http = new XMLHttpRequest();
    Http.onreadystatechange = function () {
        if (Http.readyState === XMLHttpRequest.DONE) {
            openPrintInvoice(Http.response);
        }
    }
    const url = '/invoice/print/' + invoiceId;
    Http.open("PUT", url);
    Http.send();
}

function payInvoice(invoiceId) {

    const Http = new XMLHttpRequest();
    Http.onreadystatechange = function () {
        if (Http.readyState === XMLHttpRequest.DONE) {
            let message = Http.response;
            if (Http.response == 0) {
                message = 'Invoice is already marked as paid!'
            } else if (Http.response == 1) {
                message = 'Invoice successfully paid!';
            }
            alert(message);
        }
    }
    const url = '/invoice/pay/' + invoiceId;
    Http.open("PUT", url);
    Http.send();
}

function cancelInvoice(invoiceId) {
    var result = confirm("Are you sure you want to cancel this invoice?");
    if (result) {
        const Http = new XMLHttpRequest();
        Http.onreadystatechange = function () {
            if (Http.readyState === XMLHttpRequest.DONE) {
                location.reload();
            }
        }
        const url = '/invoice/cancel/' + invoiceId;
        Http.open("POST", url);
        Http.send();
    }
}

function getInvoiceInfo(invoiceId) {
    const Http = new XMLHttpRequest();
    Http.onreadystatechange = function () {
        if (Http.readyState === XMLHttpRequest.DONE) {
            let info = JSON.parse(Http.response);
            populateInvoice(info);
            newInvoiceRequest();
            populateAutocomplete();
        }
    }
    const url = '/invoice/info/' + invoiceId;
    Http.open("GET", url);
    Http.send();
}

function populateInvoice(invoiceInfo) {

    let status;
    populateProducts(invoiceInfo['products']);
    const invoiceInputs = document.getElementsByClassName('invoice-input');

    if (invoiceInfo['is_paid'] === 1 || invoiceInfo['is_printed'] === 1) {
        let allInputs = document.getElementById('invoice-form').elements;
        for (let i = 0; i < allInputs.length; i++) {
            allInputs[i].disabled = true;
        }
    }
    for (let i = 0; i < invoiceInputs.length; i++) {

        if (invoiceInputs[i].id === 'is_paid') {
            if (invoiceInfo['is_paid'] === 1) {
                status = true;
            } else {
                status = false;
            }
            invoiceInputs[i].checked = status;
            continue;
        }
        let id = invoiceInputs[i].id;
        if (id === 'emmited_date' || id === 'due_date_of_payment') {
            let date = new Date(invoiceInfo[id]).toISOString().split('T')[0];
            invoiceInputs[i].value = date;
            continue;
        }
        invoiceInputs[i].value = invoiceInfo[id];
    }

}

function populateProducts(products) {

    let div = document.getElementById('invoice-form');
    let productForms = div.getElementsByClassName('invoiceProductForm');
    let firstForm = productForms[0];
    let formClone = firstForm.cloneNode(true);
    for (let i = 0; i < products.length - 1; i++) {
        div.appendChild(formClone);
    }
    productForms = div.getElementsByClassName('invoiceProductForm');
    for( let i = 0; i < productForms.length; i++){
        let inputs = productForms[i].getElementsByClassName('product-input');
        for (let j = 0; j < inputs.length; j++) {
            let name = inputs[j].id + '_' + (i + 1);
            inputs[j].name = name;
            inputs[j].value = products[i][inputs[j].id];
        }
    }
}

function populateAutocomplete(){

    const forms = $(".invoiceProductForm");
    for(let i = 0; i < forms.length; i++){
        $(forms[i]).find('#sku').autocomplete({
            minLength: 3,
            source: productSkus,
            select: function (event, ui) {
                let sku = ui.item.label;
                $(forms[i]).find('#name').val(productData[sku]['name']);
                $(forms[i]).find('#description').val(productData[sku]['description']);
                $(forms[i]).find('#product_value_no_vat').val(productData[sku]['value_without_vat']);
                $(forms[i]).find('#product_value_vat').val(productData[sku]['vat_percent']);
            }
        });
    }
}
