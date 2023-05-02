<?php

use Phalcon\Mvc\Controller;

class SettingController extends Controller
{
    public function indexAction()
    {
        // default login view
    }
    public function addAction()
    {
        $setting = new Settings();
        $product = $this->db->fetchAll("SELECT * FROM settings WHERE `id`=1", \Phalcon\Db\Enum::FETCH_ASSOC);
        $data = array(
            "title" => $this->escaper->escapeHtml($this->request->getPost("title")),
            "price" => $this->escaper->escapeHtml($this->request->getPost("price")),
            "stock" => $this->escaper->escapeHtml($this->request->getPost("stock")),
            "zipcode" => $this->escaper->escapeHtml($this->request->getPost("zipcode"))
        );
        if (isset($product[0])) {
            $conn = $this->container->get('db');
            $conn->query(
                "update `settings` set `title`='$data[title]',
            `price`='$data[price]',
            `stock`='$data[stock]',
            `zipcode`='$data[zipcode]' where `id`='1'"
            );
        }

        if ($conn) {
            $this->view->message = "Applied succesfully";
        } else {
            $this->view->message = "Not applied : ";
        }
    }
}
