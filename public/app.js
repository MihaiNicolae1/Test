function deleteProduct($productId) {

    var result = confirm("Are you sure you want to delete this product?");
    if (result) {
        const Http = new XMLHttpRequest();
        Http.onreadystatechange = function () {
            if (Http.readyState === XMLHttpRequest.DONE) {
                location.reload();
            }
        }
        const url = '/product/' + $productId;
        Http.open("DELETE", url);
        Http.send();
    }
}

