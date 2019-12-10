<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 13/07/2018
 * Time: 11:05
 */

namespace App\Constants;


class ErrorType
{

    const EXCEPTION = [
        'code' => 'E00',
        'message' => 'Exception levée'
    ];

    const VALIDATION_ERROR = [
        'code' => 'E01',
        'message' => 'Formulaire non valide'
    ];

}