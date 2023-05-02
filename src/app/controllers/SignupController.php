<?php

use Phalcon\Mvc\Controller;
use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;


class SignupController extends Controller
{

    public function IndexAction()
    {
        // defalut action
    }

    public function registerAction()
    {
        $user = new Users();

        $data = array(
            "name" => $this->escaper->escapeHtml($this->request->getPost("name")),
            "email" => $this->escaper->escapeHtml($this->request->getPost("email")),
            "password" => $this->escaper->escapeHtml($this->request->getPost("password")),
            'role' => $this->request->getPost('role')
        );


        $user->assign(
            $data,
            [
                'name',
                'email',
                'password',
                'role',

            ]
        );
        $success = $user->save();

        $signer  = new Hmac();
        $builder = new Builder($signer);
        

        $now        = new DateTimeImmutable();
        $issued     = $now->getTimestamp();
        $notBefore  = $now->modify('-1 minute')->getTimestamp();
        $expires    = $now->modify('+1 day')->getTimestamp();
        $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';

        $builder
            ->setContentType('application/json')
            ->setExpirationTime($expires)
            ->setId('abcd123456789')
            ->setIssuedAt($issued)
            ->setNotBefore($notBefore)
            ->setSubject($data['role'])
            ->setPassphrase($passphrase);
        $tokenObject = $builder->getToken();
       $this->session->set('userId', $tokenObject->getToken());
       $this->response->redirect('/product/index?bearer='.$this->session->get('userId'));
        $this->view->success = $success;
        if ($success) {
            $this->view->message = "Register succesfully";
        } else {
            $this->view->message = "Not Register due to following reason: <br>" . implode("<br>", $user->getMessages());
        }
    }
}
