let agency = document.querySelector("#id_agency");
let div = document.querySelector("#table-container");

agency.addEventListener("change", function(e) {

  getOrders();
} )

// JS Fetch method
function getOrders() {
  fetch('toAjax-orders.php?id_agency=' + agency.value)
    .then(function (response) {
      return response.text()
    }).then(function (data) {
      // console.log(data) //
      div.innerHTML = data;
    })
}

  getOrders();