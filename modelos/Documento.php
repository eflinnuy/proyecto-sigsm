<?php
class Documento {
    public string $titulo;
    public string $descripcion;
    public string $archivo;
    public function __construct(string $titulo, string $descripcion, string $archivo) {
        $this->titulo = $titulo; $this->descripcion = $descripcion; $this->archivo = $archivo;
    }
}
?>
