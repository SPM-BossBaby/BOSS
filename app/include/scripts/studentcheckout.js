//live calculator 
$(document).ready(function(){
     var totalSum = 0.00;   
    $('#result').text(totalSum.toFixed(2)); //2 decimal

    $("#checkoutform").on('input', '#bid-total',function(){ //on id checkout form, total will change live according to input
        var totalSum = 0.00;
        $('#checkoutform #bid-total').each(function(){
            var totalVal = $(this).val();
            if($.isNumeric(totalVal)){
                totalSum += parseFloat(totalVal);
            }
        });
        $('#result').text(totalSum.toFixed(2)); //return in fixed 2 dec
    });
});

function setTwoNumberDecimal(el) {
    el.value = parseFloat(el.value).toFixed(2);
}