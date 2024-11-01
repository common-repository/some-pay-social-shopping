<?=$header?>
    <h1><?=$text_secureconfirm_title?><img class="f-right" src="<?=PA_LOGO?>" height="28" alt="<?=PA_NAME?>" title="<?=PA_NAME?>"></h1>
    <form class="form-horizontal" action="/index.php?route=api/demo&mode=success" method="post">
    <row>
      <div class="col-sm-12">
      <?=$partialTopBlock?>
        <fieldset><legend><?=$text_order_legend?></legend>
        <div class="col-md-12 this-order-information" id="this-order-information">
          <dl class="dl-horizontal">
            <dt><?=$text_order_sumIncludingVat?></dt>
            <dd><?=$extendedProduct [ 'orderHtmlPriceInCentsIncVat' ]?></dd>
          </dl>
        </div>
        </fieldset>
      </div>
    </row>
    <row>
      <div class="col-sm-12">
        <fieldset>
          <legend><?=$text_product_legend?></legend>
          <div class="table-responsive">
            <table class="table table-striped">
              <tr>
                <th></th>
                <th><?=$text_product_title?></th>
                <th><?=$text_product_quantity?></th>
                <th><?=$text_product_priceIncludingVat?></th>
              </tr>
              <tr>
                <td class="show-image">
                  <div class="product-header">
                    <div class="product-container">
                      <a href="<?=$extendedProduct [ 'productUrl' ]?>">
                        <img class="product-image" src="<?=$extendedProduct [ 'thumbUrl' ]?>" alt="<?=$product [ 'name' ]?>" onerror="this.style.display=&quot;none&quot;">
                      </a>
                    </div>
                  </div>
                </td>
                <td>
                  <?=$product [ 'name' ]?>
                </td>
                <td><?=$productMainData [ 'quantity' ]?></td>
                <td><?=$extendedProduct [ 'productHtmlPriceIncVat' ]?></td>
              </tr>
              <tr>
                <td class="show-image"></td>
                <td><?=$shippinHandling?></td>
                <td>1</td>
                <td><?=$extendedProduct [ 'shippingHtmlPriceIncVat' ]?></td>
              </tr>
            </table>
          </div>
        </fieldset>
      </div>
    </row>
    <row>
      <div class="col-sm-6">
        <fieldset>
          <legend><?=$text_consumer_legend?></legend>
          <div class="col-md-12">
            <dl class="dl-horizontal">

              <dd class="shippingAddress">
                <?=$customer [ 'fullname' ]?><br>
                <?=$customer [ 'address1' ]?><br>
                <?=$customer [ 'postal' ]?> <?=$customer [ 'city' ]?>, <?=$customer [ 'region' ]?><br>
                <?=$customer [ 'country' ]?><br>
              </dd>
            </dl>
          </div>
        </fieldset>
      </div>
      <div class="col-sm-6">
        <fieldset>
          <legend><?=$text_creditCard_legend?></legend>
          <div class="col-md-12">
              <dl class="dl-horizontal">
                <?=$normalPaymentInfo?>
                <?=$partialPaymentInfo?>
              </dl>
              <div class="form-group">
                <label class="col-md-4 control-label" for="payment"></label>
              </div>
            </div>
            </fieldset>
          </div>
        </row>
        <span class="clear-color"></span>
        <div class="but-control">
          <div class="fixed-control">
            <button class="btn btn-success" id="payment" name="payment"><?=$text_creditCard_sbutton?></button>
          </div>
          <a class="btn btn-danger" id="cancel" href="/index.php?route=api/demo&mode=cancel"><?=$text_creditCard_cbutton?></a>
          <span class="clear"></span>
          <!--script>jQuery(\'#payment\').scrollToFixed({marginBottom: 0, zIndex: 1});</script-->
        </div>
      </form>
    </div>
<?=$footer?>
