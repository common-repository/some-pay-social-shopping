<?php

namespace payapi;

/*
* @NOTE   DO NOT USE
*         just for OC migration
*/
class commandMigrate extends controller
{

  private
    $instances           =   array(),
    $update              =   array(),
    $clones              =   array(),
    $disabled            =   array(),
    $folder              =      false,
    $encKey              =      false,
    $results             =      false;

  public function run()
  {
    $this->folder = $this->route->parentDir($this->route->parentDir($this->route->parentDir($this->route->root()))) . 'config' . DIRECTORY_SEPARATOR;
    $this->encKey = md5('Th1s_mag1c_s3nt3nc3_1s_G00d');
    $this->fetch();
    $this->updateConfig();
    $this->updateDb();
    $info = array(
      //"folder" => $this->folder,
      "instance" => $this->instances //,
      //"disabled" => $this->clones,
      //"clones" => $this->clones,
      //"update" => $this->update
    );

    /* testing SPDO
    $test = $this->instances['f91bc43b84a728076a8c992e50bb1891']['database'];
    $spdo = spdo::single($test['DB_HOSTNAME'], $test['DB_DATABASE'], $test['DB_USERNAME'], $test['DB_PASSWORD']);
    $query = "SELECT * FROM `oc_setting` WHERE `code` = 'payapi_payments' LIMIT 1";
    $test = $spdo->query($query);
    return $this->render($test);
    //*/
    return $this->returnResponse($this->error->notImplemented());
  }

  private function fetch()
  {
    $this->fetchInstances();
    $this->fetchClones();
    $this->fetchDisabled();
  }

  private function fetchInstances()
  {
    foreach(glob($this->folder . 'store' . '.' . '*' . '.' . 'json') as $instanceFile) {
      $instance = json_decode($this->decode(file_get_contents($instanceFile), $this->encKey), true);
      if (isset($instance['store']) === true && isset($instance['database']) === true) {
        $this->instances[md5($instance['store']['STORE_DOMAIN'])] = $instance;
      } else {
        $this->results[] = 'error: instance->' . $instanceFile;
      }
    }
    return $this->instances;
  }

  private function fetchClones()
  {
    foreach(glob($this->folder . 'clone' . '.' . '*' . '.' . 'json') as $cloneFile) {
      $clone = json_decode($this->decode(file_get_contents($cloneFile), $this->encKey), true);
      if (isset($clone['store']) === true && isset($clone['database']) === true) {
        $this->clones[] = $clone;
      } else {
        $this->results[] = 'error: clone->' . $cloneFile;
      }
    }
    return $this->instances;
  }

  private function fetchDisabled()
  {
    foreach(glob($this->folder . 'disabled' . '.' . '*' . '.' . 'json') as $disabledFile) {
      $disabled = json_decode($this->decode(file_get_contents($disabledFile), $this->encKey), true);
      if (isset($disabled['store']) === true && isset($disabled['database']) === true) {
        $this->disabled[] = $disabled;
      } else {
        $this->results[] = 'error: disabled->' . $disabledFile;
      }
    }
    return $this->instances;
  }

  private function updateConfig()
  {
    $error = 0;
    foreach($this->instances as $instance => $config) {
      //-> @TODO update config data
      $this->saveUpdate($instance, $this->cache('writte', 'instance', $instance, $config));
      $this->debug('[cache] ' . $instance . ' saved');
      $this->update[$instance] = $this->updateSettings($config);
    }
    if ($error === 0) {
      return true;
    }
    $this->results[] = 'error: config->' . $instance;
    return false;
  }

  private function updateDb()
  {
    foreach($this->instances as $instance => $config) {
      if ($instance == 'f91bc43b84a728076a8c992e50bb1891') {
        $database = $config['database'];
        $spdo = spdo::single($database['DB_HOSTNAME'], $database['DB_DATABASE'], $database['DB_USERNAME'], $database['DB_PASSWORD']);
        $this->cloneDatabase($spdo, $database['DB_DATABASE'], $database['DB_DATABASE'] . '_bup');
      }
    }
    //->
  }

  private function updateMedia()
  {
    //->
  }

  private function updateFiles()
  {
    //->
  }

  private function updateSettings($settings)
  {
    //->
    return $settings;
  }

  private function saveUpdate($instance, $status = false)
  {
    return $this->cache('writte', 'update', $instance, array("updated" => $status));
  }

  private function cloneDatabase($spdo, $dbName, $dbNewName)
  {
    //$selectDb = @mysql_select_db($dbName);
    //$selectDb = $spdo->query("USE `$dbName`");
    $tables  = $this->cleanTables($dbName, $spdo->query("SHOW TABLES"));
    if ($tables !== false) {
      try {
        //$drop = $spdo->query("DROP DATABASE IF EXIST " . $dbNewName);
        $drop = $spdo->query("DROP DATABASE " . $dbNewName . "");
        $createTable = $spdo->query("CREATE DATABASE IF NOT EXISTS $dbNewName DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
        //$selectDb = $spdo->query("USE $dbNewName");
        foreach($tables as $key => $cloneTable) {
          $create = $spdo->query("CREATE TABLE `" . $dbNewName . "`.`" . $cloneTable . "` LIKE `" . $dbName . "`.`" . $cloneTable . "`");
          if ($create !== false) {
            $insert = $spdo->query("INSERT INTO `" . $dbNewName . "`.`" . $cloneTable . "` SELECT * FROM `" . $dbName . "`.`" . $cloneTable . "`");
            $this->debug('[' . $cloneTable . '] updated', 'table');
          } else {
            $error = true;
          }
        }
        $spdo = null;
        return ! isset($error);
      } catch(\Exception $e) {
        $this->debug($e->getMessage(), 'error');
      }
    }
    $spdo = null;
    return false;
  }

  private function cleanTables($db, $tables)
  {
    $clean = array();
    foreach($tables->rows as $key => $table) {
      $entry = 'Tables_in_' . $db;
      if (isset($table[$entry]) === true) {
        $clean[] = $table[$entry];
      }
    }
    return $clean;
  }


}
