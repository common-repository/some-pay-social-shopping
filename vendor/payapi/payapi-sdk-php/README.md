PayApi Payments PHP SDK


  @NOTE

        - 1. to log into sdk thought :
              $sdk -> settings ( $payapi_public_id , $payapi_api_enc_key ) ;
               this returns merchantSettings payload if valid
                and creates an encrypted cache for both:
                payapi/cache/<instanceKey>/account
                payapi/cache/<instanceKey>/settings

        - 2. commands :
              $sdk -> instantPayment ( $data ) ;                     //-> validate/sanitize/cache product/payment data
                                                                          returns array ( "metadata" , "endPointPayment" , "endPointProductPayment" ) ;
              $sdk -> brand () ;                                     //-> gets brand/partner info
              $sdk -> callback () ;                                  //-> gets/cache callback decoded transaction
              $sdk -> localize () ;                                  //-> gets/cache ip decoded localization (plugin adds adapt country and zone ids)
              $sdk -> localize ( true ) ;                            //-> gets & refresh ip decoded localization cache (plugin adds adapt country and zone ids)
              $sdk -> settings ( 'public_id' , 'api_key' , true ) ;  //-> verify account & gets/cache instance merchantSettings, also refresh account data
              $sdk -> settings ( false , false , true ) ;            //-> refresh & gets instance merchantSettings
              $sdk -> settings () ;                                  //-> gets instance cached merchantSettings
              $sdk -> partialPayment ( $totalInCents , $currency ) ; //-> calculate partialPayment from merchantSettings
              $sdk -> response ( <standard_response_code_int> ) ;    //-> get response info
              $sdk -> info () ;                                      //-> gets sdk debug info


  @return

        - success :
              array (
                "code"  =>    ( int )            200 ,
                "data"  =>  ( array ) <expectedData>
              ) ;

        - error :
              array (
                "code"  =>    ( int )    <errorCode> ,
                "error" => ( string )    <errorData>
              ) ;



  @NOTE to debug, in payapi folder:
  
        $ tail -f -n300 debug/debug.payapi.log | perl colored.pl


  @NOTE check 'test' folder out
  