<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Entities\Extra;

class Extras extends BaseController
{

    private $extraModel;

    public function __construct()
    {
        $this->extraModel = new \App\Models\ExtraModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando os extras de produtos',
            'extras' => $this->extraModel->withDeleted(true)->paginate(10),
            'pager' => $this->extraModel->pager,
        ];

        return view('Admin/Extras/index', $data);
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

        $extras = $this->extraModel->procurar($this->request->getGet('term'));
        $retorno = [];
        foreach ($extras as $extra) {
            $data['id'] = $extra->id;
            $data['value'] = $extra->nome;

            $retorno[] = $data;
        }

        return $this->response->setJSON($retorno);
    }


    /**
     * Controlador para visualizar dados do extra
     *
     * @param [type] $id
     * @return void
     */
    public function show($id = null)
    {
        $extra = $this->buscaExtraOu404($id);
        $data = [
            'titulo' => "Detalhando a extra $extra->nome",
            'extra' => $extra,
        ];
        return view('Admin/extras/show', $data);
    }


    /**
     * Controlador para mostrar form vazio para um novo cadastro
     *
     * @param [type] $id
     * @return void
     */
    public function criar($id = null)
    {
        $extra = new Extra();
        $data = [
            'titulo' => "Criando uma nova extra",
            'extra' => $extra,
        ];
        return view('Admin/extras/criar', $data);
    }


    public function editar($id = null)
    {
        $extra = $this->buscaExtraOu404($id);

        if ($extra->deletado_em != null) {
            return redirect()->back()->with('info', "A extra $extra->nome encontra-se excluído. Portanto não é possível edita-la");
        }

        $data = [
            'titulo' => "Editando a extra $extra->nome",
            'extra' => $extra,
        ];
        return view('Admin/extras/editar', $data);
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

            $extra = $this->buscaExtraOu404($id);

            if ($extra->deletado_em != null) {
                return redirect()->back()->with('info', "A extra $extra->nome encontra-se excluído. Portanto não é possível edita-la");
            }

            $extra->fill($this->request->getPost());

            if (!$extra->hasChanged()) {
                return redirect()->back()->with('info', 'Não há dados para atualizar');
            }

            if ($this->extraModel->save($extra)) {
                return redirect()
                    ->to(site_url("admin/extras/show/$extra->id"))
                    ->with('sucesso', "extra $extra->nome atualizada com sucesso.");
            } else {
                return redirect()->back()
                    ->with('errors_model', $this->extraModel->errors())
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
        $extra = $this->buscaExtraOu404($id);

        if ($extra->deletado_em != null) {
            return redirect()->back()->with('info', "A extra $extra->nome encontra-se excluída.");
        }

        if ($this->request->getMethod() === 'POST') {
            $this->extraModel->delete($id);
            return redirect()->to(site_url('admin/extras'))->with('sucesso', "extra <strong>$extra->nome</strong> excluida com sucesso");
        }

        $data = [
            'titulo' => "Excluindo a extra $extra->nome",
            'extra' => $extra,
        ];
        return view('Admin/extras/excluir', $data);
    }



    /**
     * Controlador para desfazer a exclusão do usuário
     *
     * @param [type] $id
     * @return void
     */
    public function desfazerExclusao($id = null)
    {
        $extra = $this->buscaextraOu404($id);

        if ($extra->deletado_em == null) {
            return redirect()->back()->with('info', 'Apenas extras excluidas podem ser recuperados');
        }

        if ($this->extraModel->desfazerExclusao($id)) {
            return redirect()->back()->with('sucesso', 'Exclusão desfeita com sucesso');
        } else {
            return redirect()->back()
                ->with('errors_model', $this->extraModel->errors())
                ->with('atencao', 'Por favor verifique os erros abaixo.')
                ->withInput();
        }
    }


    /**
     * Controlador para cadastrar um novo usuário
     *
     * @return void
     */
    public function cadastrar()
    {
        if ($this->request->getMethod(true) === 'POST') {

            $extra = new extra($this->request->getPost());

            if ($this->extraModel->save($extra)) {
                return redirect()
                    ->to(site_url("admin/extras/show/" . $this->extraModel->getInsertID()))
                    ->with('sucesso', "extra $extra->nome cadastrada com sucesso.");
            } else {
                return redirect()->back()
                    ->with('errors_model', $this->extraModel->errors())
                    ->with('atencao', 'Por favor verifique os erros abaixo.')
                    ->withInput();
            }
        } else {
            return redirect()->back();
        }
    }


    private function buscaExtraOu404(int|null $id)
    {
        if (!$id || !$extra = $this->extraModel->withDeleted(true)->where('id', $id)->first()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o extra $id");
        }

        return $extra;
    }
}
