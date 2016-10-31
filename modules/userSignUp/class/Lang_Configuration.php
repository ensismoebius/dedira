<?php

namespace userSignUp;

final class Lang_Configuration {
	public static function getDescriptions($descriptionId) {
		$languages = array ();
		
		// Português Brasil
		$languages ["pt-br"] = array (
				0 => "Cadastro de usuário",
				1 => "Usuário ativo",
				2 => "Login",
				3 => "Senha",
				4 => "Nome",
				5 => "Último nome",
				6 => "Sexo",
				7 => "Data de nascimento",
				8 => "Email",
				9 => "Telefone",
				10 => "Enviar",
				11 => "Ambos",
				12 => "Fêmea",
				13 => "Irrelevante",
				14 => "Macho",
				15 => "Todos os campos marcados com * são obrigatórios",
				16 => "Usuário gravado com sucesso",
				17 => "Falha no cadastro"
		);
		
		// English United States
		$languages ["en-us"] = array (
				0 => "User sign up",
				1 => "Active user",
				2 => "Login",
				3 => "Password",
				4 => "Name",
				5 => "Last name",
				6 => "Sex",
				7 => "Birthdate",
				8 => "Email",
				9 => "Telephone",
				10 => "Send",
				11 => "Both",
				12 => "Female",
				13 => "Irrelevant",
				14 => "Male",
				15 => "All fields marked with * are mandatory",
				16 => "User created!",
				17 => "Fail to create a new user!"
		);
		
		return $languages [UserSignUpConf::getSelectedLanguage ()] [$descriptionId];
	}
}
?>