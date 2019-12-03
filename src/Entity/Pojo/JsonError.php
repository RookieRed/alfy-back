<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 13/07/2018
 * Time: 10:48
 */

namespace App\Entity\Pojo;


class JsonError
{
    /**
     * @var string|null
     */
    private $code;
    /**
     * @var string|null
     */
    private $message;
    /**
     * @var string[]
     */
    private $errors;

    public function __construct()
    {
        $this->errors = [];
    }

    /**
     * @return null|string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param null|string $code
     * @return JsonError
     */
    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param null|string $message
     * @return JsonError
     */
    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     * @return JsonError
     */
    public function setErrors(array $errors): self
    {
        $this->errors = $errors;

        return $this;
    }

    public function addError(string $error): self
    {
        $this->errors[] = $error;

        return $this;
    }
}