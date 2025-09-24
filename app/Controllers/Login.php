<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\Autenticacao;
use CodeIgniter\HTTP\ResponseInterface;

class Login extends BaseController
{
    public function novo()
    {
        $data = [
            'titulo' => 'Realize o login',
        ];

        return view('Login/novo', $data);
    }

    public function criar(){
        if ($this->request->getMethod() === 'POST') {
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            $autenticacao = service('autenticacao');

            if ($autenticacao->login($email, $password)) {
                $usuario = $autenticacao->pegaUsuarioLogado();
                dd($usuario);
            } else {
                return redirect()->back()->with('atencao', 'Não encontramos suas credenciais de acesso.');
            }
            
        } else {
            return redirect()->back();
        }
    }

    public function logout() {
        service('autenticacao')->logout();
        return redirect()->to(site_url('login'));
    }
}
