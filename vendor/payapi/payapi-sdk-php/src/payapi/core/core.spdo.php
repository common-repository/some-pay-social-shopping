<?php

namespace payapi;

use \PDO as PDO;

final class spdo
{

  public static
    $single                    =   false;

  protected
    $debug                     =   false;

  private
    $stream                    =    null,
    $stmt                      =    null;

  private function __construct($host, $database, $user, $password)
  {
    $this->debug = debug::single();
    $this->connect($host, $database, $user, $password);
  }

  public function query($sql, $binds = array())
  {
		$this->stmt = $this->stream->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$result = false;
		try {
			if ($this->stmt !== null && $this->stmt->execute($binds)) {
				$data = array();
        if ($this->stmt->rowCount() > 0) {
          while($row = $this->stmt->fetch(\PDO::FETCH_ASSOC)) {
  					$data[] = $row;
  				}
          $this->stmt->closeCursor();
  				$result = new \stdClass();
  				$result->row =((isset($data[0]) === true)? $data[0] : array());
  				$result->rows = $data;
  				$result->num_rows = $this->stmt->rowCount();
        } else {
          return true;
        }
			}
      $this->debug('[query] success');
		} catch(\PDOException $e) {
      $this->debug('[' . $e->getCode() . '] ' . $e->getMessage(), 'error');
      return false;
		}
		if ($result !== false) {
			return $result;
		} else {
			$result = new \stdClass();
			$result->row = array();
			$result->rows = array();
			$result->num_rows = 0;
			return $result;
		}
	}

  public function disconnect()
  {
      $this->stream = null;
  }

  private function connect($host, $database, $user, $password)
  {
    try {
      $this->stream = new \PDO(
        "mysql:host=" . $host . ";
        dbname=" . $database . ";
        char=utf8'",
        $user,
        $password
      );
      $this->stream->setAttribute(PDO::ATTR_PERSISTENT, true);
      $this->stream->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
      $this->stream->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
      $this->stream->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
      //-> @FIXME
      $this->stream->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->stream->exec("SET NAMES 'utf8'");
      $this->stream->exec("SET CHARACTER SET utf8");
      $this->stream->exec("SET CHARACTER_SET_CONNECTION=utf8");
      $this->stream->exec("SET SQL_MODE = ''");
      $this->debug('[spdo] connected');
    } catch(\PDOException $e) {
      $this->debug('[' . $e->getCode() . '] ' . $e->getMessage(), 'error');
    }
    return $this->stream;
  }

  public function escape($value)
  {
		return str_replace(array("\\", "\0", "\n", "\r", "\x1a", "'", '"'), array("\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"'), $value);
	}

  public function connected()
  {
    if ($this-> stream === null) {
      return true;
    } else {
      return false;
    }
  }

  public function lastId()
  {
		return $this-> stream->lastInsertId();
	}

  private function debug($data, $label = 'info')
  {
    if ($this->debug !== false) {
      return $this->debug->add($data, $label);
    }
    return false;
  }

  public static function single($host, $database, $user, $password)
  {
    if (self::$single === false) {
      self::$single = new self($host, $database, $user, $password);
    }
    return self::$single;
  }

  public function __destruct()
  {
    $this->disconnect();
  }


}
