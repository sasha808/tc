<?php
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\EventManager\EventManager;
use Zend\Form\Annotation\AnnotationBuilder;
use User\Form\User as UserForm;
use User\Model\User as UserModel;

class AccountController extends AbstractActionController
{

    public function indexAction()
    {
        return array();
    }

    public function addAction()
    {
        $builder = new AnnotationBuilder();
        $entity = $this->serviceLocator->get('user-entity');
        $form = $builder->createForm($entity);
        
        $form->add(array(
            'name' => 'password_verify',
            'type' => 'Zend\Form\Element\Password',
            'attributes' => array(
                'placeholder' => 'Verify Password Here...',
                'required' => 'required'
            ),
            'options' => array(
                'label' => 'Verify Password'
            )
        ), array(
            'priority' => $form->get('password')
                ->getOption('priority')
        ));
        
        // This is the special code that protects our form being submitted from automated scripts
        $form->add(array(
            'name' => 'csrf',
            'type' => 'Zend\Form\Element\Csrf'
        ));
        
        // This is the submit button
        $form->add(array(
            'name' => 'submit',
            'type' => 'Zend\Form\Element\Submit',
            'attributes' => array(
                'value' => 'Submit',
                'required' => 'false'
            )
        ));
        
        // $form = new UserForm();
        $form->bind($entity);
        
        if ($this->getRequest()->isPost()) {
            $data = array_merge_recursive($this->getRequest()
                ->getPost()
                ->toArray(), 
                // Notice: make certain to merge the Files also to the post data
                $this->getRequest()
                    ->getFiles()
                    ->toArray());
            $form->setData($data);
            if ($form->isValid()) {
                $entityManager = $this->serviceLocator->get('entity-manager');
                $entityManager->persist($entity);
                $entityManager->flush();
                
                $this->flashmessenger()->addSuccessMessage('User was added successfully.');
                
                $event = new EventManager('user');
                $event->trigger('register', $this, array(
                    'user' => $entity
                ));
                
                // redirect the user to the view user action
                return $this->redirect()->toRoute('user/default', array(
                    'controller' => 'account',
                    'action' => 'view',
                    'id' => $entity->getId()
                ));
            }
        }
        
        // pass the data to the view for visualization
        return array(
            'form1' => $form
        );
    }
    /*
     * Anonymous users can use this action to register new accounts
     */
    public function registerAction()
    {
        $result = $this->forward()->dispatch('User\Controller\Account', array(
            'action' => 'add'
        ));
        return $result;
    }

    public function viewAction()
    {
        return array();
    }

    public function editAction()
    {
        return array();
    }

    public function deleteAction()
    {
        $id = $this->params('id');
        if ($id) {
            $entityManager = $this->serviceLocator->get('entity-manager');
            $userEntity = $this->serviceLocator->get('user-entity');
            $userEntity->setId($id);
            $entityManager->remove($userEntity);
            $entityManager->flush();
        }
        return array();
    }

    public function meAction()
    {
        return array()

        ;
    }

    public function deniedAction()
    {
        return array();
    }
}