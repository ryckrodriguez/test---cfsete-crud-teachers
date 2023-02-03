<?php
    setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
    date_default_timezone_set('America/Sao_Paulo');

    class Upload
    {

        private $inputFileName = "";
        private $nomeDoUsuario = "";
        private $destinyDirectory = "";
        private $extensoesDeArquivosAceitos;
        public function __construct(
            $inputFileName,
            $nomeDoUsuario,
            $destinyDirectory,
            $arrExtensoesDeArquivosAceitos = array()
        ) {
            $this->inputFileName = $inputFileName;
            $this->nomeDoUsuario = trim(preg_replace('/(\'|\")/', "", strip_tags($nomeDoUsuario)));
            $this->destinyDirectory = $destinyDirectory;
            $this->extensoesDeArquivosAceitos = $arrExtensoesDeArquivosAceitos;
        }

        public function controller()
        {
            if (!empty($_FILES[$this->inputFileName]['name'])) {
                if ( is_array($_FILES[$this->inputFileName]['name']) ) {
                    return $this->handleMultipleUploads();
                } else if ($_FILES[$this->inputFileName]['error'] == 0) {
                    return $this->handleUpload();
                }
            }

            return [new UploadControl()];
        }

        private function handleUpload()
        {
            $arrFileUploaded = new UploadControl();
            $nomeDoArquivo = $_FILES[$this->inputFileName]["name"];
            $arrayAuxiliarTemporario = explode(".", $nomeDoArquivo);
            $arrFileUploaded->setNameTemp($_FILES[$this->inputFileName]["tmp_name"]);

            $arrFileUploaded->setFileExtension(strtolower(end($arrayAuxiliarTemporario)));

            unset($arrayAuxiliarTemporario);

            if (!empty($this->extensoesDeArquivosAceitos)) {
                if (!in_array($arrFileUploaded->getFileExtension(), $this->extensoesDeArquivosAceitos)) {
                    $arrFileUploaded->setError(true);
                    $arrFileUploaded->setMessage("Erro: O tipo do arquivo {$nomeDoArquivo} não é válido.");
                    return $arrFileUploaded;
                }
            }

            $dataDeHoje = date("YmdHis");
            $arrFileUploaded->setName("{$this->nomeDoUsuario}_{$dataDeHoje}.{$arrFileUploaded->getFileExtension()}");

            $arrFileUploaded->setDiretorio($this->destinyDirectory . "/" . $arrFileUploaded->getName());

            if (move_uploaded_file($arrFileUploaded->getNameTemp(), $arrFileUploaded->getDiretorio())) {
                $arrFileUploaded->setError(false);
                $arrFileUploaded->setMessage("Sucesso: O arquivo {$arrFileUploaded->getName()} salvo com sucesso.");
            } else {
                $arrFileUploaded->setError(true);
                $arrFileUploaded->setMessage("Erro: O arquivo {$arrFileUploaded->getName()} não pôde ser salvo.");
            }

            return $arrFileUploaded;
        }

        private function handleMultipleUploads()
        {
            $filesUploaded = [];
            $quantidadeDeArquivosUpados = sizeof($_FILES[$this->inputFileName]["name"]);

            for ($i = 0; $i < $quantidadeDeArquivosUpados; $i++) {

                $arrFileUploaded = new UploadControl();

                $nomeDoArquivo = $_FILES[$this->inputFileName]["name"][$i];

                if(empty($nomeDoArquivo)) {
                    $filesUploaded[] = $arrFileUploaded;
                    continue;
                }

                $arrFileUploaded->setNameTemp($_FILES[$this->inputFileName]["tmp_name"][$i]);

                $arrayAuxiliarTemporario = explode(".", $nomeDoArquivo);
                $arrFileUploaded->setFileExtension(end($arrayAuxiliarTemporario));
                unset($arrayAuxiliarTemporario);

                if (!empty($this->extensoesDeArquivosAceitos)) {
                    if (!in_array($arrFileUploaded->getFileExtension(), $this->extensoesDeArquivosAceitos)) {
                        $arrFileUploaded->setError(true);
                        $arrFileUploaded->setMessage("Erro: O tipo do arquivo {$nomeDoArquivo} não é válido.");
                        $filesUploaded[] = $arrFileUploaded;
                        continue;
                    }
                }

                $dataDeHoje = date("YmdHis");
                $arrFileUploaded->setName($this->nomeDoUsuario . "_" . $dataDeHoje . "_" . $i . "." . $arrFileUploaded->getFileExtension());
                $arrFileUploaded->setDiretorio($this->destinyDirectory . "/" . $arrFileUploaded->getName());

                if (move_uploaded_file($arrFileUploaded->getNameTemp(), $arrFileUploaded->getDiretorio())) {
                    $arrFileUploaded->setError(false);
                    $arrFileUploaded->setMessage("Sucesso: O arquivo {$nomeDoArquivo} salvo com sucesso.");
                    $filesUploaded[] = $arrFileUploaded;
                } else {
                    $arrFileUploaded->setError(true);
                    $arrFileUploaded->setMessage("Erro: O arquivo {$nomeDoArquivo} não pôde ser salvo.");
                    $filesUploaded[] = $arrFileUploaded;
                }
            }

            return $filesUploaded;
        }

        public static function deleteFile($directory)
        {
            $response["error"] = false;
            $response["msg"] = "Arquivo deletado com sucesso.";
            if (file_exists($directory)) {
                if (!unlink($directory)) {
                    $response["error"] = true;
                    $response["msg"] = "Falha ao deletar arquivo.";
                } 
            } else {
                $response["error"] = true;
                $response["msg"] = "O arquivo não existe!";
            }

            return $response;
        }
    }
?>