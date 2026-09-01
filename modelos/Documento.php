<?php
/**
 * Modelo simple de Documento.
 * Representa los datos básicos utilizados por la aplicación para trabajar
 * con documentos antes de persistirlos mediante el DAO.
 */
class Documento {
    public string $titulo;
    public string $descripcion;
    public string $archivo;
    public function __construct(string $titulo, string $descripcion, string $archivo) {
        $this->titulo = $titulo;
        $this->descripcion = $descripcion;
        $this->archivo = $archivo;
    }
}
 ?>
