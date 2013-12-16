<?php
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
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
        $form = new UserForm();
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
                // save the data of the new user
                $model = new UserModel();
                $id = $model->insert($form->getData());
                return $this->redirect()->toRoute('user/default', array (
                		'controller' => 'account',
                		'action' => 'view',
                		'id' => $id
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
        $result = $this->forward()->dispatch('User\Controller\Account', array('action' => 'add',));
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
        return array();
    }
    
    public function meAction()
    {   
        
    	return array(
    		
    	);
    }
    
    public function deniedAction()
    {
    	return array();
    }
    
}