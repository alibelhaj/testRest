<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * @Rest\Get("/users")
     * @ApiDoc(
     *     section="Get list user",
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     * @return String
     *
     */
    public function getUsersAction()
    {
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository('AppBundle:User')->findAll();
        // Cr?ation d'une vue FOSRestBundle
        $view = View::create($result);
        $view->setFormat('json');
        return $view;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/users/{id}", requirements={"id": "\d+"})
     * @ParamConverter("user", class="AppBundle:User")
     * @ApiDoc(
     *     section="Get Detail user",
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     * @return String
     *
     */
    public function getUserAction(User $user)
    {
        // Cr?ation d'une vue FOSRestBundle
        $view = View::create($user);
        $view->setFormat('json');
        return $view;
    }

    /**
     *
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/users")
     * @ApiDoc(
     *   section="Post users",
     *   resource = true,
     *   parameters={
     *      {"name"="email", "dataType"="string", "required"=true, "description"="Email utilisateur "},
     *      {"name"="nom", "dataType"="string", "required"=true, "description"="nom utilisateur"},
     *      {"name"="prenom", "dataType"="string", "required"=true, "description"="pr?nom utilisateur"},
     *      {"name"="groupe", "dataType"="integer", "required"=true, "description"="id groupe"},
     *      {"name"="actif", "dataType"="boolean", "required"=false, "description"="Actif"},
     *   },
     *   statusCodes = {
     *     201 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     * @return String
     */
    public function postUsersAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->submit($request->request->all());
        $em = $this->get('doctrine.orm.entity_manager');

        if ($form->isValid()) {
            $em->persist($user);
            $em->flush();

            return new JsonResponse($user, 201);
        }

        return new JsonResponse(['error' => $this->get('form.error')->getFormErrorMessages($form)]);
    }

    /**
     *
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Put("/users/{id}")
     * @ApiDoc(
     *   section="Put users",
     *   resource = true,
     *   parameters={
     *      {"name"="email", "dataType"="string", "required"=true, "description"="Email utilisateur "},
     *      {"name"="nom", "dataType"="string", "required"=true, "description"="nom utilisateur"},
     *      {"name"="prenom", "dataType"="string", "required"=true, "description"="pr?nom utilisateur"},
     *      {"name"="groupe", "dataType"="integer", "required"=true, "description"="id groupe"},
     *      {"name"="actif", "dataType"="boolean", "required"=false, "description"="Actif"},
     *   },
     *   statusCodes = {
     *     201 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     * @return String
     */
    public function putUsersAction(Request $request)
    {

        $user = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:User')
            ->find($request->get('id')); // L'identifiant en tant que param?tre n'est plus n?cessaire
        /* @var $user User */

        if (empty($user)) {
            return new JsonResponse(['message' => 'user not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(UserType::class, $user);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($user);
            $em->flush();
            return new JsonResponse(['message' => 'success'],Response::HTTP_OK);
        } else {
            return new JsonResponse(['error' => $this->get('form.error')->getFormErrorMessages($form)]);
        }


    }

}