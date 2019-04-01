
// car brands from json file
let dropdown = $('#brand-names');

dropdown.empty();

dropdown.append('<option selected="true" disabled>marque du véhicule</option>');
dropdown.prop('selectedIndex', 0);

const url = '../../data/brands.json';

// Populate dropdown with list of car brands
$.getJSON(url, function (data) {
  $.each(data, function (key, entry) {
    dropdown.append($('<option></option>').attr('value', key).text(key));
  })
});

let agency = document.querySelector("#id_agency");
let div = document.querySelector("#table-container");

agency.addEventListener("change", function(e) {

  getVehicles();
} )

// JS Fetch method for getting vehicles table
function getVehicles() {
  fetch('toAjax-vehicles.php?id_agency=' + agency.value)
    .then(function (response) {
      return response.text()
    }).then(function (data) {
      // console.log(data) //
      div.innerHTML = data;
    })
}
  getVehicles();

// const insertPost = async function (data) {
//   let response = await fetch('toAjax-vehicles.php', {
//     method: 'POST',
//     headers: {
//       // 'Content-Type': 'application/json'
//       'Content-Type': 'application/x-www-form-urlencoded'
//     },
//     body: JSON.stringify(data)
//   })
//   let responseData = await response.text()
//   console.log(responseData)
// }