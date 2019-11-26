 $(document).ready(function(){
     var totalSum = 0.00;   
    $('#result').text(totalSum.toFixed(2));

    $("#checkoutform").on('input', '#bid-total',function(){
        var totalSum = 0.00;
        $('#checkoutform #bid-total').each(function(){
            var totalVal = $(this).val();
            if($.isNumeric(totalVal)){
                totalSum += parseFloat(totalVal);
            }
        });
        $('#result').text(totalSum.toFixed(2));
    });
});

function setTwoNumberDecimal(el) {
    el.value = parseFloat(el.value).toFixed(2);
}