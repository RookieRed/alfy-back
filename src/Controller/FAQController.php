<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 21/06/2018
 * Time: 14:20
 */

namespace App\Controller;


use App\Utils\JsonSerializer;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as Doc;

/**
 * Class CareerController
 * @package App\Controller
 *
 * @Route(path="/faq")
 */
class FAQController extends JsonAbstractController
{
    public function __construct(JsonSerializer $serializer)
    {
        parent::__construct($serializer);
    }

    /**
     * @Route(path="/{careerId}", name="career_update", methods={"PUT"})
     * @Doc\Tag(name="Carrières", description="Gestion des carrières des utilisateurs.")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function getCategories() {}
    public function addCategory() {}
    public function updateCategory() {}
    public function removeCategory() {}

    public function getPage() {}
    public function removeQuestion() {}
    public function addQuestion() {}
    public function updateQuestion() {}

}