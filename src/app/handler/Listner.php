<?php


use Phalcon\Di\Injectable;
use Phalcon\Acl\Adapter\Memory;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Application;
use Phalcon\Security\JWT\Token\Parser;
use Phalcon\Security\JWT\Validator;


class Listner extends Injectable
{
    public function beforeHandleRequestProduct()
    {
        $di = $this->getDI();
        $connection = $di->get('db');
        $product = $this->db->fetchAll("SELECT * FROM settings WHERE `id`=1", \Phalcon\Db\Enum::FETCH_ASSOC);
        if ($product[0]['title'] == "tag1") {
            $_POST['pname'] = $_POST['pname'] . "-" . $_POST['ptags'];
        }
        if ($_POST['pprice'] == "" || $_POST['pprice'] == 0) {
            $_POST['pprice'] = $product[0]['price'];
        }
        if ($_POST['pstock'] == "" || $_POST['pstock'] == 0) {
            $_POST['pstock'] = $product[0]['stock'];
        }
    }
    public function beforeHandleRequestOrder()
    {
        $di = $this->getDI();
        $connection = $di->get('db');
        $product = $this->db->fetchAll("SELECT * FROM settings WHERE `id`=1", \Phalcon\Db\Enum::FETCH_ASSOC);

        if ($_POST['zipcode'] == "") {
            $_POST['zipcode'] = $product[0]['zipcode'];
        }
    }
    public function beforeHandleRequest(Event $event, Application $app, Dispatcher $dispatcher)
    {
        $bearer = $app->request->get("bearer");
        if (!empty($bearer)) {

            $tokenReceived = $bearer;
            $now           = new DateTimeImmutable();
            $issued        = $now->getTimestamp();
            $notBefore     = $now->modify('-1 minute')->getTimestamp();
            $expires       = $now->getTimestamp();
            $id            = 'abcd123456789';

            $parser      = new Parser();
            $tokenObject = $parser->parse($bearer);

            $validator = new Validator($tokenObject, 100);
            $validator
                ->validateExpiration($expires)
                ->validateId($id)
                ->validateIssuedAt($issued)
                ->validateNotBefore($notBefore);



            $subject = $tokenObject->getClaims()->getPayload();
            $role = $subject['sub'];
            $acl = new Memory();


            $acl->addRole($role);
            $acl->addComponent(
                'index',
                [
                    'index',

                ]
            );
            $acl->addComponent(
                'order',
                [
                    'index',
                    'add',
                    'show'
                ]
            );
            $acl->addComponent(
                'Product',
                [
                    'index',
                    'add',
                    'view'

                ]
            );
            $acl->addComponent(
                'setting',
                [
                    'index',
                    'add'

                ]
            );
            $acl->addComponent(
                'Signup',
                [
                    'index',
                    'add'

                ]
            );
            $acl->deny($role, '*', '*');
            $action = "index";
            $controller = "index";

            if (!empty($dispatcher->getActionName())) {
                $action =  $dispatcher->getActionName();
            }
            if (!empty($dispatcher->getControllerName())) {
                $controller =  $dispatcher->getControllerName();
            }

            if (true === $acl->isAllowed($role, $controller, $action)) {
                echo 'Access granted!';
            } else {
                echo 'Access denied :(';
                die;
                // $this->response->redirect('index/index');
            }
        } else {
            echo "Token not found";
            die;
        }
    }
}
