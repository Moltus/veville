// $(document).ready(function() {
  
//   // get table entries from 
//   $("#id_agency").on('change', function(e) {
//     // console.log(this.value);
//     ajaxQueryVehicles(2);
//   });
  
//   function ajaxQueryVehicles(id_agency) {
//     console.log(id_agency);
//     let param = "id_agency=" + id_agency;

//     $.post("toAjax-vehicles.php", param, function(data) {
//       console.log("ajax query");
//       $("#table-container").html(result);
//     }, 'json')
//   }
// })


// $.ajax method
// $(document).ready(function() {
//   $(document).on("change", '#id_agency', function (e) {
//     var param = $(this).val();


//     $.ajax({
//       type: "POST",
//       data: param,
//       url: 'toAjax-vehicles.php',
//       dataType: 'json',
//       success: function (result) {
//         $("#table-container").html(result);
//       }
//     });

//   });
// })

function getVehicles() {
  fetch('toAjax-vehicles.php?id_agency=' + agency.value)
    .then(function (response) {
      return response.text()
    }).then(function (data) {
      // console.log(data) //
      div.innerHTML = data;
    })
}


// JS Fetch method
let agency = document.querySelector("#id_agency");
let div = document.querySelector("#table-container");
// let agencyVal = agency.value;
agency.addEventListener("change", function(e) {
  // insertPost({id_agency: agency.value}) ;
  // insertPost("id_agency=" + agencyVal)
  getVehicles();
} )

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