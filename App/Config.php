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
    // const DB_NAME = 'e2296236'; // webdev

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'root';
    // const DB_USER = 'e2296236';// webdev

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = 'root'; // mac
    // const DB_PASSWORD = ''; // windows
    // const DB_PASSWORD = 'owioZ7vb1n0D0d4uLPw4'; // webdev

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;
    
    const PATH_DIR = "/Applications/MAMP/htdocs/Stampee/public/"; 

    const URL_RACINE = "http://localhost:8888/stampee/public/"; // mac
    // const URL_RACINE = "http://localhost:8000/stampee/public/"; // windows
    // const URL_RACINE = "https://e2296236.webdev.cmaisonneuve.qc.ca/stampee/public/"; // webdev

}
