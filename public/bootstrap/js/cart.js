update_amounts();
$('.qty').change(function () {
    update_amounts();
});

function update_amounts() {
    var sum = 0.0;
    $('#myTable > tbody  > tr').each(function () {
        var qty = $(this).find('.qty').val();
        var price = $(this).find('.price').val();
        var amount = parseFloat(qty) * parseFloat(price);
        amount = isNaN(amount) ? 0 : amount;
        sum += amount;
        $(this).find('.amount').text('' + amount);
        $(this).find('.amount').val('' + amount);
    });
    $('.totalRow').text(sum);

}

$('.qtyplus').click(function (e) {
    e.preventDefault();
    var $this = $(this);
    var $target = $this.prev('input[name=' + $this.attr('field') + ']');
    var currentVal = parseInt($target.val());
    if (!isNaN(currentVal)) {
        $target.val(currentVal + 1);
    } else {
        $target.val(0);
    }
    update_amounts();

});
$(".qtyminus").click(function (e) {
    e.preventDefault();
    var $this = $(this);
    var $target = $this.next('input[name=' + $this.attr('field') + ']');
    var currentVal = parseInt($target.val());
    if (!isNaN(currentVal)) {
        $target.val((currentVal == 0) ? 0 : currentVal - 1);
    } else {
        $target.val(0);
    }
    update_amounts();

});

function calc() {
    var elems = document.getElementsByClassName("amount");
    var sum = 0;
    for (var i = 0; i < elems.length; i++) {
        var elem = elems[i];
        var num = elem.innerHTML;
        num = num.substr(0);
        sum += Number(num);

    }
    document.getElementById("cost").innerHTML = sum;
}

function calc2() {
    var elemsQty = document.getElementsByClassName('qty');
    var sumQty = 0;
    for (var i = 0; i < elemsQty.length; i++) {
        var elem = elemsQty[i];
        var num = elem.value;
        num = num.substr(0);
        sumQty += Number(num);
        console.log(elem);
    }
    document.getElementById("totalQty").innerHTML = sumQty;
    calc();
}
