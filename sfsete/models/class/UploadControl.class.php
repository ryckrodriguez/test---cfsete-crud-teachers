<?php
    class UploadControl {
        private $name = "";
        private $directory = "";
        private $nameTemp = "";
        private $fileExtension = "";
        private $error = false;
        private $message = "";

        public function getName()
        {
            return $this->name;
        }

        public function setName($name)
        {
            $this->name = $name;
            return $this;
        }

        public function getDiretorio()
        {
            return $this->directory;
        }

        public function setDiretorio($directory)
        {
            $this->directory = $directory;
            return $this;
        }

        public function getNameTemp()
        {
            return $this->nameTemp;
        }

        public function setNameTemp($nameTemp)
        {
            $this->nameTemp = $nameTemp;
            return $this;
        }

        public function getFileExtension()
        {
            return $this->fileExtension;
        }

        public function setFileExtension($fileExtension)
        {
            $this->fileExtension = $fileExtension;
            return $this;
        }

        public function getError()
        {
            return $this->error;
        }

        public function setError($error)
        {
            $this->error = $error;
            return $this;
        }

        public function getMessage()
        {
            return $this->message;
        }

        public function setMessage($message)
        {
            $this->message = $message;
            return $this;
        }
    }
?>