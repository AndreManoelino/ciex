<?php

namespace App\Controllers;

use App\Models\ExpansoesModel;


class Expansoes extends BaseController

{
	protected function __construct()
	{
		$this->expansoesModel = new ExpansoesModel();
	}

	public function index()
	{
		$session = session();
		$tipo = $session->get('tipo');
		$unidade = $session->get('unidade');
		$estado = $session->get('estado');

		$unidadesEstado = $this->getUnidadesPorEstado($estado);
		$unidadeFiltro = $this->request->getGet('unidade');
		$editarId = $this->request->getGet('editar');

		$query = $this->expansoesModel;


		// Filtrando a vizualisação do tipo como supervisor 
		if($tipo === 'supervisor') {
			if($unidadeFiltro) {
				$query->where('unidade', $unidadeFiltro);
			} else {
				$query->whereIn('unidade', $unidadesEstado);
			}
		} else {
			$query->where('unidade', $unidade);
		}

		$espansoes = $query->orderBy('id', 'DESC')->findAll();


		// Criando sistema para verificar se ha campos a serem editados 
		$edicao = null;
		if($editarId) {
			$expansao = $this->expansoesModel->find($editarId);
			if($edicao) {
				if
			}
		}



	}
} 