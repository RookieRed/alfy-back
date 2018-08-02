<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 13/07/2018
 * Time: 14:13
 */

namespace App\Controller;


use App\Service\ValidationService;
use Symfony\Bundle\TwigBundle\Controller\ExceptionController;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Twig\Environment;

class JsonExceptionController extends ExceptionController
{

    private $validator;

    public function __construct(Environment $twig, bool $debug, ValidationService $validationService)
    {
        parent::__construct($twig, $debug);
        $this->validator = $validationService;
    }

    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        $previous = [];
        $allPrevious = $exception->getAllPrevious();
        foreach ($allPrevious as $prev) {
            $previous[] = $prev->getMessage();
        }

        return $this->validator->generateErrorResponse(
            $exception->getCode(),
            $exception->getMessage(),
            $exception->getTrace(),
            $exception->getStatusCode()
        );
    }
}