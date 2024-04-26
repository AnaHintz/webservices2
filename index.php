
<?php //index.php
 
require_once("./model/Usuario.php");
require_once("./model/Tarefa.php");
require_once("./database/MariaDB.php");
 
function dd($valor)
{
    echo "<pre>";
    print_r($valor);
    echo "</pre>";
}
 
$metodo = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['PATH_INFO'];
$rota = explode('/', $path);
 
$status_code = 200;
$resposta = [
    "status" => true,
    "mensagem" => "",
];
 
if ($rota[1] == "usuarios") {
    $database = new MariaDb();
    $usuario = new Usuario($database->dbConnection());
    switch ($metodo) {
        case "GET":
            if(isset($rota[2]) && is_numeric($rota[2])){
                $result = $usuario->getUserById($rota[2]);
                if(count($result) == 0){
                    $status_code = 404;
                    $resposta['status'] = false;
                    $resposta['mensagem'] = "Usuário não encontrado";
                    break;
                }


                $resposta['dados'] = $result;
            }else{
                $resposta['dados'] = $usuario->getAll() ;
            }
            break;
        case "DELETE":
            if(isset($rota[2]) && is_numeric($rota[2])){
                $usuario->id = $rota[2];
               $result =  $usuario->remove($rota[2]);
 
               if($result === false){
                    $status_code = 403;
                    $resposta['status'] = false;
                    $resposta['mensagem'] = "Erro ao tentar remover o usuário";
                    break;
                }
                $resposta['mensagem'] = "Usuário removido com sucesso!";
                break;
            }else{
                $status_code = 403;
                $resposta['status'] = false;
                $resposta['mensagem'] = "Usuário não foi informado";
            }
            break;
            case "POST":
                $parametros = file_get_contents('php://input');
                $parametros = (array) json_decode($parametros, true);
                $usuario->nome = $parametros['nome'];
                $usuario->login = $parametros['login'];
                $usuario->senha = $parametros['senha'];
 
                if(!$usuario->create()) {
                    $status_code = 403;
                    $resposta['status'] = false;
                    $resposta['mensagem'] = "Erro ao tentar cadastrar o usuário";
                    break;
                }
                $resposta['mensagem'] = "Usuário cadastrado com sucesso!";
                break;
            case "PUT":
                $parametros = file_get_contents('php://input');
                $parametros = (array) json_decode($parametros, true);
                $usuario->id = $rota[2];
                $usuario->nome = $parametros['nome'];
                $usuario->login = $parametros['login'];
                $usuario->senha = $parametros['senha'];
 
                if(!$usuario->update()) {
                    $status_code = 403;
                    $resposta['status'] = false;
                    $resposta['mensagem'] = "Erro ao tentar atualizar o usuário";
                    break;
                }
                $resposta['mensagem'] = "Usuário atualizado com sucesso!";
                break;
        default:
            $status_code = 403;
            $resposta['status'] = false;
            $resposta['mensagem'] = "Método não permitido";
        }
}

else if ($rota[1] == "tarefas") {
    //////////////////rotas tarefas
    $database = new MariaDb();
    $tarefa = new Tarefa($database->dbConnection());
    switch ($metodo) {
        case "GET":
            if(isset($rota[2]) && is_numeric($rota[2])){
                $result = $tarefa->getTarefaById($rota[2]);
                if(count($result) == 0){
                    $status_code = 404;
                    $resposta['status'] = false;
                    $resposta['mensagem'] = "Tarefa não encontrada";
                    break;
                }


                $resposta['dados'] = $result;
            }else{
                $resposta['dados'] = $tarefa->getAll() ;
            }
            break;
        case "DELETE":
            if(isset($rota[2]) && is_numeric($rota[2])){
                $tarefa->id_tarefa = $rota[2];
               $result = $tarefa->remove($rota[2]);
 
               if($result === false){
                    $status_code = 403;
                    $resposta['status'] = false;
                    $resposta['mensagem'] = "Erro ao tentar remover a tarefa";
                    break;
                }
                $resposta['mensagem'] = "Tarefa removida com sucesso!";
                break;
            }else{
                $status_code = 403;
                $resposta['status'] = false;
                $resposta['mensagem'] = "Tarefa não foi informada";
            }
            break;
            case "POST":
                $parametros = file_get_contents('php://input');
                $parametros = (array) json_decode($parametros, true);
                $tarefa->titulo = $parametros['titulo'];
                $tarefa->descricao = $parametros['descricao'];
                $tarefa->id_usuario = $parametros['id_usuario'];
            
             
 
                if(!$tarefa->create()) {
                    $status_code = 403;
                    $resposta['status'] = false;
                    $resposta['mensagem'] = "Erro ao tentar criar a tarefa";
                    break;
                }
                $resposta['mensagem'] = "Tarefa criada com sucesso!";
                break;
            case "PUT":
                $parametros = file_get_contents('php://input');
                $parametros = (array) json_decode($parametros, true);
                $tarefa->id_tarefa = $rota[2];
                $tarefa->titulo = $parametros['titulo'];
                $tarefa->descricao = $parametros['descricao'];
                $tarefa->id_usuario = $parametros['id_usuario'];
               
 
                if(!$tarefa->update()) {
                    $status_code = 403;
                    $resposta['status'] = false;
                    $resposta['mensagem'] = "Erro ao tentar atualizar a tarefa";
                    break;
                }
                $resposta['mensagem'] = "Tarefa atualizada com sucesso!";
                break;
        default:
            $status_code = 403;
            $resposta['status'] = false;
            $resposta['mensagem'] = "Método não permitido";
        }
}

else{
    $status_code = 403;
    $resposta['status'] = false;
    $resposta['mensagem'] = "Não foi possível entender sua requisição!";
}


http_response_code($status_code);
header("Content-Type: application/json");
echo json_encode($resposta);
 