// UpdateStatus.js

var request = new XMLHttpRequest();

function requestData() {
  request.open("GET", "KundenStatus.php");
  request.onreadystatechange = processData;
  request.send(null);
}

function processData() {
  if (request.readyState === XMLHttpRequest.DONE) {
    if (request.status === 200) {
      if (request.responseText !== null) {
        const data = JSON.parse(request.responseText);
        process(data);
      } else {
        console.error('Dokument ist leer');
      }
    } else {
      console.error('Uebertragung fehlgeschlagen');
    }
  }
}

// Function to process the JSON data and insert it into the customer page
function process(jsonData) {
  const statusContainer = document.getElementById("status-container");

  // Clear any existing content in the status container
  while (statusContainer.firstChild) {
    statusContainer.removeChild(statusContainer.firstChild);
  }

  if (jsonData.length === 0) {
    const noPizzaMessage = document.createElement("p");
    noPizzaMessage.textContent = "No pizzas available.";
    statusContainer.appendChild(noPizzaMessage);
    return;
  }

  //Make paragraph for order id
  const orderId = document.createElement("p");
  orderId.textContent = "Order ID: "+jsonData[0].ordering_id;
  statusContainer.appendChild(orderId);


  jsonData.forEach(statusObj => {
    const statusElement = document.createElement("div");
    statusElement.classList.add("status");



    const articleName = document.createElement("p");
    articleName.textContent = "Pizza: " + statusObj.name;
    statusElement.appendChild(articleName);

    for (let i = 0; i < 5; i++) {
      const radio = document.createElement("input");
      radio.type = "radio";
      radio.name = "status_"+statusObj.ordered_article_id;
      radio.value = i;
      statusElement.appendChild(radio);
  
      const label = document.createElement("label");
      if(i== 0) {
      label.textContent = "Bestellt";
      } else if(i == 1) {
        label.textContent = "Im Offen";
      } else if(i == 2) {
        label.textContent = "Fertig";
      } else if(i == 3) {
        label.textContent = "unterwegs";
      } else if(i == 4) {
        label.textContent = "Geliefert";
      }
      statusElement.appendChild(label);
      // Add spacing between radio buttons
      statusElement.appendChild(document.createTextNode(" "));
  
      // Check the radio button that matches the current status
      if (statusObj.status == i) {
        radio.checked = true;
      }
      radio.disabled = true;
    }
    statusContainer.appendChild(statusElement);
  });
}

// Start the polling after the page finishes loading
window.onload = function() {
  requestData();
  setInterval(requestData, 2000);
};