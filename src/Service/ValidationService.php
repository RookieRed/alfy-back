<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 13/07/2018
 * Time: 10:53
 */

namespace App\Service;


use App\Constants\ErrorType;
use App\Entity\Pojo\JsonError;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
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
     * @return null|Response
     */
    public function validateBean($bean, array $groups): ?Response
    {
        $validationErrors = $this->validator->validate($bean, null, $groups);

        $nbErrors = $validationErrors->count();
        if ($nbErrors > 0) {
            $errors = [];
            for ($i = 0; $i < $nbErrors; $i++) {
                $errors[] = $validationErrors->get($i)->getMessage();
            }

            return $this->generateErrorResponse(
                ErrorType::VALIDATION_ERROR['code'],
                ErrorType::VALIDATION_ERROR['message'],
                $errors,
                Response::HTTP_BAD_REQUEST
            );
        }

        return null;
    }

    public function generateErrorResponse($code, $message, $errors = [], $status = Response::HTTP_INTERNAL_SERVER_ERROR): Response
    {
        $errorBean = new JsonError();
        $errorBean->setCode($code)
            ->setMessage($message);

        if (count($errors)) {
            $errorBean->setErrors($errors);
        }
        $response = new Response($this->serializer->serialize($errorBean, 'json'));
        $response->headers->set('Content-type', 'application/json');
        $response->setStatusCode($status);

        return $response;
    }
}