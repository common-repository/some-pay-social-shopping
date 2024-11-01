<?php

namespace payapi;

/*
* @COMMAND
*           $sdk->cron()
*
* @TYPE     private
*
* @RETURNS
*           standard PHP response/error
*
* @SAMPLE
*          ["code"]=>
*           int(200)
*          ["error"]=>
*           string(15) "success"
*
* @NOTE
*           just for CRON
*            (it does not work with user access just from cli)
*           refresh SSL, merchant settings and sanitize old cache(s)
*
*/
final class commandCron extends controller
{

  public function run()
  {
    if ($this->validate->commandLineInterfaceAccess() === true) {
      $this->debug('[cron] valid access');
      //-> check SSL(done in controller auto)
      //-> check if merchant account
      //-> sanitize caches
      if ($this->sanitizeCache() === true) {
        return $this->render('success');
      }
      $this->error('cannot sanitize cache files', 'warning');
      return $this->returnResponse($this->error->noCacheSanitization());
    }
    $this->error('[cron] unvalid access', 'warning');
    return $this->returnResponse($this->error->noValidCronAccess());
  }

  private function sanitizeCache()
  {
    return $this->cache->sanitize();
  }


}
