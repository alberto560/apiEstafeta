<?php
	include_once("bd.php");
	include_once("apiEstafeta.php");
/**
 *
 */
	class estafetaApi
	{
		private $model;
		function __construct()
		{
				$this->model = new estafeta();
		}
		
		public function getDataOrder($parametros){			
			$consulta = $this->model->getDataOrder($parametros);
			echo $consulta;
		}

		public function apiEstafeta($parametros){			
			$consulta = $this->model->apiEstafeta($parametros);
			echo $consulta;
		}
		
		public function getDataProducto($parametros){			
			$consulta = $this->model->getDataProducto($parametros);
			echo $consulta;
		}
		
		public function sendGuiaMail($parametros){			
			$consulta = $this->model->sendGuiaMail($parametros);
			echo $consulta;
		}
		
		public function rastrearGuia($parametros){			
			$consulta = $this->model->rastrearGuia($parametros);
			echo $consulta;
		}
		//==========================================================
		//==========================================================
		public function saveDataGuia($parametros){			
			$consulta = $this->model->saveDataGuia($parametros);
			echo $consulta;
		}
		
		public function getDataGuias($parametros){			
			$consulta = $this->model->getDataGuias($parametros);
			echo $consulta;
		}
		
	}

	$apiEsta= new estafetaApi();

	if(!isset($_POST['action'])) {
		print json_encode(0);
		return;
	}

	switch($_POST['action']) {
		case 'getDataOrder':
			$parametros = new stdClass;
			$parametros = json_decode($_POST['parametros']);
			$apiEsta->getDataOrder($parametros);
		break;

		case 'apiEstafeta':
			$parametros = new stdClass;
			$parametros = json_decode($_POST['parametros']);
			$apiEsta->apiEstafeta($parametros);
		break;
		
		case 'getDataProducto':
			$parametros = new stdClass;
			$parametros = json_decode($_POST['parametros']);
			$apiEsta->getDataProducto($parametros);
		break;
		
		//**************************************************************
		//**************************************************************
		case 'sendGuiaMail':
			$parametros = new stdClass;
			$parametros = json_decode($_POST['parametros']);
			$apiEsta->sendGuiaMail($parametros);
		break;
		
		case 'rastrearGuia':
			$parametros = new stdClass;
			$parametros = json_decode($_POST['parametros']);
			$apiEsta->rastrearGuia($parametros);
		break;
		//**************************************************************
		//**************************************************************
		case 'saveDataGuia':
			$parametros = new stdClass;
			$parametros = json_decode($_POST['parametros']);
			$apiEsta->saveDataGuia($parametros);
		break;
		
		case 'getDataGuias':
			$parametros = new stdClass;
			$parametros = json_decode($_POST['parametros']);
			$apiEsta->getDataGuias($parametros);
		break;
		
		
		
	}
?>
