"use strict";

// Event Listener für den Klick auf ein Pizzabild, um sie in den Warenkorb einzufügen
const pizzaImages = document.querySelectorAll('.pizza-image');
pizzaImages.forEach(pizzaImage => {
    pizzaImage.addEventListener('click', function(event) {
        const selectedPizza = event.target.title;
        const selectElement = document.getElementById('selectPizza');
        
        // Neue Option im Warenkorb hinzufügen
        const option = document.createElement('option');
        option.value = selectedPizza;
        option.text = selectedPizza;
        selectElement.appendChild(option);
        
        // Preis aktualisieren
        updateTotalPrice();
    });
});

// Function to update total price
function updateTotalPrice() {
    const selectElement = document.getElementById('selectPizza');
    const allOptions = selectElement.options;
    let totalPrice = 0;

    for (let i = 0; i < allOptions.length; i++) {
        const pizzaName = allOptions[i].text;
        const pizzaPrice = getPizzaPriceByName(pizzaName);
        totalPrice += pizzaPrice;
    }

    const totalPriceElement = document.getElementById('totalPrice');
    totalPriceElement.value = totalPrice.toFixed(2); // Update hidden input value
    document.querySelector('.priceTotal').textContent = `Total Price: €${totalPrice.toFixed(2)}`;

    // Calculate the sum of all total prices in the selectPizza
    const allPizzaPrices = Array.from(allOptions).map(option => {
        const pizzaName = option.text;
        const pizzaPrice = getPizzaPriceByName(pizzaName);
        return pizzaPrice;
    });

    const totalPriceOfAllPizzas = allPizzaPrices.reduce((sum, price) => sum + price, 0);
    console.log('Total price of all pizzas:', totalPriceOfAllPizzas.toFixed(2));
}



// Helper function to get pizza price by name
function getPizzaPriceByName(pizzaName) {
    const pizzaElements = document.querySelectorAll('.pizza-image');
    for (let i = 0; i < pizzaElements.length; i++) {
        if (pizzaElements[i].title === pizzaName) {
            const pizzaPrice = parseFloat(pizzaElements[i].getAttribute('data-price'));
            return pizzaPrice;
        }
    }
    return 0; // Return 0 if pizza price not found
}

// Event Listener für das Löschen von ausgewählten Pizzen im Warenkorb
function clearSelecOption() {
    const selectElement = document.getElementById('selectPizza');
    const selectedOptions = selectElement.selectedOptions;
    for (let i = selectedOptions.length - 1; i >= 0; i--) {
        selectElement.removeChild(selectedOptions[i]);
    }
    // Preis aktualisieren nach dem Löschen
    updateTotalPrice();
}

// Event Listener für die Änderung im Warenkorb, um den Gesamtpreis zu aktualisieren
document.getElementById('selectPizza').addEventListener('change', updateTotalPrice);

document.addEventListener('DOMContentLoaded', function () {
    const selectElement = document.getElementById('selectPizza');
    const addressInput = document.getElementById('inputAddress');
    const submitButton = document.querySelector('button[type="submit"]');

    // Function to check and enable/disable the submit button
    function checkSubmitButton() {
        const warenkorbOptions = Array.from(selectElement.options);
        const isWarenkorbNotEmpty = warenkorbOptions.length > 0;
        const isAddressNotEmpty = addressInput.value.trim() !== '';

        submitButton.disabled = !(isWarenkorbNotEmpty && isAddressNotEmpty);
    }

    // Initially disable the submit button
    checkSubmitButton();

    // Event listeners for changes in the address input or the selectPizza
    selectElement.addEventListener('change', checkSubmitButton);
    addressInput.addEventListener('input', checkSubmitButton);

    // Event Listener for the form submission
    document.querySelector('form').addEventListener('submit', function(event) {
        const warenkorbOptions = Array.from(selectElement.options);
        const isWarenkorbNotEmpty = warenkorbOptions.length > 0;
        const isAddressNotEmpty = addressInput.value.trim() !== '';

        if (!(isWarenkorbNotEmpty && isAddressNotEmpty)) {
            event.preventDefault();
            alert('Bitte geben Sie eine Lieferadresse an und fügen Sie mindestens eine Pizza hinzu.');
        } else {
            warenkorbOptions.forEach(option => {
                option.selected = true;
            });
        }
    });
});
