<?php

namespace App;

/**
 * Application configuration
 *
 * PHP version 7.0
 */
class Config
{

    /**
     * Database host
     * @var string
     */
    const DB_HOST = 'localhost';

    /**
     * Database name
     * @var string
     */
    const DB_NAME = 'stampee';

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'root';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = 'root'; // mac
    // const DB_PASSWORD = ''; // windows

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;
    
    // const PATH_DIR = "http://localhost:8888/stampee/"; 

    const URL_RACINE = "http://localhost:8888/stampee/public/"; // mac
    // const URL_RACINE = "http://localhost:8000/stampee/public/"; // windows
}
