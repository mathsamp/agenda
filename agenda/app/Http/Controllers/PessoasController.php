<?php

namespace App\Http\Controllers;

use App\Pessoa;
use App\Telefone;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class PessoasController extends Controller
{
    
    private $telefones_controller;
    private $pessoa;

    public function __construct(TelefonesController $telefones_controller)
    {
        $this->telefones_controller = $telefones_controller;
        $this->pessoa = new Pessoa();
    }

    public function index($letra)
    {
        $list_pessoas = Pessoa::indexLetra($letra);
        return view('pessoas.index', [
            'pessoas' => $list_pessoas,
            'criterio' => $letra
        ]);
    }

    public function busca(Request $request)
    {
        $pessoas = Pessoa::busca($request->criterio);
        return view('pessoas.index', [
            'pessoas' => $pessoas,
            'criterio' => $request->criterio
        ]);
    }

    public function novoView()
    {
        return view('pessoas.create');
    }

    public function store(Request $request)
    {
        $validacao = $this->validacao($request->all());

        if ($validacao->fails()){
            return redirect()->back()
                ->withErrors($validacao->errors())
                ->withInput($request->all());
        }

        $pessoa = Pessoa::create($request->all());
        if ($request->ddd && $request->telefone) {
            $telefone = new Telefone();
            $telefone->ddd = $request->ddd;
            $telefone->telefone = $request->telefone;
            $telefone->pessoa_id = $pessoa->id;

            $this->telefones_controller->store($telefone);

            /*Telefone::create([
                'ddd' => $request->ddd,
                'telefone' => $request->telefone,
                'pessoa_id' => $pessoa->id
            ]);*/
        }
        return redirect("/pessoas")->with("message", "Pessoa cadastrada com sucesso!");
    }

    public function editarView($id)
    {
        return view('pessoas.edit', [
            'pessoa' => $this->getPessoa($id)
        ]);
    }

    public function excluirView($id)
    {
        return view('pessoas.delete',[
            'pessoa' => $this->getPessoa($id)
        ]);
    }

    public function destroy($id)
    {
        $this->getPessoa($id)->delete();
        return redirect(url('pessoas'))->with('success', 'Excluido!');
    }

    public function update(Request $request)
    {
        $validacao = $this->validacao($request->all());

        if ($validacao->fails()){
            return redirect()->back()
                ->withErrors($validacao->errors())
                ->withInput($request->all());
        }

        $pessoa = $this->getPessoa($request->id);
        $pessoa->update($request->all());
        return redirect('/pessoas');
    }

    protected function getPessoa($id)
    {
        return $this->pessoa->find($id);
    }

    private function validacao($data)
    {
        if (array_key_exists('ddd', $data) && array_key_exists('telefone', $data)){

            if ($data['ddd'] || $data['telefone']){
                $regras['ddd'] = 'required|size:2';
                $regras['telefone'] = 'required';
            } 
        }   

        $regras['nome'] = 'required|min:3';

        $mensagens = [
            'nome.required' => 'Campo nome é obrigatório',
            'nome.min' => 'Campo nome deve ter ao menos 3 letras',
            'ddd.required' => 'Campo DDD é obrigatório',
            'ddd.size' => 'Campo DDD deve ter 2 digitos',
            'telefone.required' => 'Campo telefone é obrigatório'
        ];

        return Validator::make($data, $regras, $mensagens);
    }
}
