$(document).ready(function() {
  
  // get table entries from 
  $("#id_agency").on('change', function(e) {
    // console.log(this.value);
    ajaxQueryVehicles(this.value);
  });
  
  function ajaxQueryVehicles(id_agency) {
    console.log(id_agency);
    let param = "id_agency=" + id_agency;

    $.post("toAjax-vehicles.php", param, function(data) {
      $("#table-container").html(result);
    }, 'json')
  }
})

// $(document).on("change", '#id_agency', function (e) {
//   var param = $(this).val();


//   $.ajax({
//     type: "POST",
//     data: param,
//     url: 'toAjax-vehicles.php',
//     dataType: 'json',
//     success: function (result) {
//       $("#table-container").html(result);
//     }
//   });

// });