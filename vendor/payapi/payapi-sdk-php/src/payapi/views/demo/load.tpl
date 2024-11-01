<?=$header?>
    <br>
    <h1 align="center"><?=$text_loading_title?></h1>
    <div class="row">
      <div class="loading">
        <p><ul class="fa-ul loading-steps" id="loadingSteps">
        <li><i class="fa-li fa fa-spinner fa-spin fa-2x fa-pull-left"></i><?=$text_loading_step1?></li>
        <li><i class="fa-li fa fa-spinner fa-spin fa-2x fa-pull-left"></i><?=$text_loading_step2?></li>
        <li><i class="fa-li fa fa-spinner fa-spin fa-2x fa-pull-left"></i><?=$text_loading_step3?></li>
        <li><i class="fa-li fa fa-spinner fa-spin fa-2x fa-pull-left"></i><?=$text_loading_step4?></li>
        <li><i class="fa-li fa fa-spinner fa-spin fa-2x fa-pull-left"></i><?=$text_loading_step5?></li>
        </ul></p>
      </div>
    </div><br><br><br><br>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="<?=$media?>lib/ScrollToFixed/jquery-scrolltofixed-min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
    <script src="<?=$media?>/payapi.client.validator.js"></script>
    <script src="<?=$media?>/lib/jquery-serialize-object/dist/jquery.serialize-object.min.js"></script>
    <?php
    $script = '
    <script>
    function flatten(obj, path, result) {
      var key, val, _path;
      path = path || [];
      result = result || {};
      for (key in obj) {
        val = obj[key];
        _path = path.concat([key]);
        if (val instanceof Object) {
          flatten(val, _path, result);
        } else {
          result[_path.join(\'.\')] = val;
        }
      }
      return result;
    }

    var showTick = function(elem){
      elem.find(\'i\').removeClass(\'fa-spinner fa-spin\').addClass(\'fa-check\').css(\'color\',\'#4CAF50\');
    };

    function showLoadingTicks() {

      var loadingStepsLi = $(\'#loadingSteps\').find(\'li\');
      var delaySteps = 0;
      loadingStepsLi.each(function(i) {
        if(i < loadingStepsLi.length ){
          delaySteps += Math.floor( Math.random() * (500));
          setTimeout(showTick, delaySteps, $(this));
          if ( i == ( loadingStepsLi.length - 1 ) ) {
            // add timer
            setTimeout(function() {
              window.location = "' . $redirectLocation. '";
            }, delaySteps );
          }
        }
      });
      return true ;
    }

    var messages = {};
    var locales = [\'en-US\', \'es-ES\', \'fi-FI\'];
    var locale =\'' . $settingLocale . '\'; // get session locale;
    var publicId = \'' . $settingPublicId . '\';
    $.getJSON(\'/private/locales/\'+ (locales.indexOf(locale) < 0 ? \'en-US\' : locale), function(json) {
      messages.invalid = json;
      messages = flatten(messages);
    });
    $(function() {
      showLoadingTicks();
      $(\'input\').focusout(function() {
        $(this).parent().parent().removeClass(\'has-error has-feedback\');
        $(this).popover("hide");
        $(this).nextAll(\'span.glyphicon.glyphicon-warning-sign.form-control-feedback\').remove();
      });
      $(\'form\').submit(function(e) {
        // Allow form submit once
        if( $(this).hasClass(\'form-submitted\') ){
          e.preventDefault();
          return false;
        }
        $(this).addClass(\'form-submitted\');
        var validator = new InputDataValidator($(\'form\').serializeObject());
        var validationErrors = validator.validateForm();
        if(validationErrors.length > 0) {
          for(var i = 0; i < validationErrors.length; i++) {
            var element = $(\'input[name="\' + validationErrors[i].elementName + \'"]\');
            element.parent().parent().addClass(\'has-error has-feedback\');
            element.after(\'<span class="glyphicon glyphicon-warning-sign form-control-feedback"></span>\');
            var _popover;
            _popover = $(element).popover({
              trigger: "manual",
              placement: "top",
              template: "<div class=\"popover\"><div class=\"arrow\"></div><div class=\"popover-inner\"><div class=\"popover-content\"></div></div></div>"
            });
            // TODO: get translation from messages object
            _popover.data("bs.popover").options.content = messages[validationErrors[i].translationKey] || \'TODO: load "invalid" dictionary from localizations\';
            $(element).popover("show");
          }
          $(\'input[name="\' + validationErrors[0].elementName + \'"]\').focus();
          return false;
        } else {
          return true;
        }
      });
      var regpath = new RegExp(\'^(/v1/secureform/\'+publicId+\')/?$\');
      var regWebshop = new RegExp(\'^(/v1/webshop/)\');
      var initGet = performance.now();
      // Redirect if returning consumer
      if(regpath.test(window.location.pathname) || regWebshop.test(window.location.pathname)) {
        var fngrAppData = null;
        if(typeof AndroidPayapiInterface !== "undefined" && typeof AndroidPayapiInterface.getPayapiData === \'function\'){
          var resu = AndroidPayapiInterface.getPayapiData();
          if(typeof resu !==\'undefined\') { fngrAppData = resu; }
        }else if(typeof getPayapiDataIOS === "function"){
          var resu = getPayapiDataIOS();
          if(typeof resu !==\'undefined\') { fngrAppData = resu; }
        }
        $.get( "/int/Consumer/isReturningConsumer", { \'fngrAppData\': fngrAppData })
          .success(function(data, statusText, xhr) {
            var waitFor = performance.now() - initGet;
            setTimeout(function() {
              $(\'#loadingSteps\').find(\'li\').find(\'i\').removeClass(\'fa-spinner fa-spin\').addClass(\'fa-check\').css(\'color\',\'#4CAF50\');
            }, waitFor > 2000 ? 0 : 2000);

            window.setTimeout(function() { window.location.href=data.redirectUrl; }, waitFor > 2000 ? 0 : 2000);
          })
          .error(function(data, statusText, xhr) {
            window.location.href = \'/v1/secureform/\'+publicId + \'/return\';
          })
      }
      $("#selectbasic").on(\'change\', function(e) {
        $.post( "/v1/secureform/" + publicId + "/paymentMethod", { \'paymentMethod\': $(this).val() }, function(data) {
          if(data.hideCreditCardForm) {
            $(".creditCardPayment").hide(250);
            $(".creditCardPayment :input").each(function() {
              $(this).prop(\'disabled\', true);
            });
          } else if($(".creditCardPayment").is(\':hidden\')) {
            $(".creditCardPayment").show(250);
            $(".creditCardPayment :input").each(function() {
              $(this).prop(\'disabled\', false);
            });
          }
        });
        return true;
      }).change();
    });
    </script>' ;
    echo $script ;
    ?>
    <?=$footer?>
  </body>
</html>
