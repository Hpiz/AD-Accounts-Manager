<?php namespace Config;

use CodeIgniter\Config\Services as CoreServices;
use CodeIgniter\Config\BaseConfig;

require_once SYSTEMPATH . 'Config/Services.php';

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends CoreServices
{

	//    public static function example($getShared = true)
	//    {
	//        if ($getShared)
	//        {
	//            return static::getSharedInstance('example');
	//        }
	//
	//        return new \CodeIgniter\Example();
	//    }
	public static function renderer($viewPath = null, $config = null, bool $getShared = false)
    {
        if ($getShared)
        {
            return static::getSharedInstance('renderer', $viewPath, $config);
        }

        if (is_null($config))
        {
            $config = new \Config\View();
        }

        if (is_null($viewPath))
        {
            $paths = config('Paths');

            $viewPath = $paths->viewDirectory;
        }

        return new \App\Middleware\BaseView($config, $viewPath, static::locator(true), CI_DEBUG, static::logger(true));
    } 
}