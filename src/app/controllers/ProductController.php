<?php

use Phalcon\Mvc\Controller;
use Phalcon\Events\Manager as EventsManager;
use MyApp\handle\Aware;


class ProductController extends Controller
{
    public function indexAction()
    {
        // default login view
    }
    public function addAction()
    {
        $product = new Products();
        $eventsManager = new EventsManager();
        $component = new Aware();

        $component->setEventsManager($eventsManager);
        $eventsManager->attach(
            'application:beforeHandleRequestProduct',
            new Listner()
        );
        $component->process();
        $data = array(
            "name" => $this->escaper->escapeHtml($this->request->getPost("pname")),
            "description" => $this->escaper->escapeHtml($this->request->getPost("pdescription")),
            "tags" => $this->escaper->escapeHtml($this->request->getPost("ptags")),
            "price" => $this->escaper->escapeHtml($this->request->getPost("pprice")),
            "stock" => $this->escaper->escapeHtml($this->request->getPost("pstock"))
        );
        $product->assign(
            $data,
            [
                'name',
                "description",
                "tags",
                "price",
                "stock"
            ]
        );
        $success = $product->save();
        if ($success) {
            $this->view->message = "Added succesfully";
        } else {
            $this->view->message = "Not added : ";
        }
    }
    public function viewAction()
    {
        $product = $this->db->fetchAll(
            "SELECT * FROM products",
            \Phalcon\Db\Enum::FETCH_ASSOC
        );
        $this->view->message = $product;
    }
}
