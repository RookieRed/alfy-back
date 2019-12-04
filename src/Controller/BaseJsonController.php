<?php


namespace App\Controller;


use App\Utils\JsonSerializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseJsonController extends AbstractController
{
    /** @var JsonSerializer $serializer */
    protected $serializer;

    public function __construct(JsonSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Generate a new JSON response based on :
     * @param mixed $data return as a response
     * @param array $groups for serialization : default none
     * @param int $status Http Status code, default : 200 OK
     * @param array $headers additional headers
     * @return JsonResponse
     */
    protected function json($data, int $status = 200, array $groups = [], array $headers = []): JsonResponse
    {
        return new JsonResponse(
            $this->serializer->jsonSerialize($data, $groups),
            $status,
            $headers,
            true
        );
    }

    /**
     * Generate a new JSON response with HTTP_OK 200 status code, based on :
     * @param mixed $data return as a response
     * @param array $groups for serialization : default none
     * @param array $headers additional headers
     * @return JsonResponse
     */
    public function jsonOK($data, $groups = [], $headers = []) {
        return $this->json($data, Response::HTTP_OK, $groups, $headers);
    }

    /**
     * Generate an empty response.
     * @return JsonResponse
     */
    public function noContent($headers = []) {
        return $this->json('', Response::HTTP_NO_CONTENT, [], $headers);
    }
}