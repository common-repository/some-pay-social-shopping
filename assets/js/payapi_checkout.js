(function($) {
    $(document).ready(function() {
        setTimeout(function(){

        $("#payapi-checkout-partial").click(function(evt){
            postToPayApi(true);
            evt.preventDefault();
        });
        },1000);

        $("form[name='checkout']").submit(function(e) {
            if (usingPayApiGateway()) {
                postToPayApi(false);
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
        });
    });

    function postToPayApi(hasPartial){
        var jsonPostData = { "payapidata" : form2json($("form[name='checkout']")) };
        if(hasPartial){
            jsonPostData["partialPaymentsEnabled"] = 1;
        }
                $.ajax({
                    showLoader: true,
                    type: "POST",
                    url: "/wp-json/payapi-gateway/v1/secureformgenerator",
                    data: jsonPostData,
                    success: function(object) {
                        var form = document.createElement('form');
                        form.style.display = 'none';
                        form.setAttribute('method', 'POST');
                        form.setAttribute('action', object.url);
                        form.setAttribute('enctype', 'application/json');
                        var input = document.createElement('input');
                        input.name = 'data';
                        input.type = 'text';
                        input.setAttribute('value', object.data);
                        form.appendChild(input);
                        document.getElementsByTagName('body')[0].appendChild(form);
                        form.submit();
                    }
                });
    }

    function usingPayApiGateway() {
        return $('form[name="checkout"] input[name="payment_method"]:checked').val() == 'payapisecureform';
    }

    function form2json(form) {
        var formArray = form.serializeArray();
        var returnArray = {};
        for (var i = 0; i < formArray.length; i++) {
            returnArray[formArray[i]['name']] = formArray[i]['value'];
        }
        return returnArray;
    }


})(jQuery);