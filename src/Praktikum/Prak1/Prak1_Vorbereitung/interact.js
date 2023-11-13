function seePrice() {
    const selectPizza = document.getElementById("selectPizza");
    const priceTag = document.querySelector(".priceTotal");
    const totalPriceInput = document.getElementById("totalPrice");


    selectPizza.addEventListener("change", seePrice);
    var totalPrice = 0.0;

    for (var i = 0; i < selectPizza.options.length; i++) {
        if (selectPizza.options[i].selected) {
            if (selectPizza.options[i].value == "margherita") {
                totalPrice += 4.0;
            } else if (selectPizza.options[i].value == "salami") {
                totalPrice += 4.5;
            } else if (selectPizza.options[i].value == "hawaii") {
                totalPrice += 5.5;
            }
        }
    }

    priceTag.textContent = "Total Price: " + totalPrice + " €";
    totalPriceInput.value = totalPrice + " €";

}

function clearSelecOption() {

    onclick = document.getElementById('selectPizza').value = '';
}