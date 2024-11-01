<?=$header?>
  <h1 class="<?=$noticeStatus?>"><?=$noticeTitle;?></h1>
  <row>
    <div class="col-sm-12"><?=$noticeDescription;?>
       <a href="/index.php?route=payment/payapi_payments/<?=$noticeRedirect;?>"><?=$clickHere;?></a>.
       <script>window.setTimeout(function(){
        window.location.href = "/index.php?route=payment/payapi_payments/<?=$noticeRedirect;?>";
      }, 3000);</script>
    </div>
  </row>
<?=$footer?>
