<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Groupe;
use AppBundle\Form\GroupeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;


class GroupeController extends Controller
{
    /**
     * @Rest\Get("/groups")
     * @ApiDoc(
     *     section="Get list groupe",
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     * @return String
     *
     */
    public function getgroupsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository('AppBundle:Groupe')->findAll();
        // Cr?ation d'une vue FOSRestBundle
        $view = View::create($result);
        $view->setFormat('json');
        return $view;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/groups/{id}", requirements={"id": "\d+"})
     * @ParamConverter("groupe", class="AppBundle:Groupe")
     * @ApiDoc(
     *     section="Get Detail groupe",
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     * @return String
     *
     */
    public function getGroupAction(Groupe $groupe)
    {
        // Cr?ation d'une vue FOSRestBundle
        $view = View::create($groupe);
        $view->setFormat('json');
        return $view;
    }


    /**
     *
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/groups")
     * @ApiDoc(
     *   section="Post Group",
     *   resource = true,
     *   parameters={
     *      {"name"="nom", "dataType"="string", "required"=true, "description"="Nom groupe"},
     *   },
     *   statusCodes = {
     *     201 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     * @return String
     */
    public function postGroupeAction(Request $request)
    {
        $groupe = new Groupe();
        $form = $this->createForm(GroupeType::class, $groupe);
        $form->submit($request->request->all());
        $em = $this->get('doctrine.orm.entity_manager');

        if ($form->isValid()) {
            $em->persist($groupe);
            $em->flush();

            return new JsonResponse($groupe, 201);
        }

        return new JsonResponse(['error' => $this->get('form.error')->getFormErrorMessages($form)]);
    }

    /**
     * @Rest\View()
     * @Rest\Put("/groups/{id}")
     *
     * @ApiDoc(
     *   section="Put Group",
     *   resource = true,
     *   parameters={
     *      {"name"="nom", "dataType"="string", "required"=true, "description"="Nom groupe"},
     *   },
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param Request $request the request object
     *
     * @return String
     */
    public function putGroupeAction(Request $request)
    {

        $groupe = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Groupe')
            ->find($request->get('id')); // L'identifiant en tant que param?tre n'est plus n?cessaire
        /* @var $groupe Groupe */

        if (empty($groupe)) {
            return new JsonResponse(['message' => 'group not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(GroupeType::class, $groupe);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($groupe);
            $em->flush();
            return new JsonResponse(['message' => 'success'],Response::HTTP_OK);
        } else {
            return new JsonResponse(['error' => $this->get('form.error')->getFormErrorMessages($form)]);
        }

    }

}