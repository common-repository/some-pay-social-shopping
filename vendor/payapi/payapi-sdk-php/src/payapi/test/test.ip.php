<?php
//-> testing PA ip endPoint performance
$many = 50 ;
$fetched = 0 ;
$error = 0 ;
for ( $cont = 0 ; $cont < $many ; $cont ++ ) {
  $localization = $sdk -> localize ( true , '79.159.' . rand ( 141 , 240 ) . '.' . rand ( 141 , 240 ) ) ;
  if ( isset ( $localization [ 'code' ] ) !== true || $localization [ 'code' ] !== 200 ) {
    $error ++ ;
  }
  var_dump ( $localization ) ;
  $fetched ++ ;
}

$test = 'test localize x' . $fetched . ' [ ' . $error . ' errors ]' ;
