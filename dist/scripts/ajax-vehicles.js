


let agency = document.querySelector("#id_agency");
let div = document.querySelector("#table-container");
// let agencyVal = agency.value;
agency.addEventListener("change", function(e) {
  // insertPost({id_agency: agency.value}) ;
  // insertPost("id_agency=" + agencyVal)
  getVehicles();
} )

// JS Fetch method
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