<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 13/07/2018
 * Time: 10:53
 */

namespace App\Service;

use App\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationService
{

    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator
    )
    {
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    /**
     * Validate a bean.
     *
     * Use form forms / input data.
     * If OK returns null, otherwise return a 400 Response object in JSON.
     * @param $bean
     * @param array $groups
     * @return bool true if valid
     * @throws ValidationException if invalid
     */
    public function validateOrThrowException($bean, array $groups): bool
    {
        $validationErrors = $this->validator->validate($bean, null, $groups);

        $nbErrors = $validationErrors->count();
        if ($nbErrors > 0) {
            throw new ValidationException($validationErrors);
        }
        return true;
    }
}