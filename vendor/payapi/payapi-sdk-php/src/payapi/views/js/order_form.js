/**
 * 2015-2017 PayApi
 *
 *  @author    Efim Polevoi <efim@payapi.in>
 *  @copyright 2017 PayApi Ltd
 *  @license   GPL v3.0
 */
$(document).ready(function() {
    //console.log("PayApi Order Form");

    //var publicId = "'.$public_id.'";
    var form = document.createElement("form");
    form.style.display = "none";
    form.setAttribute("method", "POST");
    if (payapi_staging_env) {
        form.setAttribute("action", "https://staging-input.payapi.io/v1/secureform/"+payapi_public_id);
    } else {
        form.setAttribute("action", "https://input.payapi.io/v1/secureform/"+payapi_public_id);
    }
    form.setAttribute("enctype", "application/json");
    form.setAttribute("id", "order-form-post");

    //var data = "'.$data.'";
    //console.log("data: " + payapi_payload_data);

    var input = document.createElement("input");
    input.name = "data";
    input.type = "text";
    input.setAttribute("value", payapi_payload_data);

    form.appendChild(input);
    document.getElementsByTagName("body")[0].appendChild(form);

    // $("<input type=\"button\" id=\"payapi-order-btn\" class=\"btn btn-primary center-block\" onclick=$(\"#order-form-post\").submit() value=\"Order with an obligation to pay\">").insertAfter("#payment-confirmation");
    // $("#payapi-order-btn").hide();
    // $("#payapi-order-btn").prop("disabled", true);
    // $("input[type=radio][name=payment-option]").change(function() {
    //     var container_id = this.id + "-container";
    //     //console.log("container_id: " + container_id);
    //     var label_text = $("#" + container_id).find("label[for=" + this.id + "]").text();
    //     if (label_text.indexOf("PayApi") !== -1) {
    //         $("#payment-confirmation .ps-shown-by-js :button").hide();
    //         $("#payapi-order-btn").show();
    //     } else {
    //         $("#payment-confirmation .ps-shown-by-js :button").show();
    //         $("#payapi-order-btn").hide();
    //     }
    // });
    // $("input[type=checkbox][class=ps-shown-by-js]").change(function() {
    //     if (this.checked) {
    //         $("#payapi-order-btn").prop("disabled", false);
    //     }
    //     else {
    //         $("#payapi-order-btn").prop("disabled", true);
    //     }
    // });
});
