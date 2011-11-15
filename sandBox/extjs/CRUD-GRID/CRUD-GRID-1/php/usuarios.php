<?php
/*
 * Devemos requisitar aqui nossa classe base para que possamos criar nossa classe de usu�rios,
 * usamos require_once se esta classe n�o for encontrada o resto do c�digo n�o pode ser executado
 * e o (_once) garante que ela ser� carregada apenas uma vez
 */
require_once 'baseClass.php';

/*
 * Aqui estendemos a classe base para uma classe usu�rio, � recomend�vel ter uma classe por tabela
 * Note que em momento algum usamos de fun��es proprias de algum banco de dados, isso � tratado
 * na classe pai
 */
class usuario extends base {
	//A vari�vel $actions deve conter os m�todos que podem ser executados pelo m�todo execAction da
	//classe pai, � uma lista de strings com o mesmo nome dos m�todos
	protected $actions = array(
		'select'
	);
	
	//Este m�todo est� na lista de a��es e poder� ser executado
	public function select(){
		//Definimos um sql para buscar os dados
		$sql = "SELECT * FROM usuarios";
		//Usamos de nossa fun��o para buscar os dados do banco e nos retornar um array pronto
		$result = $this->_select_fetch_all($sql);
		
		//Aqui apenas montamos nosso JSON da forma que quisermos
		echo json_encode(array(
			"data" => $result
		));
	}
}

/*
 * Aqui temos um detalho, podemos fazer de duas formas.
 * 1 - Criar a classe e juntamente a classe passar a a��o a ser executadam, lembre que dentro
 *     do constructor da classe pai temos um teste para isso. Quest�o apenas de facilitar.
 * 2 - Criar a classe e chamar a fun��o que executa a a��o que for passada
 */
$usuario = new usuario($_POST['_action']);
//Poderia ser executado como abaixo
//$usuario = new usuario();
//$usuario->execAction('select');
?>