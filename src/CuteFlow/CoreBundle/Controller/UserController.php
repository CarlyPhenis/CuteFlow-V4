<?php
namespace CuteFlow\CoreBundle\Controller;

use CuteFlow\CoreBundle\Form\SettingsGeneralType;
use CuteFlow\CoreBundle\Form\SettingsEmailType;
use CuteFlow\CoreBundle\Form\UserFilterType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CuteFlow\CoreBundle\Entity\User;
use CuteFlow\CoreBundle\Form\UserType;



class UserController extends Controller
{
    /**
     * @Route("/admin/user", name="cuteflow_admin_user")
     * @Template()
     *
     * @return array
     */
    public function listAction()
    {
        $em = $settings = $this->getDoctrine()->getEntityManager();

        $settings = $em->find('CuteFlowCoreBundle:Settings', 1);
        $users = $em->getRepository('CuteFlowCoreBundle:User')->findAll();

        $filterForm = $this->createForm(new UserFilterType());

        return array('filterForm'=>$filterForm, 'users'=>$users);
    }

    /**
     * @Route("/admin/edit/user/{id}", name="cuteflow_admin_user_edit")
     * @Template()
     *
     * @param  $id
     * @return array
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $user = $em->find('CuteFlowCoreBundle:User', $id);

        if (!$user) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }

        $userForm = $this->createForm(new UserType(), $user);
        return array('form'=>$userForm->createView(),
                     'user'=>$user);
    }

    /**
     * @Route("/admin/create/user", name="cuteflow_admin_user_create")
     * @Template("CuteFlowCoreBundle:User:edit.html.twig")
     *
     * @return array
     */
    public function createAction()
    {
        $user = new User();
        $userForm = $this->createForm(new UserType(), $user);
        return array('form'=>$userForm->createView(),
                     'user'=>$user);
    }

    /**
     * @Route("/admin/save/user/{id}", name="cuteflow_admin_user_save")
     * @Template("CuteFlowCoreBundle:User:edit.html.twig")
     */
    public function saveAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        if ($id == -1) {
            $user = new User();
        }
        else {
            $user = $em->find('CuteFlowCoreBundle:User', $id);
        }

        if (!$user) {
            throw $this->createNotFoundException('Unable to find user.');
        }

        $userForm = $this->createForm(new UserType(), $user);
        $userForm->bindRequest($this->getRequest());

        if ($userForm->isValid()) {
            $em->persist($user);
            $em->flush();

            $this->getRequest()->getSession()->setFlash('saved.successful', 1);
            return new \Symfony\Component\HttpFoundation\RedirectResponse(
                $this->generateUrl('cuteflow_admin_user')
            );
        }

        return array('form'=>$userForm->createView(),
                     'user'=>$user);
    }

    /**
     * @Route("/admin/delete/user/{id}", name="cuteflow_admin_user_delete")
     *
     * @param  $id
     * @return array
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $user = $em->find('CuteFlowCoreBundle:User', $id);

        if (!$user) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }

        $em->remove($user);
        $em->flush();

        $this->getRequest()->getSession()->setFlash('deleted.successful', 1);
        return new \Symfony\Component\HttpFoundation\RedirectResponse(
            $this->generateUrl('cuteflow_admin_user')
        );
    }
}