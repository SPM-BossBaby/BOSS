

$(function(){ // this will run when the document.ready event fires
    biddingcart = Array(); 

    $('.table.p-0.m-0 tbody tr').click(function(event) { // registering a click on a checkbox
        if (event.target.type !== 'checkbox') {
            $(':checkbox', this).trigger('click');
        }
    });

    $("input[type='checkbox']").change(function (e) { // for the checkbox, if it's checked, highlight that particular row
        if ($(this).is(":checked")) {
            $(this).closest('tr').addClass("highlight_row");
        } else {
            $(this).closest('tr').removeClass("highlight_row");
        }
    });

    $('.form-check-input').change( function() { // keep track of a chceckbox whether it is being checked or unchecked
        phpbidvalue = '<table class="table table-striped p-0 m-0">\
        <thead style="background-color: #ff8a8a">\
            <tr>\
                <th scope="col" style="text-align:center;">Code</th>\
                <th scope="col" style="text-align:center;">Title</th>\
                <th scope="col" style="text-align:center;">Section</th>\
                <th scope="col" style="text-align:center;">Instructor</th>\
                <th scope="col" style="text-align:center;">Venue</th>\
            </tr>\
        </thead>\
        <tbody>';
        value = $(this).val();
        value = JSON.parse(value); // javascript, undoing it into what javascript can read
        if (this.checked) {
            biddingcart.push(value); // putting the checked row of details into the biddcart array
        } else {
            // biddingcart.splice((biddingcart.indexOf(value) - 1), 1 );
            biddingcart = biddingcart.filter(item => JSON.stringify(item) !== JSON.stringify(value))
        }
        if (biddingcart.length == 0) {
            phpbidvalue = '<h6 class="p-3">Bidding Cart Empty</h6>'
        } else {
            for (anitem of biddingcart) {
            phpbidvalue += '<tr><td>' + anitem["course"] + '</td>'
            phpbidvalue += '<td>' + anitem["title"] + '</td>'
            phpbidvalue += '<td>' + anitem["section"] + '</td>'
            phpbidvalue += '<td>' + anitem["instructor"] + '</td>'
            phpbidvalue += '<td>' + anitem["venue"] + '</td>'
            phpbidvalue += '</tr>'
            }
            phpbidvalue += '</tbody></table>'
        }
        $('#biddingcart').html(phpbidvalue) 

        // delete phpbidvalue;
    })
});

function checkout() { // takie the information in bidding cart put it into student-processcart in POST
    $.ajax({ // a post function (how we post data)
        type:'post',
        url: 'student-processcart.php',
        data: {bidcart: biddingcart},
        success: (function() {
            window.location.href = 'studentcheckout.php';
        })
    });
}

function searchCourse() { // how to search for a course
    // Declare variables
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("searchCourse"); 
    filter = input.value.toUpperCase();
    table = document.getElementById("searchTable");
    tr = table.getElementsByTagName("tr");
  
    // Loop through all table rows, and hide those who don't match the search query
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[1];
      if (td) {
        txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }
    }
  }