<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Entities\Medida;

class Medidas extends BaseController
{

    private $medidaModel;

    public function __construct()
    {
        $this->medidaModel = new \App\Models\MedidaModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando as medidas de produtos',
            'medidas' => $this->medidaModel->withDeleted(true)->paginate(10),
            'pager' => $this->medidaModel->pager,
        ];

        return view('Admin/medidas/index', $data);
    }


    /**
     * Controlador do campo busca na página principal
     *
     * @return void
     */
    public function procurar()
    {
        if (!$this->request->isAJAX()) {
            exit('Pagina não encontrada');
        }

        $medidas = $this->medidaModel->procurar($this->request->getGet('term'));
        $retorno = [];
        foreach ($medidas as $medida) {
            $data['id'] = $medida->id;
            $data['value'] = $medida->nome;

            $retorno[] = $data;
        }

        return $this->response->setJSON($retorno);
    }

    /**
     * Controlador para visualizar dados do medida
     *
     * @param [type] $id
     * @return void
     */
    public function show($id = null)
    {
        $medida = $this->buscaMedidaOu404($id);
        $data = [
            'titulo' => "Detalhando a medida $medida->nome",
            'medida' => $medida,
        ];
        return view('Admin/medidas/show', $data);
    }


    /**
     * Controlador para mostrar form vazio para um novo cadastro
     *
     * @param [type] $id
     * @return void
     */
    public function criar($id = null)
    {
        $medida = new Medida();
        $data = [
            'titulo' => "Criando uma nova medida",
            'medida' => $medida,
        ];
        return view('Admin/medidas/criar', $data);
    }


    public function editar($id = null)
    {
        $medida = $this->buscaMedidaOu404($id);

        if ($medida->deletado_em != null) {
            return redirect()->back()->with('info', "A medida $medida->nome encontra-se excluído. Portanto não é possível edita-la");
        }

        $data = [
            'titulo' => "Editando a medida $medida->nome",
            'medida' => $medida,
        ];
        return view('Admin/medidas/editar', $data);
    }


    /**
     * Controlador para Atualizar os dados no banco de dados
     *
     * @param [type] $id
     * @return void
     */
    public function atualizar($id = null)
    {
        if ($this->request->getMethod(true) === 'POST') {

            $medida = $this->buscaMedidaOu404($id);

            if ($medida->deletado_em != null) {
                return redirect()->back()->with('info', "A medida $medida->nome encontra-se excluído. Portanto não é possível edita-la");
            }

            $medida->fill($this->request->getPost());

            if (!$medida->hasChanged()) {
                return redirect()->back()->with('info', 'Não há dados para atualizar');
            }

            if ($this->medidaModel->save($medida)) {
                return redirect()
                    ->to(site_url("admin/medidas/show/$medida->id"))
                    ->with('sucesso', "medida $medida->nome atualizada com sucesso.");
            } else {
                return redirect()->back()
                    ->with('errors_model', $this->medidaModel->errors())
                    ->with('atencao', 'Por favor verifique os erros abaixo.')
                    ->withInput();
            }
        } else {
            return redirect()->back();
        }
    }


    /**
     * Controlador para excluir o usuário selecionado
     *
     * @param [type] $id
     * @return void
     */
    public function excluir($id = null)
    {
        $medida = $this->buscaMedidaOu404($id);

        if ($medida->deletado_em != null) {
            return redirect()->back()->with('info', "A medida $medida->nome encontra-se excluída.");
        }

        if ($this->request->getMethod() === 'POST') {
            $this->medidaModel->delete($id);
            return redirect()->to(site_url('admin/medidas'))->with('sucesso', "medida <strong>$medida->nome</strong> excluida com sucesso");
        }

        $data = [
            'titulo' => "Excluindo a medida $medida->nome",
            'medida' => $medida,
        ];
        return view('Admin/medidas/excluir', $data);
    }


    /**
     * Controlador para cadastrar um novo usuário
     *
     * @return void
     */
    public function cadastrar()
    {
        if ($this->request->getMethod(true) === 'POST') {

            $medida = new medida($this->request->getPost());

            if ($this->medidaModel->save($medida)) {
                return redirect()
                    ->to(site_url("admin/medidas/show/" . $this->medidaModel->getInsertID()))
                    ->with('sucesso', "medida $medida->nome cadastrada com sucesso.");
            } else {
                return redirect()->back()
                    ->with('errors_model', $this->medidaModel->errors())
                    ->with('atencao', 'Por favor verifique os erros abaixo.')
                    ->withInput();
            }
        } else {
            return redirect()->back();
        }
    }



    /**
     * Controlador para desfazer a exclusão do usuário
     *
     * @param [type] $id
     * @return void
     */
    public function desfazerExclusao($id = null)
    {
        $medida = $this->buscaMedidaOu404($id);

        if ($medida->deletado_em == null) {
            return redirect()->back()->with('info', 'Apenas medidas excluidas podem ser recuperados');
        }

        if ($this->medidaModel->desfazerExclusao($id)) {
            return redirect()->back()->with('sucesso', 'Exclusão desfeita com sucesso');
        } else {
            return redirect()->back()
                ->with('errors_model', $this->medidaModel->errors())
                ->with('atencao', 'Por favor verifique os erros abaixo.')
                ->withInput();
        }
    }

    private function buscaMedidaOu404(int|null $id)
    {
        if (!$id || !$medida = $this->medidaModel->withDeleted(true)->where('id', $id)->first()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos a medida $id");
        }

        return $medida;
    }
}
