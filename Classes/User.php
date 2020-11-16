<?php

session_start();
require_once "Conexao.php";

class User
{
    public $name;
    public $genre;
    public $email;
    public $address;
    public $phone;
    public $password;

    public function VerifyUserToCreate() {
        try {
            if (isset($_POST["nome"]) && !empty($_POST["nome"]) && isset($_POST["sexo"]) && !empty($_POST["sexo"]) &&
                isset($_POST["email"]) && !empty($_POST["email"]) && isset($_POST["endereco"]) && !empty($_POST["endereco"]) &&
                isset($_POST["telefone"]) && !empty($_POST["telefone"]) && isset($_POST["senha"]) && !empty($_POST["senha"])) {

                $this->email = $_POST["email"];

                $bd = new Conexao();
                $con = $bd->conectar();
                $sql = $con->prepare("select 1 from User where email = ?");
                $sql->execute(array(
                    $this->email
                ));
                if($sql->rowCount() > 0) {
                    echo "<script> alert('Já existe um usuário utilizando este email em nosso sistema.'); </script>";
                } else {
                    $this->CreateUser();
                }

            }
        } catch (PDOException $msg) {
            echo "<script> alert('Não foi possível efetuar a verificação dos seus dados para criar sua conta. \n {$msg->getMessage()}'); </script>";
        }
    }

    public function CreateUser() {
        try {
            if(isset($_POST["nome"]) && !empty($_POST["nome"]) && isset($_POST["sexo"]) && !empty($_POST["sexo"]) &&
                isset($_POST["email"]) && !empty($_POST["email"]) && isset($_POST["endereco"]) && !empty($_POST["endereco"]) &&
                isset($_POST["telefone"]) && !empty($_POST["telefone"]) && isset($_POST["senha"]) && !empty($_POST["senha"])) {

                $this->name = $_POST["nome"];
                $this->genre = $_POST["sexo"];
                $this->email = $_POST["email"];
                $this->address = $_POST["endereco"];
                $this->phone = $_POST["telefone"];
                $this->password = $_POST["senha"];

                $bd = new Conexao();
                $con = $bd->conectar();
                $sql = $con->prepare("insert into User(id,name,genre,email,address,phone,password) values(null,?,?,?,?,?,?)");

                $sql->execute(array(
                    $this->name,
                    $this->genre,
                    $this->email,
                    $this->address,
                    $this->phone,
                    $this->password,
                ));

                if($sql->rowCount() > 0) {
                    $this->Authenticator();
                } else {
                    header("location: RegisterAccount.php");
                }
            }
        } catch (PDOException $msg) {
            echo "<script> alert('Não foi possível criar o usuário: {$msg->getMessage()}'); </script>";
        }
    }

    public function ListingUserData($userToken) {
        try {
            $bd = new Conexao();
            $con = $bd->conectar();
            $sql = $con->prepare("select * from User where id = ?");
            $sql->execute(array(
                $userToken
            ));
            if($sql->rowCount() > 0) {
                return $result = $sql->fetchAll(PDO::FETCH_CLASS);
            }

        } catch (PDOException $msg) {
            echo "<script> alert('Não foi possível listar o seus dados: {$msg->getMessage()}'); </script>";
        }
    }

    public function Authenticator() {
        try {
            if(isset($_POST["email"]) && !empty($_POST["email"]) && isset($_POST["senha"]) && !empty("senha")) {
                $this->email = $_POST["email"];
                $this->password = $_POST["senha"];

                $bd = new Conexao();
                $con = $bd->conectar();
                $sql = $con->prepare("select id from User where email = ? and password = ?");
                $sql->execute(array(
                    $this->email,
                    $this->password
                ));
                if($sql->rowCount() > 0) {
                    $result = $sql->fetchAll(PDO::FETCH_CLASS);
                    $_SESSION["userToken"] = $result;
                    if(isset($_POST["createUser"])) {
                        header("location: ../Transaction/TransactionPage.php");
                    } elseif (isset($_POST["logUser"])) {
                        header("location: Screens/Transaction/TransactionPage.php");
                    } else {
                        echo "<script> alert('Sua requisição não foi autorizada pelo nosso servidor. Entre em contato com o suporte'); </script>";
                    }
                } else {
                    echo '<script type="text/javascript">alert("Email ou senha incorretos");</script>';
                }
            }

        } catch (PDOException $msg) {
            echo "<script> alert('Não foi possível realizar sua autenticação: {$msg->getMessage()}');</script>";
        }
    }
}

?>