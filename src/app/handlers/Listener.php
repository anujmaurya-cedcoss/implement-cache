<?php
namespace handler\Listener;

use Phalcon\Acl\Adapter\Memory;
use Phalcon\Mvc\Application;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Di\Injectable;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Listener extends injectable
{
    public function beforeProductAdd()
    {
        $settings = $this->db->fetchAll("SELECT * FROM settings", \Phalcon\Db\Enum::FETCH_ASSOC);
        if ($settings[0]['title'] == 'with-tag') {
            // change name to name+tag
            $_POST['name'] = $this->request->getPost('name') . '-' . $this->request->getPost('tags');
        }
        if ($this->request->getPost('price') == 0 || !isset($_POST['price'])) {
            $_POST['price'] = $settings[0]['price'];
        }
        if ($this->request->getPost('stock') == 0 || !isset($_POST['stock'])) {
            $_POST['stock'] = $settings[0]['stock'];
        }
    }

    public function beforeOrderAdd()
    {
        $settings = $this->db->fetchAll("SELECT * FROM settings", \Phalcon\Db\Enum::FETCH_ASSOC);
        if ($_POST['zip'] == '') {
            $_POST['zip'] = $settings[0]['zip'];
        }
    }

    public function beforeHandleRequest(Event $event, Application $app, Dispatcher $dis)
    {
        $acl = new Memory();
        /*
         * Add the roles
         */
        if (!isset($this->session->roles)) {
            $this->session->roles = [];
        }
        foreach ($this->session->roles as $value) {
            $acl->addRole($value);
        }
        $acl->addRole('admin');
        $acl->addRole('manager');
        $acl->addRole('user');
        /*
         * Add the Components
         */
        $actions = [];
        if (!isset($this->session->controllerList)) {
            $this->session->controllerList = [];
        }
        foreach ($this->session->controllerList as $controller => $controllerList) {
            foreach ($controllerList as $key => $action) {
                array_push($actions, $action);
            }
            $acl->addComponent(
                $controller,
                array_values($actions)
            );
            $actions = [];
        }

        $acl->addComponent('aclpage', ['index', 'acl',]);
        $acl->addComponent('addcomponent', ['index', 'add',]);
        $acl->addComponent('index', ['index',]);
        $acl->addComponent('order', ['index', 'add', 'show',]);
        $acl->addComponent('product', ['add', 'index', 'show']);
        $acl->addComponent('role', ['index', 'add',]);
        $acl->addComponent('setting', ['index', 'add',]);
        $acl->addComponent('signup', ['index', 'signup']);

        // access list
        if (!isset($this->session->accessList)) {
            $this->session->accessList = [];
        }
        foreach ($this->session->accessList as $key => $value) {
            if (!is_null($value['role']) && !is_null($value['controller']) && !is_null($value['action']))
                $acl->allow($value['role'], $value['controller'], $value['action']);
        }

        $acl->allow('admin', '*', '*');
        $acl->allow('manager', 'product', '*');
        $acl->allow('manager', 'order', '*');
        $acl->allow('user', 'index', 'index');
        $acl->allow('user', 'product', 'show');
        $acl->allow('*', 'signup', '*');
        $controller = "index";
        $action = "index";
        if (!empty($dis->getControllerName())) {
            $controller = $dis->getControllerName();
        }
        if (!empty($dis->getActionName())) {
            $action = $dis->getActionName();
        }
        $tokenReceived = $this->request->get('bearer');
        if ($tokenReceived != '') {
            $key = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';
            $decoded = JWT::decode($tokenReceived, new Key($key, 'HS256'));
            if (true === $acl->isAllowed($decoded->role, $controller, $action)) {
                if (file_exists(APP_PATH . "/controllers/$controller/")) {
                    $_SESSION['currUser'] = $tokenReceived;
                    $this->response->redirect($controller / $action . '?bearer=' . $tokenReceived);
                } else {
                    echo $this->locale->_('Access Granted').':)';
                }
            } else {
                echo $this->locale->_('Access denied'). ':(';
                die;
            }
        }
    }
}
