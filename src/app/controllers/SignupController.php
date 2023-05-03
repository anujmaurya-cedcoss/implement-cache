<?php
use Phalcon\Mvc\Controller;
use Firebase\JWT\JWT;

class SignupController extends Controller
{
    public function indexAction()
    {
        // redirected to view
        
    }
    public function signupAction() {
        $name = $this->escaper->escapeHtml($this->request->getPost('name'));
        $email = $this->escaper->escapeHtml($this->request->getPost('email'));
        $password = $this->escaper->escapeHtml($this->request->getPost('password'));
        $role = $this->escaper->escapeHtml($this->request->getPost('role'));
        if ($name != '' && $email != '' && $password != '' && $role != '') {
            $now = new DateTimeImmutable();
            $issued = $now->getTimestamp();
            $notBefore = $now->modify('-1 minute')->getTimestamp();
            $key = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';

            $payload = [
                'iat' => $issued,
                'nbf' => $notBefore,
                'role' => $role,
            ];
            $jwt = JWT::encode($payload, $key, 'HS256');
            $this->session->currUser = $jwt;
            $this->response->redirect('/product/show?bearer=' . $this->session->currUser);
        }
    }
}
