<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 25/06/2018
 * Time: 14:59
 */

namespace App\Constants;


class FileConstants
{
    const DEFAULT_CONTACT_CSV_FILE = '/config/fixtures/contacts-alfy.ods';
    const XLS_FIRST_NAME = 'Prénom';
    const XLS_LAST_NAME = 'Nom';
    const XLS_BIRTHDAY = 'Date de naissance';
    const XLS_EMAIL = 'Email';
    const XLS_PHONE = 'Téléphone';
    const XLS_BAC = 'Bac';
    const XLS_USERNAME = 'Nom d\'utilisateur';

    const UPLOAD_DIR = '/files/uploads/';
    const MODELS_DIR = '/files/models/';
    const GENERATED_XLS = 'import_alfy.xls';

    const DEFAULT_NO_IMAGE_FILE = '/files/pictures/default.png';

    const PROFILE_PICTURES_DIR = '/files/pictures/profiles/';
    const COVER_PICTURES_DIR = '/files/pictures/covers/';
    const PAGES_PICTURES_DIR = '/files/pictures/pages/';
    const SLIDE_SHOW_DIR = '/files/slide-show/';
    const TILES_PICTURE_DIR = '/files/tiles-pictures/';
}