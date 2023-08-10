<?php
namespace App\Engines;

use Liman\Toolkit\Shell\ICommandEngine;
use phpseclib3\Net\SFTP as sftp;

class SFTPEngine
{
	
	private static $connection;
	private static $initialized = false;

	public static function putFile($file,$path)
	{
		if (!self::$initialized) {
			throw new \Exception(
				'The SFTP Engine class must ve initialized with init() function.'
			);
		}
		return self::$connection->put($file,$path);
	}

    public static function putFolder($file,$path)
	{
		if (!self::$initialized) {
			throw new \Exception(
				'The SFTP Engine class must ve initialized with init() function.'
			);
		}
		return self::$connection->put($file,$path);
	}

    public static function list($path)
    {
        if (!self::$initialized) {
            throw new \Exception(
                'The SFTP Engine class must ve initialized with init() function.'
            );
        }
        return self::$connection->nlist($path,true);
    }

	public static function chdir($directory) {
		if (!self::$initialized) {
            throw new \Exception(
                'The SFTP Engine class must ve initialized with init() function.'
            );
        }
        return self::$connection->chdir($directory);
	} 

	public static function getSelectedFile($path)
    {
        if (!self::$initialized) {
            throw new \Exception(
                'The SFTP Engine class must ve initialized with init() function.'
            );
        }
        return self::$connection->get($path);
    }

	public static function is_dir($path)
	{
		if (!self::$initialized) {
            throw new \Exception(
                'The SFTP Engine class must ve initialized with init() function.'
            );
        }
        return self::$connection->is_dir($path);

	}

	public static function sudo()
	{
		return 'sudo ';
	}

	public static function init($ipAddress, $username, $password)
	{
		$connection = new sftp($ipAddress);
		if (!$connection->login($username, $password)) {
			throw new \Exception(
				$connection->isConnected()
					? abort("SFTP ile giriş yapılamadı!", 201) : abort("İstemciye bağlanılamadı!", 201)
			);
		}
		self::$connection = $connection;
		self::$initialized = true;
        return self::$connection;
	}
}